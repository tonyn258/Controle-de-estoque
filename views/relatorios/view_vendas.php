<?php
require_once '../../App/auth.php';

/**
 * Classe VendasModel
 * Responsável pela conexão com o banco de dados e consulta das vendas.
 */
class VendasModel {
    private $pdo;

    public function __construct() {
        // Configurações de conexão
        $host = 'localhost';
        $dbname = 'controlestoque';
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public function getVendasUsuario($idUsuario, $mes = null, $ano = null) {
        try {
            // Consulta unindo vendas, cliente e anuncio (para nome do produto)
            $sql = "SELECT 
                        v.idVendas, 
                        v.Itensquant, 
                        v.valor, 
                        v.DataVenda, 
                        v.CodRastreioV, 
                        v.Vd_Tax, 
                        v.Venda_Total, 
                        v.Diferenca_Quantidade,
                        c.NomeCliente, 
                        c.CepCliente, 
                        c.cpfCliente,
                        a.NomeProduto
                    FROM vendas v
                    LEFT JOIN cliente c ON v.cliente_idCliente = c.idCliente
                    LEFT JOIN anuncio a ON v.anuncio_id = a.idAnuncio
                    WHERE (v.usuario_id = :idUsuario OR v.usuario_id IS NULL OR v.usuario_id = 0)";

            if (!empty($mes) && !empty($ano)) {
                $sql .= " AND MONTH(v.DataVenda) = :mes AND YEAR(v.DataVenda) = :ano";
            }

            $sql .= " ORDER BY v.idVendas DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            if (!empty($mes) && !empty($ano)) {
                $stmt->bindValue(':mes', $mes, PDO::PARAM_INT);
                $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
            }
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

// Inicialização e Busca de Dados
$vendasModel = new VendasModel();
$idUsuario = $_SESSION['idUsuario'] ?? 0;

// Filtros de Mês e Ano
$filtroMes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$filtroAno = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

// Variáveis para exibição no formulário (Padrão: Atual se não houver filtro)
$uiMes = $filtroMes;
$uiAno = $filtroAno;

$dadosBrutos = $vendasModel->getVendasUsuario($idUsuario, $filtroMes, $filtroAno);

// Agrupamento de Vendas por Código de Rastreio
$vendasAgrupadas = [];

foreach ($dadosBrutos as $row) {
    $codRastreio = $row['CodRastreioV'];

    if (!isset($vendasAgrupadas[$codRastreio])) {
        $vendasAgrupadas[$codRastreio] = [
            'CodRastreioV' => $codRastreio,
            'NomeCliente' => $row['NomeCliente'] ?? 'Cliente Não Identificado',
            'CepCliente' => $row['CepCliente'] ?? 'N/A',
            'CpfCliente' => $row['cpfCliente'] ?? '',
            'Venda_Total' => 0,
            'Diferenca_Quantidade' => 0,
            'Produtos' => []
        ];
    }

    // Acumula totais
    $vendasAgrupadas[$codRastreio]['Venda_Total'] += $row['Venda_Total'];
    $vendasAgrupadas[$codRastreio]['Diferenca_Quantidade'] += $row['Diferenca_Quantidade'];

    // Adiciona produto à lista
    $vendasAgrupadas[$codRastreio]['Produtos'][] = [
        'NomeProduto' => $row['NomeProduto'] ?? 'Produto Indisponível',
        'Itensquant' => $row['Itensquant'],
        'Vd_Tax' => $row['Vd_Tax']
    ];
}

// Função auxiliar para obter cidade e UF via ViaCEP com cache
function getEnderecoByCep($cep) {
    static $cache = [];
    $cepClean = preg_replace('/[^0-9]/', '', $cep);
    
    if (empty($cepClean)) {
        return ['localidade' => 'Cidade não informada', 'uf' => 'UF não informada'];
    }

    if (isset($cache[$cepClean])) {
        return $cache[$cepClean];
    }

    // Timeout de 2 segundos para evitar travamento
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $json = @file_get_contents("https://viacep.com.br/ws/{$cepClean}/json/", false, $ctx);
    $data = json_decode($json, true);

    $result = ($data && !isset($data['erro'])) 
        ? ['localidade' => $data['localidade'], 'uf' => $data['uf']] 
        : ['localidade' => 'Cidade não informada', 'uf' => 'UF não informada'];

    $cache[$cepClean] = $result;
    return $result;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #333;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: var(--secondary-color);
            margin: 0;
            font-size: 1.8rem;
        }

        /* Layout do Cabeçalho */
        .header-row-1 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: bold;
            margin-left: 20px;
            font-size: 1rem;
        }

        .header-row-2 {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
        }

        /* Estilos do Filtro */
        .filter-form {
            display: flex;
            gap: 10px;
            margin: 0; /* Remove margem padrão */
            flex-shrink: 0;
        }

        .list-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .list-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid #eee;
        }

        .item-header {
            padding: 15px 20px;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .item-text {
            font-size: 1rem;
            color: var(--secondary-color);
            font-weight: 600;
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-open { background-color: var(--primary-color); color: white; }
        .btn-open:hover { background-color: #2980b9; }

        .btn-copy { background-color: #27ae60; color: white; }
        .btn-copy:hover { background-color: #219150; }

        .item-details {
            background-color: #f8f9fa;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease;
            border-top: 1px solid #eee;
        }

        .list-item.active .item-details {
            max-height: 1000px;
            padding: 20px;
            overflow-y: auto;
        }

        .product-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item strong {
            display: block;
            color: #555;
        }

        .product-item span {
            font-size: 0.9em;
            color: #888;
        }

        @media (max-width: 768px) {
            .item-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .item-text { white-space: normal; }
            .item-actions { width: 100%; justify-content: flex-end; }
        }
        
        .filter-select, .filter-btn {
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .filter-btn { background-color: var(--primary-color); color: white; border: none; cursor: pointer; }
        .filter-btn:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <!-- Linha 1: Título e Botões -->
        <div class="header-row-1">
            <h1>Relatório de Vendas</h1>
            <div class="nav-links">
                <a href="../">🏠 Home</a>
                <a href="excluir_vendas.php" style="color: #e74c3c;">🗑️ Excluir Venda</a>
                <a href="index.php">📊 Voltar</a>
            </div>
        </div>

        <!-- Linha 2: Pesquisa e Filtros -->
        <div class="header-row-2">
            <form method="GET" class="filter-form">
                <select name="mes" class="filter-select">
                    <?php 
                    $meses = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
                    foreach($meses as $num => $nome): ?>
                        <option value="<?php echo $num; ?>" <?php echo $num == $uiMes ? 'selected' : ''; ?>><?php echo $nome; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="ano" class="filter-select">
                    <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $uiAno ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                
                <button type="submit" class="filter-btn">Filtrar</button>
            </form>
        </div>
    </header>

    <div class="list-container" id="vendasList">
        <?php if (empty($vendasAgrupadas)): ?>
            <p style="text-align: center; width: 100%; padding: 40px; color: #666;">Nenhuma venda encontrada para o período selecionado.</p>
        <?php else: ?>
            <?php $iter = 0; ?>
            <?php foreach ($vendasAgrupadas as $venda): ?>
                <?php 
                    $iter++;
                    $detailsId = 'details-' . $iter; // ID único para vincular botão e detalhes
                    $end = getEnderecoByCep($venda['CepCliente']);
                    $titulo = $venda['CodRastreioV'] . ' - ' . 
                              $venda['NomeCliente'] . ' - ' . 
                              $end['localidade'] . ' - ' . 
                              $end['uf'] . ' - ' . 
                              'R$' . number_format($venda['Venda_Total'], 2, ',', '.') . ' - ' . 
                              'R$' . number_format($venda['Diferenca_Quantidade'], 2, ',', '.');
                ?>
                <div class="list-item">
                    <div class="item-header">
                        <span class="item-text"><?php echo htmlspecialchars($titulo); ?></span>
                        <div class="item-actions">
                            <button class="btn-action btn-open" data-target="<?php echo $detailsId; ?>" onclick="toggleDetails(this)">Abrir detalhes</button>
                            <button class="btn-action btn-copy" onclick="copyText('<?php echo htmlspecialchars($titulo); ?>', this)">Copiar</button>
                        </div>
                    </div>
                    <div class="item-details" id="<?php echo $detailsId; ?>">
                        <?php foreach ($venda['Produtos'] as $produto): ?>
                            <?php 
                                // Repete o produto conforme a quantidade de itens
                                $qtd = (int)$produto['Itensquant'];
                                for ($i = 0; $i < $qtd; $i++): 
                            ?>
                                <div class="product-item">
                                    <strong><?php echo htmlspecialchars($produto['NomeProduto']); ?></strong>
                                    <span>Valor Unit.: R$ <?php echo number_format($produto['Vd_Tax'], 2, ',', '.'); ?></span>
                                </div>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // Função de Toggle (Abrir/Fechar Detalhes)
    function toggleDetails(btn) {
        const targetId = btn.getAttribute('data-target');
        const details = document.getElementById(targetId);
        const listItem = details.closest('.list-item');
        
        listItem.classList.toggle('active');
        btn.textContent = listItem.classList.contains('active') ? 'Fechar detalhes' : 'Abrir detalhes';
    }

    // Função de Copiar
    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.textContent;
            btn.textContent = 'Copiado!';
            btn.style.backgroundColor = '#2ecc71';
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.backgroundColor = '';
            }, 2000);
        }).catch(err => {
            console.error('Erro ao copiar', err);
            alert('Erro ao copiar texto.');
        });
    }
</script>

</body>
</html>
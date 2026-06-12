<?php
require_once '../../App/auth.php';

class VendasExclusaoModel {
    private $pdo;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'controlestoque';
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function getVendas($idUsuario, $mes, $ano) {
        $sql = "SELECT v.idVendas, v.CodRastreioV, c.NomeCliente, c.CepCliente 
                FROM vendas v 
                LEFT JOIN cliente c ON v.cliente_idCliente = c.idCliente 
                WHERE (v.usuario_id = :idUsuario OR v.usuario_id IS NULL OR v.usuario_id = 0)
                AND MONTH(v.DataVenda) = :mes
                AND YEAR(v.DataVenda) = :ano
                ORDER BY v.idVendas DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':mes', $mes, PDO::PARAM_INT);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteVenda($idVenda, $idUsuario) {
        // Verifica se a venda pertence ao usuário ou é pública antes de excluir
        $sql = "DELETE FROM vendas 
                WHERE idVendas = :idVenda 
                AND (usuario_id = :idUsuario OR usuario_id IS NULL OR usuario_id = 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idVenda', $idVenda, PDO::PARAM_INT);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

$model = new VendasExclusaoModel();
$idUsuario = $_SESSION['idUsuario'] ?? 0;
$msg = '';

// Filtros de Mês e Ano (Padrão: Atual)
$filtroMes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$filtroAno = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

// Processamento da Exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if ($model->deleteVenda($_POST['delete_id'], $idUsuario)) {
        $msg = '<div class="alert success">Venda excluída com sucesso!</div>';
    } else {
        $msg = '<div class="alert error">Erro ao excluir: Venda não encontrada ou permissão negada.</div>';
    }
}

$vendas = $model->getVendas($idUsuario, $filtroMes, $filtroAno);

// Função auxiliar para CEP (Mesma lógica do relatório)
function getEnderecoByCep($cep) {
    static $cache = [];
    $cepClean = preg_replace('/[^0-9]/', '', $cep);
    
    if (empty($cepClean)) return ['localidade' => 'Cidade não informada', 'uf' => 'UF não informada'];
    if (isset($cache[$cepClean])) return $cache[$cepClean];

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
    <title>Excluir Vendas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #ddd; padding-bottom: 15px; }
        h1 { color: #c0392b; margin: 0; font-size: 1.8rem; }
        .nav-links a { text-decoration: none; color: #3498db; font-weight: bold; margin-left: 20px; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .list-container { display: flex; flex-direction: column; gap: 10px; }
        .list-item { 
            background: white; 
            padding: 15px 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border: 1px solid #eee;
        }
        
        .item-text { font-size: 1rem; font-weight: 500; color: #2c3e50; }
        
        .btn-delete { 
            background-color: #e74c3c; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn-delete:hover { background-color: #c0392b; }

        @media (max-width: 600px) {
            .list-item { flex-direction: column; gap: 10px; text-align: center; }
            .header { flex-direction: column; gap: 10px; }
            .filter-form { flex-direction: column; width: 100%; }
        }

        .filter-form { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
        .filter-select { padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; font-size: 1rem; }
        .filter-btn { background-color: #3498db; color: white; border: none; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; }
        .filter-btn:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Excluir Vendas</h1>
        <div class="nav-links">
            <a href="../">🏠 Home</a>
            <a href="view_vendas.php">📋 Relatório</a>
        </div>
    </div>

    <!-- Filtro de Período -->
    <form method="GET" class="filter-form">
        <select name="mes" class="filter-select">
            <?php 
            $meses = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
            foreach($meses as $num => $nome): ?>
                <option value="<?php echo $num; ?>" <?php echo $num == $filtroMes ? 'selected' : ''; ?>><?php echo $nome; ?></option>
            <?php endforeach; ?>
        </select>
        
        <select name="ano" class="filter-select">
            <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                <option value="<?php echo $i; ?>" <?php echo $i == $filtroAno ? 'selected' : ''; ?>><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        
        <button type="submit" class="filter-btn">Filtrar</button>
    </form>

    <?php echo $msg; ?>

    <div class="list-container">
        <?php if (empty($vendas)): ?>
            <p style="text-align: center; color: #777;">Nenhuma venda encontrada.</p>
        <?php else: ?>
            <?php foreach ($vendas as $venda): ?>
                <?php 
                    $end = getEnderecoByCep($venda['CepCliente']);
                    
                    // Formato Obrigatório: idVendas - CodRastreioV - Nome do Cliente - Cidade - UF
                    $textoExibicao = $venda['idVendas'] . ' - ' . 
                                     ($venda['CodRastreioV'] ?: 'S/R') . ' - ' . 
                                     ($venda['NomeCliente'] ?: 'Cliente N/I') . ' - ' . 
                                     $end['localidade'] . ' - ' . 
                                     $end['uf'];
                ?>
                <div class="list-item">
                    <span class="item-text">
                        <?php echo htmlspecialchars($textoExibicao); ?>
                    </span>
                    
                    <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir a venda ID: <?php echo $venda['idVendas']; ?>? Esta ação não pode ser desfeita.');">
                        <input type="hidden" name="delete_id" value="<?php echo $venda['idVendas']; ?>">
                        <button type="submit" class="btn-delete">Excluir</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
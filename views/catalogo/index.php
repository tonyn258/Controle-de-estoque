<?php
// Configurações do Banco de Dados
// Ajuste as credenciais conforme seu ambiente local
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente da classe Connect do sistema
if (file_exists('../../App/Models/connect.php')) {
    require_once '../../App/Models/connect.php';
    if (class_exists('Connect')) {
        $legacy = new Connect();
        if (isset($legacy->SQL) && $legacy->SQL instanceof mysqli) {
            $res = $legacy->SQL->query("SELECT DATABASE()");
            if ($res && $row = $res->fetch_row()) {
                $db_name = $row[0];
            }
        }
    }
}

try {
    // Conexão PDO obrigatória conforme solicitado
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em produção, evite exibir detalhes do erro para o usuário
    die("Erro de conexão: " . $e->getMessage() . "<br>Verifique a variável <b>\$db_name</b> na linha 5.");
}

// Lógica de Filtros (Estoque)
$filtroEstoque = isset($_GET['estoque']) ? $_GET['estoque'] : 'com_estoque';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid'; // 'grid' (página) ou 'list' (lista)

// Construção da Query SQL
// Regra: Listar apenas anúncios onde Ativo = 1 e public = 1
if ($viewMode === 'list') {
    // Visualização em Lista: Individual
    $sql = "SELECT 
                a.idAnuncio, 
                a.skuAnuncio, 
                a.model, 
                a.NomeProduto, 
                a.ValorVenda, 
                a.descricao, 
                a.QuantItens, 
                a.Ativo,
                a.public,
                a.QuantItensVend,
                c.NomeCategoria,
                (
                    SELECT caminhoImagem 
                    FROM anuncio_imagem ai 
                    WHERE ai.anuncio_id = a.idAnuncio 
                    ORDER BY ai.ordem ASC 
                    LIMIT 1
                ) as imagem
            FROM anuncio a 
            LEFT JOIN categoria_produto c ON a.idCategoria = c.idCategoria
            WHERE 1=1";

    // Filtro de Estoque (WHERE)
    if ($filtroEstoque === 'com_estoque') {
        $sql .= " AND ((a.QuantItens - COALESCE(a.QuantItensVend, 0)) > 0)";
    } elseif ($filtroEstoque === 'sem_estoque') {
        $sql .= " AND ((a.QuantItens - COALESCE(a.QuantItensVend, 0)) <= 0)";
    }
    
    $sql .= " ORDER BY a.NomeProduto ASC";

} else {
    // Visualização em Grade: Agrupado por SKU
    $sql = "SELECT 
                (
                    SELECT a_sub.idAnuncio 
                    FROM anuncio a_sub 
                    WHERE a_sub.skuAnuncio = a.skuAnuncio 
                    ORDER BY (a_sub.QuantItens - COALESCE(a_sub.QuantItensVend, 0)) DESC, a_sub.idAnuncio DESC 
                    LIMIT 1
                ) as idAnuncio,
                a.skuAnuncio, 
                MAX(a.model) as model, 
                MAX(a.NomeProduto) as NomeProduto, 
                MAX(a.ValorVenda) as ValorVenda, 
                MAX(a.descricao) as descricao, 
                SUM(a.QuantItens) as QuantItens, 
                MAX(a.Ativo) as Ativo, 
                MAX(a.public) as public,
                SUM(COALESCE(a.QuantItensVend, 0)) as QuantItensVend,
                MAX(c.NomeCategoria) as NomeCategoria,
                (
                    SELECT ai.caminhoImagem 
                    FROM anuncio_imagem ai 
                    INNER JOIN anuncio a2 ON ai.anuncio_id = a2.idAnuncio
                    WHERE a2.skuAnuncio = a.skuAnuncio
                    ORDER BY (a2.QuantItens - COALESCE(a2.QuantItensVend, 0)) DESC, a2.idAnuncio DESC, ai.ordem ASC 
                    LIMIT 1
                ) as imagem
            FROM anuncio a 
            LEFT JOIN categoria_produto c ON a.idCategoria = c.idCategoria
            WHERE 1=1
            GROUP BY a.skuAnuncio";

    // Filtro de Estoque (HAVING)
    if ($filtroEstoque === 'com_estoque') {
        $sql .= " HAVING (SUM(a.QuantItens) - SUM(COALESCE(a.QuantItensVend, 0))) > 0";
    } elseif ($filtroEstoque === 'sem_estoque') {
        $sql .= " HAVING (SUM(a.QuantItens) - SUM(COALESCE(a.QuantItensVend, 0))) <= 0";
    }

    $sql .= " ORDER BY NomeProduto ASC";
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $anuncios = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar anúncios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Anúncios</title>
    <style>
        /* Reset e Variáveis CSS */
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
            --radius: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f6f9; color: #333; line-height: 1.5; }
        
        /* Layout Principal */
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        /* Cabeçalho e Filtros */
        .catalog-header { text-align: center; margin-bottom: 30px; }
        .catalog-header h1 { font-size: 2rem; margin-bottom: 10px; color: var(--dark); }
        
        .filters { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
        .btn { padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; border: 1px solid #ddd; background: white; color: var(--secondary); }
        .btn:hover { background-color: #e2e6ea; }
        .btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }

        /* Grid de Produtos (Responsivo) */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        /* Card do Produto */
        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }

        /* Imagem */
        .card-img-container {
            height: 200px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #eee;
            padding: 10px;
        }
        .card-img { width: 100%; height: 100%; object-fit: contain; }
        .no-img { color: #999; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; }
        
        /* Corpo do Card */
        .card-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        
        .card-meta { font-size: 0.8rem; color: #888; margin-bottom: 5px; display: flex; justify-content: space-between; }
        
        .card-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: var(--dark); line-height: 1.3; }
        
        .card-desc { font-size: 0.9rem; color: #666; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1; }
        
        .card-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f1f1; padding-top: 12px; }
        
        .price { font-size: 1.25rem; font-weight: bold; color: var(--primary); }
        
        .badge { font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }

        /* Tabela (List View) */
        .table-container { background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; color: var(--dark); }
        tr:hover { background-color: #f1f1f1; }
        .btn-sm { padding: 4px 8px; font-size: 0.8rem; border-radius: 4px; }
        .btn-edit { background-color: var(--primary); color: white; border: none; cursor: pointer; text-decoration: none; }
        .btn-edit:hover { background-color: #0056b3; }
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        /* Empty State */
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 40px; color: #777; background: white; border-radius: var(--radius); }
        
        @media (max-width: 576px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="btn" style="margin-bottom: 20px; display: inline-block;">🏠 Voltar ao Início</a>
    <header class="catalog-header">
        <h1>Catálogo de Produtos</h1>
        <div class="filters">
            <a href="?estoque=todos" class="btn <?= $filtroEstoque == 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="?estoque=com_estoque" class="btn <?= $filtroEstoque == 'com_estoque' ? 'active' : '' ?>">Com Estoque</a>
            <a href="?estoque=sem_estoque" class="btn <?= $filtroEstoque == 'sem_estoque' ? 'active' : '' ?>">Sem Estoque</a>
        </div>
        
        <!-- Controle de Visualização -->
        <div class="view-controls" style="margin-top: 15px;">
            <a href="?estoque=<?= $filtroEstoque ?>&view=grid" class="btn <?= $viewMode == 'grid' ? 'active' : '' ?>">📅 Grade</a>
            <a href="?estoque=<?= $filtroEstoque ?>&view=list" class="btn <?= $viewMode == 'list' ? 'active' : '' ?>">☰ Lista</a>
            <a href="add.php" class="btn" style="background-color: #28a745; color: white; border-color: #28a745; margin-left: 10px;">+ Novo Anúncio</a>
        </div>
    </header>

    <?php if (empty($anuncios)): ?>
        <div class="empty-state">
            <p>Nenhum anúncio encontrado com os critérios selecionados.</p>
        </div>
    <?php else: ?>

        <?php if ($viewMode === 'list'): ?>
            <!-- Visualização em LISTA -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Valor Venda</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($anuncios as $item): ?>
                            <?php 
                                $estoqueAtual = $item['QuantItens'] - ($item['QuantItensVend'] ?? 0);
                                $ativo = $item['Ativo'] == 1;
                                $public = $item['public'] == 1;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['skuAnuncio']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($item['NomeProduto']) ?></strong>
                                    <?php if($item['model']): ?><br><small class="text-muted"><?= htmlspecialchars($item['model']) ?></small><?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['NomeCategoria'] ?? 'N/A') ?></td>
                                <td>R$ <?= number_format($item['ValorVenda'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $estoqueAtual > 0 ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $estoqueAtual ?>
                                    </span>
                                </td>
                                <td>
                                    <div title="Ativo no Sistema">
                                        <span class="status-dot" style="background: <?= $ativo ? '#28a745' : '#dc3545' ?>"></span> <?= $ativo ? 'Ativo' : 'Inativo' ?>
                                    </div>
                                    <div title="Visível no Site">
                                        <span class="status-dot" style="background: <?= $public ? '#007bff' : '#6c757d' ?>"></span> <?= $public ? 'Público' : 'Oculto' ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $item['idAnuncio'] ?>" class="btn btn-sm btn-edit">✏️ Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- Visualização em GRADE (Página) -->
            <div class="grid">
                <?php foreach ($anuncios as $item): ?>
                    <?php 
                        $estoqueAtual = $item['QuantItens'] - ($item['QuantItensVend'] ?? 0);
                        $estoqueDisponivel = $estoqueAtual > 0;
                        $statusTexto = $estoqueDisponivel ? 'Estoque: ' . $estoqueAtual : 'Esgotado';
                        $statusClass = $estoqueDisponivel ? 'badge-success' : 'badge-danger';
                        // Caminho da imagem relativo à pasta views/catalogo/ (sobe 2 niveis para raiz)
                        $imagemUrl = !empty($item['imagem']) ? '../../' . $item['imagem'] : null;
                    ?>
                    <a href="detalhe.php?id=<?= $item['idAnuncio'] ?>" class="card">
                        <div class="card-img-container">
                            <?php if ($imagemUrl): ?>
                                <img src="<?= htmlspecialchars($imagemUrl) ?>" alt="<?= htmlspecialchars($item['NomeProduto']) ?>" class="card-img">
                            <?php else: ?>
                                <div class="no-img"><span>Sem imagem</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="card-meta">
                                <span>SKU: <?= htmlspecialchars($item['skuAnuncio']) ?></span>
                                <span><?= htmlspecialchars($item['model']) ?></span>
                            </div>
                            <h3 class="card-title"><?= htmlspecialchars($item['NomeProduto']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($item['descricao']) ?></p>
                            <div class="card-footer">
                                <span class="price">R$ <?= number_format($item['ValorVenda'], 2, ',', '.') ?></span>
                                <span class="badge <?= $statusClass ?>"><?= $statusTexto ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
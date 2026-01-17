<?php
require_once '../../App/auth.php';

// Configurações do Banco de Dados (PDO)
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

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
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Filtros
$filtroEstoque = $_GET['estoque'] ?? 'todos';
$filtroCategoria = $_GET['categoria'] ?? '';

// Query Categorias
$stmtCat = $pdo->query("SELECT idCategoria, NomeCategoria FROM categoria_produto WHERE statusCategoria = 1 ORDER BY NomeCategoria ASC");
$categorias = $stmtCat->fetchAll();

// Query Anuncios (Catalogo)
$sql = "SELECT 
            a.idAnuncio, 
            a.skuAnuncio, 
            a.model, 
            a.NomeProduto, 
            a.ValorVenda, 
            a.QuantItens, 
            a.QuantItensVend,
            a.Ativo,
            a.public,
            c.NomeCategoria
        FROM anuncio a 
        LEFT JOIN categoria_produto c ON a.idCategoria = c.idCategoria
        WHERE 1=1";

if ($filtroEstoque === 'com_estoque') {
    $sql .= " AND ((a.QuantItens - COALESCE(a.QuantItensVend, 0)) > 0)";
} elseif ($filtroEstoque === 'sem_estoque') {
    $sql .= " AND ((a.QuantItens - COALESCE(a.QuantItensVend, 0)) <= 0)";
}

if (!empty($filtroCategoria)) {
    $sql .= " AND a.idCategoria = :cat";
}

$sql .= " ORDER BY a.NomeProduto ASC";

$stmt = $pdo->prepare($sql);
if (!empty($filtroCategoria)) {
    $stmt->bindValue(':cat', $filtroCategoria);
}
$stmt->execute();
$anuncios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
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
        
        .badge { font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="btn" style="margin-bottom: 20px; display: inline-block;">🏠 Voltar ao Início</a>
    
    <header class="catalog-header">
        <h1>Lista de Produtos</h1>
        <div class="filters">
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                  <label>Estoque: </label>
                  <select name="estoque" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;" onchange="this.form.submit()">
                      <option value="todos" <?= $filtroEstoque == 'todos' ? 'selected' : '' ?>>Todos</option>
                      <option value="com_estoque" <?= $filtroEstoque == 'com_estoque' ? 'selected' : '' ?>>Com Estoque</option>
                      <option value="sem_estoque" <?= $filtroEstoque == 'sem_estoque' ? 'selected' : '' ?>>Sem Estoque</option>
                  </select>
              
                  <label>Categoria: </label>
                  <select name="categoria" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;" onchange="this.form.submit()">
                      <option value="">Todas</option>
                      <?php foreach($categorias as $cat): ?>
                          <option value="<?= $cat['idCategoria'] ?>" <?= $filtroCategoria == $cat['idCategoria'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($cat['NomeCategoria']) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              
              <a href="index.php" class="btn" style="padding: 5px 10px; font-size: 0.9rem;">Limpar</a>
            </form>
        </div>
        <div style="margin-top: 15px;">
            <a href="../catalogo/add.php" class="btn" style="background-color: #28a745; color: white; border-color: #28a745;">+ Novo Anúncio</a>
        </div>
    </header>

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
              <th class="text-center">Ações</th>
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
                    <td class="text-center">
                        <a href="../catalogo/edit.php?id=<?= $item['idAnuncio'] ?>" class="btn btn-sm btn-edit" title="Editar">✏️</a>
                        <a href="../catalogo/detalhe.php?id=<?= $item['idAnuncio'] ?>" class="btn btn-sm" title="Ver" target="_blank">👁️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
</div>
</body>
</html>

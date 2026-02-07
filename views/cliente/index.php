<?php
require_once '../../App/auth.php';

// Configurações do Banco de Dados
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente
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

// Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome_asc';

// Query Principal
$sql = "SELECT * FROM cliente WHERE 1=1";
$params = [];

if ($busca) {
    $sql .= " AND (idCliente LIKE :busca OR NomeCliente LIKE :busca)";
    $params[':busca'] = "%$busca%";
}

switch ($ordem) {
    case 'id_asc':
        $sql .= " ORDER BY idCliente ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY idCliente DESC";
        break;
    case 'nome_desc':
        $sql .= " ORDER BY NomeCliente DESC";
        break;
    case 'nome_asc':
    default:
        $sql .= " ORDER BY NomeCliente ASC";
        break;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar clientes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes</title>
    <style>
        /* Reset e Variáveis CSS (Baseado no Catálogo) */
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
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .catalog-header { text-align: center; margin-bottom: 20px; }
        .catalog-header h1 { font-size: 2rem; margin-bottom: 10px; color: var(--dark); }
        
        /* Barra de Controles Unificada */
        .controls-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 15px 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-form { display: flex; flex-grow: 1; max-width: 600px; align-items: center; }
        .search-wrapper { position: relative; flex-grow: 1; width: 100%; }
        .search-input {
            width: 100%;
            padding: 8px 40px 8px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .search-input:focus { border-color: var(--primary); }
        .search-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #777;
        }

        .btn-new {
            background-color: #28a745; color: white; padding: 10px 20px; border-radius: 25px;
            text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;
            white-space: nowrap; box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
        }
        .btn-new:hover { background-color: #218838; transform: translateY(-1px); }
        
        .btn-back { display: inline-block; margin-bottom: 20px; color: var(--secondary); text-decoration: none; font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        .table-container { background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; color: var(--dark); }
        tr:hover { background-color: #f1f1f1; }
        
        .action-emoji { font-size: 1.1rem; text-decoration: none; cursor: pointer; transition: transform 0.2s; border: none; background: transparent; line-height: 1; margin-right: 10px; }
        .action-emoji:hover { transform: scale(1.2); }

        .empty-state { text-align: center; padding: 40px; color: #777; background: white; border-radius: var(--radius); }
        
        @media (max-width: 576px) {
            .controls-bar { flex-direction: column; align-items: stretch; gap: 15px; }
            .search-form { max-width: 100%; }
            .btn-new { text-align: center; }
        }

        /* Excel-like Headers */
        .th-content { display: flex; align-items: center; justify-content: space-between; position: relative; }
        .th-link { text-decoration: none; color: inherit; display: flex; align-items: center; gap: 5px; flex-grow: 1; cursor: pointer; }
        .th-link:hover { color: var(--primary); }
        .sort-indicator { font-size: 0.8em; }
        
        .filter-trigger { 
            cursor: pointer; padding: 2px 5px; border-radius: 4px; opacity: 0.3; transition: opacity 0.2s; font-size: 0.8em;
        }
        .filter-trigger:hover, .th-content:hover .filter-trigger { opacity: 1; background: #e9ecef; }

        .excel-menu {
            position: absolute; top: 100%; left: 0; background: white; border: 1px solid #ccc; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.15); border-radius: 4px; z-index: 100; 
            min-width: 180px; padding: 5px 0; display: none; font-weight: normal;
        }
        .excel-menu.show { display: block; }
        .excel-menu-item { 
            padding: 8px 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #333;
        }
        .excel-menu-item:hover { background-color: #f1f1f1; }
        .excel-divider { height: 1px; background: #eee; margin: 5px 0; }
        .excel-search { padding: 8px 10px; }
        .excel-search input { 
            width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="btn-back">🏠 Voltar ao Início</a>
    <header class="catalog-header">
        <h1>Gestão de Clientes</h1>
    </header>

    <div class="controls-bar">
        <form action="" method="GET" class="search-form">
            <div class="search-wrapper">
                <input type="text" name="busca" placeholder="Buscar por Nome ou ID" value="<?= htmlspecialchars($busca) ?>" class="search-input">
                <button type="submit" class="search-btn">🔍</button>
            </div>
        </form>
        <a href="addcliente.php" class="btn-new">+ Novo Cliente</a>
    </div>

    <?php if (empty($clientes)): ?>
        <div class="empty-state">
            <p>Nenhum cliente encontrado.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table id="clientesTable">
                <thead>
                    <tr>
                        <th>
                            <div class="th-content">
                                <a href="?ordem=<?= $ordem == 'id_asc' ? 'id_desc' : 'id_asc' ?>&busca=<?= urlencode($busca) ?>" class="th-link">
                                    ID <span class="sort-indicator"><?= $ordem == 'id_asc' ? '🔼' : ($ordem == 'id_desc' ? '🔽' : '') ?></span>
                                </a>
                                <span class="filter-trigger" onclick="toggleMenu('menu-id')">▼</span>
                                <div id="menu-id" class="excel-menu">
                                    <div class="excel-menu-item" onclick="window.location='?ordem=id_asc&busca=<?= urlencode($busca) ?>'">🔼 Crescente</div>
                                    <div class="excel-menu-item" onclick="window.location='?ordem=id_desc&busca=<?= urlencode($busca) ?>'">🔽 Decrescente</div>
                                    <div class="excel-divider"></div>
                                    <div class="excel-search">
                                        <input type="text" placeholder="Filtrar ID..." onkeyup="filterTable(0, this.value)">
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <a href="?ordem=<?= $ordem == 'nome_asc' ? 'nome_desc' : 'nome_asc' ?>&busca=<?= urlencode($busca) ?>" class="th-link">
                                    Nome <span class="sort-indicator"><?= $ordem == 'nome_asc' ? '🔼' : ($ordem == 'nome_desc' ? '🔽' : '') ?></span>
                                </a>
                                <span class="filter-trigger" onclick="toggleMenu('menu-nome')">▼</span>
                                <div id="menu-nome" class="excel-menu">
                                    <div class="excel-menu-item" onclick="window.location='?ordem=nome_asc&busca=<?= urlencode($busca) ?>'">🔼 A a Z</div>
                                    <div class="excel-menu-item" onclick="window.location='?ordem=nome_desc&busca=<?= urlencode($busca) ?>'">🔽 Z a A</div>
                                    <div class="excel-divider"></div>
                                    <div class="excel-search">
                                        <input type="text" placeholder="Filtrar Nome..." onkeyup="filterTable(1, this.value)">
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>CPF</th>
                        <th>CEP</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['idCliente']) ?></td>
                            <td><strong><?= htmlspecialchars($cliente['NomeCliente']) ?></strong></td>
                            <td><?= htmlspecialchars($cliente['cpfCliente'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($cliente['CepCliente'] ?? '-') ?></td>
                            <td>
                                <a href="editcliente.php?id=<?= $cliente['idCliente'] ?>" class="action-emoji" title="Editar">✏️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    // Toggle Dropdown Menu
    function toggleMenu(id) {
        // Close all others
        document.querySelectorAll('.excel-menu').forEach(menu => {
            if (menu.id !== id) menu.classList.remove('show');
        });
        document.getElementById(id).classList.toggle('show');
    }

    // Close menu when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.filter-trigger') && !event.target.closest('.excel-menu')) {
            document.querySelectorAll('.excel-menu').forEach(menu => menu.classList.remove('show'));
        }
    }

    // Frontend Filter
    function filterTable(colIndex, value) {
        const filter = value.toUpperCase();
        const rows = document.querySelector("#clientesTable tbody").rows;
        for (let i = 0; i < rows.length; i++) {
            const cell = rows[i].cells[colIndex];
            if (cell) {
                const txtValue = cell.textContent || cell.innerText;
                rows[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    }
</script>

</body>
</html>
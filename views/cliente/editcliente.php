<?php
require_once __DIR__ . '/../../App/auth.php';

// Verificação de Permissão
if (isset($perm) && $perm != 1) {
    echo "Você não tem permissão!";
    exit();
}

// Configuração de Conexão PDO (Padronizado para garantir funcionamento independente)
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente se disponível
if (file_exists(__DIR__ . '/../../App/Models/connect.php')) {
    require_once __DIR__ . '/../../App/Models/connect.php';
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

$id = $_GET['id'] ?? null;
$row = null;
$msg = null;
$msgType = null;

// Processar Atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload']) && $_POST['upload'] == 'Atualizar') {
    $idUser = $_POST['iduser'] ?? null;
    $nome = $_POST['NomeCliente'] ?? '';
    $cpf = $_POST['cpfCliente'] ?? '';
    $cep = $_POST['CepCliente'] ?? '';

    if ($idUser && $nome) {
        try {
            $stmt = $pdo->prepare("UPDATE cliente SET NomeCliente = ?, cpfCliente = ?, CepCliente = ? WHERE idCliente = ?");
            $stmt->execute([$nome, $cpf, $cep, $idUser]);
            $msg = "Cliente atualizado com sucesso!";
            $msgType = "success";
            // Atualiza o ID para garantir que os dados recarregados sejam os corretos
            $id = $idUser;
        } catch (PDOException $e) {
            $msg = "Erro ao atualizar: " . $e->getMessage();
            $msgType = "danger";
        }
    } else {
        $msg = "Preencha os campos obrigatórios.";
        $msgType = "warning";
    }
}

// Buscar Dados do Cliente (Carregamento Seguro)
if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM cliente WHERE idCliente = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
    } catch (PDOException $e) {
        die("Erro ao buscar cliente: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CSS Padronizado (Baseado em addcliente.php e catalogo) */
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --radius: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; }
        
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); margin: 0; }
        
        .breadcrumb { font-size: 0.9rem; color: var(--secondary); margin-bottom: 10px; }
        .breadcrumb a { text-decoration: none; color: var(--primary); }
        
        .btn-back { text-decoration: none; color: var(--secondary); font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .btn-back:hover { color: var(--primary); }

        .alert { padding: 15px; border-radius: var(--radius); margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        input[type="text"], input[type="number"], select, textarea {
            padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; transition: border-color 0.2s; width: 100%;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; }
        
        .btn-submit {
            grid-column: 1 / -1; background-color: var(--primary); color: white; border: none; padding: 15px;
            font-size: 1.1rem; font-weight: bold; border-radius: var(--radius); cursor: pointer; margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-submit:hover { background-color: #0056b3; }

        .empty-state { text-align: center; padding: 40px; color: #777; }

        @media (max-width: 768px) {
            form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="breadcrumb">
        <a href="../">Home</a> / <a href="index.php">Clientes</a> / Editar
    </div>

    <div class="card">
        <div class="header">
            <h1>Editar Cliente</h1>
            <a href="index.php" class="btn-back">&larr; Voltar para Lista</a>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($row): ?>
            <form action="" method="POST">
                <div class="form-group full">
                    <label for="NomeCliente">Nome Completo</label>
                    <input type="text" name="NomeCliente" id="NomeCliente" placeholder="Nome Completo" value="<?= htmlspecialchars($row['NomeCliente']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="cpfCliente">CPF</label>
                    <input type="text" name="cpfCliente" id="cpfCliente" placeholder="CPF" value="<?= htmlspecialchars($row['cpfCliente']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="CepCliente">CEP</label>
                    <input type="text" name="CepCliente" id="CepCliente" placeholder="CEP" value="<?= htmlspecialchars($row['CepCliente']) ?>" required>
                </div>

                <input type="hidden" name="iduser" value="<?= htmlspecialchars($row['idCliente']) ?>">

                <button type="submit" name="upload" class="btn-submit" value="Atualizar">Atualizar Cliente</button>
            </form>
        <?php else: ?>
            <div class="empty-state">
                <h3>Cliente não encontrado</h3>
                <p>O cliente solicitado não existe ou foi removido.</p>
                <a href="index.php" class="btn-back" style="margin-top: 15px;">Voltar</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="../../assets/js/cliente.js"></script>
</body>
</html>
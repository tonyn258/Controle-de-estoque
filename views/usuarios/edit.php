<?php
require_once '../../App/auth.php';
require_once '../../App/Models/UsuarioModel.php';

$model = new UsuarioModel();
$id = $_GET['id'] ?? 0;
$user = $model->getById($id);
$erro = '';

if (!$user) {
    header('Location: index.php?msg=Usuário não encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'Username' => $_POST['Username'],
            'Email' => $_POST['Email'],
            'Password' => $_POST['Password'], // Can be empty
            'Permissão' => $_POST['Permissão']
        ];
        
        $file = $_FILES['arquivo'] ?? null;
        
        if ($model->update($id, $data, $file)) {
            header('Location: index.php?msg=Usuário atualizado com sucesso!');
            exit;
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <style>
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
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); }
        .btn-back { text-decoration: none; color: var(--secondary); font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        input, select { padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; }
        input:focus, select:focus { border-color: var(--primary); outline: none; }

        .btn-submit { grid-column: 1 / -1; background-color: var(--primary); color: white; border: none; padding: 15px; font-size: 1.1rem; font-weight: bold; border-radius: var(--radius); cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background-color: #0056b3; }

        .preview-container { margin-top: 10px; text-align: center; }
        #preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; }
        .help-text { font-size: 0.8rem; color: #777; margin-top: 5px; }

        @media (max-width: 600px) { form { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Editar Usuário</h1>
            <a href="index.php" class="btn-back">&larr; Voltar</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Username">Nome de Usuário *</label>
                <input type="text" name="Username" id="Username" value="<?= htmlspecialchars($user['Username']) ?>" required>
            </div>

            <div class="form-group">
                <label for="Email">Email *</label>
                <input type="email" name="Email" id="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="Password">Senha</label>
                <input type="password" name="Password" id="Password" placeholder="Deixe em branco para manter a atual">
            </div>

            <div class="form-group">
                <label for="Permissão">Permissão *</label>
                <select name="Permissão" id="Permissão" required>
                    <option value="0" <?= $user['Permissão'] == 0 ? 'selected' : '' ?>>Usuário Padrão</option>
                    <option value="1" <?= $user['Permissão'] == 1 ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <div class="form-group full">
                <label for="arquivo">Foto do Usuário</label>
                <input type="file" name="arquivo" id="arquivo" accept="image/*" onchange="previewImage(this)">
                <div class="preview-container">
                    <?php 
                        $imgSrc = $user['arquivo'] ? "../../uploads/usuarios/" . $user['arquivo'] : "";
                        $display = $user['arquivo'] ? "inline-block" : "none";
                    ?>
                    <img id="preview" src="<?= $imgSrc ?>" style="display: <?= $display ?>" alt="Preview">
                </div>
                <p class="help-text">Selecione um arquivo apenas se desejar alterar a foto atual.</p>
            </div>

            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>
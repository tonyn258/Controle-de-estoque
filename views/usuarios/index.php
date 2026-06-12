<?php
require_once '../../App/auth.php';
require_once '../../App/Models/UsuarioModel.php';

$model = new UsuarioModel();
$usuarios = $model->getAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
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
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); }
        
        .btn { padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 0.9rem; display: inline-block; border: none; cursor: pointer; transition: background 0.2s; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-back { color: var(--secondary); background: transparent; }
        .btn-back:hover { color: var(--primary); }
        .btn-edit { background-color: var(--success); color: white; padding: 6px 12px; font-size: 0.8rem; }
        .btn-delete { background-color: var(--danger); color: white; padding: 6px 12px; font-size: 0.8rem; }

        /* Table Styles */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background-color: #f8f9fa; font-weight: 600; color: var(--dark); }
        tr:hover { background-color: #f1f1f1; }

        .user-img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; }
        .no-img { width: 50px; height: 50px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; color: #777; font-size: 0.8rem; }

        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge-admin { background-color: #d4edda; color: #155724; }
        .badge-user { background-color: #cce5ff; color: #004085; }

        .actions { display: flex; gap: 5px; }

        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Usuários</h1>
            <div>
                <a href="../" class="btn btn-back">🏠 Home</a>
                <a href="add.php" class="btn btn-primary">+ Novo Usuário</a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Permissão</th>
                        <th>Data Registro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($usuarios)): ?>
                        <tr><td colspan="7" style="text-align: center;">Nenhum usuário encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach($usuarios as $user): ?>
                            <tr>
                                <td>
                                    <?php if($user['arquivo']): ?>
                                        <img src="../../uploads/usuarios/<?= htmlspecialchars($user['arquivo']) ?>" class="user-img" alt="Foto">
                                    <?php else: ?>
                                        <div class="no-img">N/A</div>
                                    <?php endif; ?>
                                </td>
                                <td>#<?= $user['idUser'] ?></td>
                                <td><strong><?= htmlspecialchars($user['Username']) ?></strong></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td>
                                    <span class="badge <?= $user['Permissão'] == 1 ? 'badge-admin' : 'badge-user' ?>">
                                        <?= $user['Permissão'] == 1 ? 'Admin' : 'Usuário' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($user['DataRegistro'])) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit.php?id=<?= $user['idUser'] ?>" class="btn btn-edit">Editar</a>
                                        <form action="delete.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            <input type="hidden" name="id" value="<?= $user['idUser'] ?>">
                                            <button type="submit" class="btn btn-delete">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
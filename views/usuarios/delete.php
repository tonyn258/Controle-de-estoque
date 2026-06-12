<?php
require_once '../../App/auth.php';
require_once '../../App/Models/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $model = new UsuarioModel();
    $id = $_POST['id'];

    if ($model->delete($id)) {
        header('Location: index.php?msg=Usuário excluído com sucesso!');
    } else {
        header('Location: index.php?msg=Erro ao excluir usuário.');
    }
} else {
    header('Location: index.php');
}
exit;
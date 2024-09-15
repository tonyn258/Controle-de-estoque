<?php
require_once '../auth.php';
require_once '../Models/produto.class.php';

if (isset($_POST['upload']) == 'Cadastrar') {
    $skuProduto   = $_POST['skuProduto'];
    $model        = $_POST['model'];
    $NomeProduto  = $_POST['NomeProduto'];
    $Quantidade   = $_POST['Quantidade'];
    $Conexao      = $_POST['Conexao'];
    $Marca        = $_POST['Marca'];

    $produto = new Produto;

    if ($skuProduto != NULL && $model != NULL && $NomeProduto != NULL && $Quantidade != NULL && $Conexao != NULL && $Marca != NULL) 
    {
        if (!isset($_POST['idProduto'])) {
            $result = $produto->insertProduto($skuProduto,$model, $NomeProduto, $Quantidade, $Conexao, $Marca, $idUsuario, $perm);
        } else {
                $idProduto = $_POST['idProduto'];
                $result = $produto->UpdateProduto($idProduto,$skuProduto, $model,$NomeProduto, $Quantidade, $Conexao, $Marca, $idUsuario, $perm);
                }
        $_SESSION['alert'] = $result;
        header('Location: ../../views/produto/index.php');
    } else {
        header('Location: ../../views/produto/index.php?alert=3');
    }
} else {
    header('Location: ../../views/produto/index.php');
}
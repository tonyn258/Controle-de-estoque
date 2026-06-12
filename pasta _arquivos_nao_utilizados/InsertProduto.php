<?php
require_once '../auth.php';
require_once '../Models/produto.class.php';

if (isset($_POST['upload']) && $_POST['upload'] == 'Cadastrar') {
    $skuProduto   = $_POST['skuProduto'];
    $modelo       = $_POST['modelo'];
    $NomeProduto  = $_POST['NomeProduto'];
    $Conexao      = $_POST['Conexao'];
    $Marca        = $_POST['Marca'];

    $produto = new Produto;

    if ($skuProduto != NULL && $modelo != NULL && $NomeProduto != NULL && $Conexao != NULL && $Marca != NULL) 
    {
        if (!isset($_POST['idProduto'])) {
            $result = $produto->insertProduto($skuProduto, $modelo, $NomeProduto, $Conexao, $Marca, $idUsuario);
        } else {
                $idProduto = $_POST['idProduto'];
                $Quantidade = $_POST['Quantidade'];
                $result = $produto->UpdateProduto($idProduto, $skuProduto, $modelo, $NomeProduto, $Quantidade, $Conexao, $Marca, $perm);
                }
        $_SESSION['alert'] = $result;
        header('Location: ../../views/produto/index.php');
    } else {
        header('Location: ../../views/produto/index.php?alert=3');
    }
} else {
    header('Location: ../../views/produto/index.php');
}
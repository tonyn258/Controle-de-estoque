<?php
require_once '../auth.php';

if(isset($_GET['limpar']) && $_GET['limpar'] == "tudo"){
    unset($_SESSION['itens']);
    unset($_SESSION['vendas']);
    unset($_SESSION['taxas']);
    unset($_SESSION['fretes']);
    if (isset($_GET['target']) && $_GET['target'] == 'inicio') {
        header("Location: ../../views/");
    } else {
        header("Location: ../../views/sales/");
    }
    exit();
}

if(isset($_GET['remover']) && $_GET['remover'] == "carrinho" && isset($_GET['id'])){

	$idProduto = $_GET['id'];    

    if(isset($_SESSION['itens'][$idProduto])){
	    unset($_SESSION['itens'][$idProduto]);
        unset($_SESSION['vendas'][$idProduto]);
    }
    
    header("Location: ../../views/sales/");
    exit();   
}
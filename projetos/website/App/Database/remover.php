<?php
require_once '../auth.php';

	$idProduto = $_GET['id'];

if(isset($_GET['remover']) && $_GET['remover'] == "carrinho"){

	$idProduto = $_GET['id'];    

	unset($_SESSION['itens'][$idProduto]);

    $NomeProdutoEncoded = urlencode($NomeProduto); // Codifica o nome do produto para ser incluído na URL

// Redireciona de volta para o arquivo index.php, passando o nome do produto como parâmetro
header("Location: ../../views/vendas/?removedItem=$NomeProdutoEncoded");
exit();   

	echo "<meta http-equiv='refresh' content='0;URL=../../views/vendas/'>";
}
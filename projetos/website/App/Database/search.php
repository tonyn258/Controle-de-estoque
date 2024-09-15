<?php
require_once '../auth.php';
require_once('../Models/produto.class.php');
require_once('../Models/cliente.class.php');

$indexProduto = new Produto;
$indexCliente = new Cliente;
//Compras
if(isset($_POST["query"])!= null){

	$resp = $indexProduto->search($_POST["query"]);
			// $users = json_decode($resp , true);
			//print_r($resp);
	echo '<ul id="pesqcpf" class="list-unstyled ulcpf">';
	if($resp == 0){
		echo '<li class="licpf">Nenhum resultado encontrado!</li>';
	}else{

		foreach ($resp['data'] as $user){
			echo  '<li id="li['. $user['idProduto'] .']" class="licpf">'. 
			$user['skuProduto'].
			' - '. 
			$user['model'].
			' - '. 
			$user['NomeProduto']. 
			' - '. 
			$user['Marca'].
			'</li>';
		}
		echo '</ul>';
	}
}// Fim Compras

//Vendas
if(isset($_POST["client"])!= null){

	$resp = $indexCliente->search($_POST["client"]);

	echo '<ul id="pesqcpf" class="list-unstyled ulcpf">';
	if($resp == 0){
		echo '<li class="licpf">Nenhum resultado encontrado!</li>';
	}else{

		foreach ($resp['data2'] as $user){
			echo  '<li id="li['. $user['idCliente'] .']" class="licpf">'. 
			$user['cpfCliente'].
			' - '. 
			$user['NomeCliente'].
			
			'</li>';
		}
		echo '</ul>';
	}
}// Fim Vendas

?>
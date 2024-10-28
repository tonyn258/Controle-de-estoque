<?php
require_once '../auth.php';
require_once('../Models/produto.class.php');
require_once('../Models/cliente.class.php');

$index = new Produto;
$index = new Cliente;


//Vendas
if(isset($_POST["client"])!= null){

	$resp = $index->search($_POST["client"]);

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
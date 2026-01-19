<?php
require_once '../auth.php';
require_once('../Models/connect.php');
require_once('../Models/cliente.class.php');

$connect = new Connect;
$indexCliente = new Cliente;
//Compras
if (isset($_POST["query"]) != null) {

    $value = mysqli_real_escape_string($connect->SQL, $_POST["query"]);
    $query = "SELECT * FROM `anuncio` WHERE `skuAnuncio` LIKE '$value%' OR `model` LIKE '$value%' OR `NomeProduto` LIKE '$value%' LIMIT 5";
    $result = mysqli_query($connect->SQL, $query);

	echo '<ul id="pesqcpf" class="list-unstyled ulcpf">';
	if (mysqli_num_rows($result) == 0) {
		echo '<li class="licpf">Nenhum resultado encontrado!</li>';
	} else {

		while ($user = mysqli_fetch_assoc($result)) {
            $id = $user['idAnuncio'];
			echo  '<li id="li[' . $id . ']" class="licpf">' .
				$user['skuAnuncio'] .
				' - ' .
				$user['model'] .
				' - ' .
				$user['NomeProduto'] .
				'</li>';
		}
		echo '</ul>';
	}
} // Fim Compras

//Vendas
if (isset($_POST["client"]) != null) {

	$resp = $indexCliente->search($_POST["client"]);

	echo '<ul id="pesqcpf" class="list-unstyled ulcpf">';
	if ($resp == 0) {
		echo '<li class="licpf">Nenhum resultado encontrado!</li>';
	} else {

		foreach ($resp['data2'] as $user) {
			echo  '<li id="li[' . $user['idCliente'] . ']" class="licpf">' .
				$user['cpfCliente'] .
				' - ' .
				$user['NomeCliente'] .

				'</li>';
		}
		echo '</ul>';
	}
} // Fim Vendas

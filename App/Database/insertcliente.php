<?php
require_once '../auth.php';
require_once '../Models/cliente.class.php';

if(isset($_POST['upload']) == 'Cadastrar'){

$NomeCliente  = $_POST['NomeCliente'];
$cpfCliente   = $_POST['cpfCliente'];
$CepCliente   = $_POST['CepCliente'];

 
$cliente = new Cliente;

if($NomeCliente != NULL && $cpfCliente != NULL && $CepCliente != NULL){

		if (!isset($_POST['idCliente']))
		{

			$result = $cliente->InsertCliente($NomeCliente, $cpfCliente, $CepCliente, $idUsuario, $perm);
	}else{
			$idCliente = $_POST['idCliente'];
			$result = $cliente->UpdateCliente($idCliente, $NomeCliente, $cpfCliente, $CepCliente, $idUsuario, $perm);				
		}	
			$_SESSION['alert'] = $result;
		    header('Location: ../../views/cliente/index.php');		
	}else{
			header('Location: ../../views/cliente/index.php?alert=3');
		}		
 }else{
	header('Location: ../../views/cliente/index.php');
} 
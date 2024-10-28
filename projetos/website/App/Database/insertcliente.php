<?php
require_once '../auth.php';
require_once '../Models/cliente.class.php';

if(isset($_POST['upload']) == 'Cadastrar'){

$NomeCliente  = $_POST['NomeCliente'];
$Cidade       = $_POST['Cidade'];
$UF           = $_POST['UF'];
$FoneCliente = $_POST['FoneCliente'];
$cpfCliente   = $_POST['cpfCliente'];

 
$cliente = new Cliente;

if($NomeCliente != NULL && $Cidade != NULL && $UF != NULL && $FoneCliente != NULL && $cpfCliente != NULL){

		if (!isset($_POST['idCliente']))
		{

			$result = $cliente->InsertCliente($NomeCliente, $Cidade, $UF, $FoneCliente, $cpfCliente, $idUsuario, $perm);
	}else{
			$idCliente = $_POST['idCliente'];
			$result = $cliente->UpdateCliente($idCliente, $NomeCliente, $Cidade, $UF, $FoneCliente, $cpfCliente, $idUsuario, $perm);				
		}	
			$_SESSION['alert'] = $result;
		    header('Location: ../../views/cliente/index.php');		
	}else{
			header('Location: ../../views/cliente/index.php?alert=3');
		}		
 }else{
	header('Location: ../../views/cliente/index.php');
} 
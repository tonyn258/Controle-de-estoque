<?php
require_once '../../App/auth.php';
require_once '../../App/Models/sales.class.php';

if (isset($_POST['idItem']) > 0 && 
    !empty($_POST['qtde'])       &&
    !empty($_POST['NomeCliente']) &&
    !empty($_POST['cpfCliente']) &&
    !empty($_POST['FoneCliente']) &&
    !empty($_POST['Cidade']) &&
    !empty($_POST['UF']) &&
    !empty($_POST['DataVenda']) &&
    !empty($_POST['CodRastreioV'])&&
    !empty($_POST['TxMl']) &&
    !empty($_POST['TxFret']) &&
    !empty($_POST['Vd_Tax'])     

    ){      

  $idItem       = $_POST['idItem'];
  $quant        = $_POST['qtde'];
  $NomeCliente  = $_POST['NomeCliente'];
  $cpfCliente   = $_POST['cpfCliente'];
  $FoneCliente  = $_POST['FoneCliente'];
  $Cidade       = $_POST['Cidade'];
  $UF           = $_POST['UF'];
  $DataVenda    = $_POST['DataVenda'];
  $CodRastreioV = $_POST['CodRastreioV'];
  $TxMl         = $_POST['TxMl'];
  $TxFret       = $_POST['TxFret'];
  $Vd_Tax       = $_POST['Vd_Tax'];  
  $vendas = new Vendas;
  $vendas->itensVendidos($idItem, $quant,$NomeCliente,$cpfCliente,$FoneCliente,$Cidade,$UF,$idUsuario,
  $DataVenda, $CodRastreioV,$TxMl,$TxFret,$Vd_Tax);
}else{     
  $_SESSION['msg'] = 'Falta preencher alguns campos obrigatorios!';
  header('Location: ../../views/sales/');
}
?>
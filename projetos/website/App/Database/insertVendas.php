<?php
require_once '../../App/auth.php';
require_once '../../App/Models/vendas.class.php';

if (
    isset($_POST['idItem']) > 0 && 
    !empty($_POST['qtde'])       &&
    isset($_POST['NomeCliente']) != NULL &&
    isset($_POST['cpfCliente']) != NULL &&
    isset($_POST['FoneCliente']) != NULL &&
    isset($_POST['Cidade']) != NULL &&
    isset($_POST['UF']) != NULL &&
    isset($_POST['DataVenda']) != NULL &&
    isset($_POST['CodRastreioV']) != NULL &&
    isset($_POST['TxMl'])  != NULL &&
    isset($_POST['TxFret']) != NULL &&
    isset($_POST['Vd_Tax']) != NULL){
  
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
  $connect = new Connect;
 
  //$cart = $_SESSION['cart'];
        
    foreach ($_POST['idItem'] as $key => $error){

       $id = $_POST['idItem'][$key];
    $quant = $_POST['qtd'][$key];    
   
    $vendas = new Vendas;
    $result = $vendas->itensVerify($id, $quant);  
    if($result['status'] == 0){

        $_SESSION['msg'] = '<div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Ops!</strong>
        O Produto <b>'. $result['NomeProduto'] . '</b> 
        não pode ser vendido nessa quantidade! <br/> 
        Quantidade em estoque <b>'. $result['estoque'] .'. </b><br/></div>';
        header('Location: ../../views/vendas/index.php');
        exit;       
    }
}
foreach ($_POST['idItem'] as $key => $error) {
    
    $id = $_POST['idItem'][$key];
    $quant = $_POST['qtd'][$key];
    
    $vendas = new Vendas;                
    $vendas->itensVendido($id, $quant,$NomeCliente,$cpfCliente,$FoneCliente,$Cidade,$UF,$idUsuario,$DataVenda, $CodRastreioV,$TxMl,$TxFret,$Vd_Tax);                
}    
}else{
    $_SESSION['alert'] = 0;    
    header('Location: ../../views/vendas/index.php');
}
?>
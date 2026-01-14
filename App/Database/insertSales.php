<?php
require_once '../../App/auth.php';
require_once '../../App/Models/sales.class.php';

if (isset($_POST['idItem']) && 
    !empty($_POST['qtd'])       &&
    !empty($_POST['NomeCliente']) &&
    !empty($_POST['cpfCliente']) &&
    !empty($_POST['CepCliente']) &&
    !empty($_POST['DataVenda'])

    ){      

  $idItem       = $_POST['idItem'];
  $quant        = $_POST['qtd'];
  $NomeCliente  = $_POST['NomeCliente'];
  $cpfCliente   = $_POST['cpfCliente'];
  $CepCliente  = $_POST['CepCliente'];
  $DataVenda    = $_POST['DataVenda'];
  $CodRastreioV = $_POST['CodRastreioV'] ?? '';
  
  // Recebe o array de preços do carrinho, se existir
  $Vd_Tax_Array = isset($_POST['Vd_Tax_Array']) ? $_POST['Vd_Tax_Array'] : [];

  $vendas = new Vendas;
  
  // O loop agora acontece aqui ou dentro da classe?
  // O arquivo original tinha um loop foreach($_POST['idItem']...)
  // Vamos manter a lógica original de loop, mas passando o preço correto.

    foreach ($_POST['idItem'] as $key => $error) {
        $id = $_POST['idItem'][$key];
        $quant = $_POST['qtd'][$key];
        
        // Pega o preço específico deste item, ou 0 se não definido
        $precoItem = isset($Vd_Tax_Array[$key]) ? $Vd_Tax_Array[$key] : 0;

        $vendas->itensVendidos($id, $quant, $NomeCliente, $cpfCliente, $CepCliente, $idUsuario, $DataVenda, $CodRastreioV, $precoItem);
    }
    
    // Limpa o carrinho após o sucesso (opcional, mas recomendado)
    unset($_SESSION['carrinho']);
    unset($_SESSION['Cliente'], $_SESSION['cpf'], $_SESSION['Cep']);
    
}else{     
  $_SESSION['msg'] = 'Falta preencher alguns campos obrigatorios!';
  header('Location: ../../views/sales/');
}
?>
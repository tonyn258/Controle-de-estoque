<?php
// Este é um script PHP que processa uma solicitação de envio de formulário para cadastrar ou atualizar informações de compra de produtos.
// Incluindo o arquivo de autenticação e o arquivo que contém a classe Compras
require_once '../auth.php';
require_once '../Models/compras.class.php';
// Verificando se a solicitação POST contém o parâmetro 'upload' e seu valor é 'Cadastrar'
if (isset($_POST['upload']) && $_POST['upload'] == 'Cadastrar') {
    // Recuperando os valores enviados pelo formulário via POST
    $skuProduto   = $_POST['skuProduto'];
    $model        = $_POST['model'];
    $NomeProduto  = $_POST['NomeProduto'];
    $CodRastreio  = $_POST['CodRastreio'];
    $ValorCompra  = $_POST['ValorCompra'];
    $DataCompra   = $_POST['DataCompra'];
    $DataEntrega  = $_POST['DataEntrega'];
    $QuantItens   = $_POST['QuantItens'];
    
    
    
    // Criando um novo objeto da classe Compras
    $compras = new Compras;
    // Verificando se todas as informações necessárias foram preenchidas pelo usuário
    if ($skuProduto != null && $model != null && $NomeProduto != null && $CodRastreio != null && $ValorCompra != null && $DataCompra != null && $QuantItens != null)
     {
        // Configurando a mensagem de alerta com base no resultado da operação
        if ($result) {
            $_SESSION['alert'] = '0'; // Operação não efetuada, erro
        } else {
            $_SESSION['alert'] = '1'; // Operação realizada com sucesso
           
        }
    } else { // Se alguma informação estiver faltando, redireciona de volta para a página de compras com um alerta de erro.
        header('Location: ../../views/estoque/index.php?alert=0');
        exit();
    }

    // Redirecionando para a página de compras
    header('Location: ../../views/estoque/index.php');
    exit();
} else { // Se o parâmetro 'upload' não estiver definido como 'Cadastrar', redireciona de volta para a página de compras com um alerta de erro.
    header('Location: ../../views/estoque/index.php?alert=0');
    exit();
}
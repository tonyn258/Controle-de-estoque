<?php
require_once '../auth.php';
require_once '../Models/compras.class.php';

if (isset($_POST['IdCompra'])) {
    $IdCompra     = $_POST['IdCompra'];
    $skuProduto   = $_POST['skuProduto'];
    $model        = $_POST['model'];
    $NomeProduto  = $_POST['NomeProduto'];
    $CodRastreio  = $_POST['CodRastreio'];
    $ValorCompra  = $_POST['ValorCompra'];
    $DataCompra   = $_POST['DataCompra'];
    $DataEntrega  = $_POST['DataEntrega'];

    $sqlUpdate     =  "UPDATE `compras` SET 
    `skuProduto`   = '$skuProduto', 
    `Modelo`       = '$model',
    `Nome_Produto` = '$NomeProduto', 
    `Rastreio`     = '$CodRastreio', 
    `Valor_Compra` = '$ValorCompra', 
    `Data_Compra`  = '$DataCompra', 
    `Data_Entrega` = '$DataEntrega' 
    WHERE `id`   = '$IdCompra'";

    //$result = $conexao->query($sqlUpdate);
    $result = mysqli_query($this->SQL, $query); 
    
}

header('Location: ../../views/compras/index.php?alert=0');

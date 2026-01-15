<?php
error_reporting(0);
require_once '../../App/auth.php';
require_once '../../App/Models/connect.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['term'])) {
    $connect = new Connect();
    $term = mysqli_real_escape_string($connect->SQL, $_GET['term']);

    // Busca por Nome ou SKU, apenas itens com estoque positivo
    $query = "SELECT IdCompra, skuAnuncio, NomeProduto, QuantItens, QuantItensVend, ValorVenda 
              FROM `anuncio` 
              WHERE (skuAnuncio LIKE '%$term%' OR NomeProduto LIKE '%$term%') 
              AND (QuantItens - COALESCE(QuantItensVend, 0) > 0)
              LIMIT 10";

    $result = mysqli_query($connect->SQL, $query);
    $json = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $estoqueAtual = $row['QuantItens'] - ($row['QuantItensVend'] ?? 0);
        $valorVenda = isset($row['ValorVenda']) ? $row['ValorVenda'] : 0;
        
        $json[] = [
            'id' => $row['IdCompra'], // ID interno para o sistema
            'sku' => $row['skuAnuncio'],
            'label' => $row['skuAnuncio'] . ' - ' . $row['NomeProduto'], // Exibição na lista
            'nome' => $row['NomeProduto'],
            'estoque' => $estoqueAtual,
            'preco' => number_format($valorVenda, 2, ',', '') // Preço sugerido formatado (ex: 10,50)
        ];
    }

    echo json_encode($json);
}
?>
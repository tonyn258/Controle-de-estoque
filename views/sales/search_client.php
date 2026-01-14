<?php
error_reporting(0); // Suprime avisos para garantir que o JSON não quebre
require_once '../../App/auth.php';
require_once '../../App/Models/cliente.class.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['term'])) {
    $cliente = new Cliente();
    $term = $_GET['term'];
    
    // Utiliza o método search existente na classe Cliente
    $results = $cliente->search($term);
    
    $json = [];
    if ($results && isset($results['data2'])) {
        foreach ($results['data2'] as $row) {
            $json[] = [
                'id' => $row['idCliente'],
                'value' => $row['NomeCliente'], // O que aparece na lista
                'label' => $row['NomeCliente'] . ' - ' . $row['cpfCliente'], // O que aparece na sugestão
                'cpf' => $row['cpfCliente'],
                'nome' => $row['NomeCliente'],
                'cep' => isset($row['CepCliente']) ? $row['CepCliente'] : ''
            ];
        }
    }
    echo json_encode($json);
}
?>
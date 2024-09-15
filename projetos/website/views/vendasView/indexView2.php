<?php
require_once '../../App/auth.php'; // Verifica se o usuário está autenticado
require_once '../../layout/script.php'; // Inclui os scripts necessários
require_once '../../App/Models/vendasView.class.php'; // Inclui a classe Vendas

// Imprime o cabeçalho HTML, o cabeçalho da página, a barra lateral e abre a seção do conteúdo
echo $head;
echo $header;
echo $aside;

echo '<div class="content-wrapper">
    <section class="content-header">
        <h1>Vendas Realizadas</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Vendas</li>
        </ol>
    </section>
    <section class="content">';

require '../../layout/alert.php'; // Inclui alertas para mensagens ao usuário

echo '
  <div class="row">  
   <div class="box box-primary">
    <div class="box-body">';
    

// Instancia um novo objeto da classe Vendas
$vendas = new Vendas;
$resp =  $vendas->indexView(); // Obtenção de todas as vendas
$resps = json_decode($resp, true);

$clientes = []; // Array para armazenar os CPFs e os nomes dos clientes

// Organiza as vendas por cliente (CPF)
foreach ($resps as $row) {
    // Agrupa as vendas por cliente usando CPF e salva idCliente, nomeCliente e as vendas
    if (!isset($clientes[$row['cpfCliente']])) {
        $clientes[$row['cpfCliente']]['idCliente'] = $row['idCliente']; 
        $clientes[$row['cpfCliente']]['NomeCliente'] = $row['NomeCliente'];
        $clientes[$row['cpfCliente']]['vendas'] = []; // Inicializa o array de vendas
    }
    $clientes[$row['cpfCliente']]['vendas'][] = $row; // Agrupa as vendas por CPF
}

// Função para ordenar as vendas do cliente
foreach ($clientes as &$cliente) {
    usort($cliente['vendas'], function($a, $b) {
        // Ordena por idVendas em ordem decrescente
        return $b['idVendas'] - $a['idVendas'];
    });
}

// Exibe o cabeçalho acima da lista de clientes
echo '<h3>Clientes e suas Vendas</h3>';

echo '<ul class="list-group">';

foreach ($clientes as $cpf => $cliente) {
    // Cria um identificador único para cada cliente com base no CPF
    $collapseId = 'collapse-' . $cpf;

    // Exibe o ID do cliente e o nome do cliente
    echo '<li class="list-group-item">
            <a data-toggle="collapse" href="#' . $collapseId . '" role="button" aria-expanded="false" aria-controls="' . $collapseId . '">
                ' . $cliente['idCliente'] . ' - ' . $cliente['NomeCliente'] . ' (CPF: ' . $cpf . ')
            </a>
          </li>';

    // Conteúdo colapsável que mostra as vendas do cliente
    echo '<div class="collapse" id="' . $collapseId . '">
            <div class="card card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Cpf</th>           
                        <th>SKU/Modelo</th>         
                        <th>Nome Produto</th>   
                        <th>Cod.Rastreio</th>             
                        <th>Valor</th>          
                        <th>Data Venda</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>';

    // Exibe as vendas do cliente atual
    foreach ($cliente['vendas'] as $venda) {
        echo '<tr>';
        echo '<td>' . $venda['idVendas'] . '</td>';
        echo '<td>' . $venda['NomeCliente'] . '</td>';
        echo '<td>' . $venda['cpfCliente'] . '</td>';
        echo '<td>' . $venda['skuProduto'] . ' / ' . $venda['model'] . '</td>';  
        echo '<td>' . $venda['NomeProduto'] . '</td>';
        echo '<td>' . $venda['CodRastreioV'] . '</td>';
        echo '<td>R$ ' . number_format($venda['valor'], 2, ',', '.') . '</td>';
        echo '<td>' . date('d-m-Y', strtotime($venda['DataVenda'])) . '</td>';
        echo '<td> <a href="editvenda.php?id=' . $venda['idVendas'] . '"<i class="fa fa-edit"></i></a></td>';
        echo '</tr>';
    }

    echo '</tbody>
            </table>
            </div>
          </div>';
}

echo '</ul>';
echo '</div>
</table>
   </div>
  </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
?>

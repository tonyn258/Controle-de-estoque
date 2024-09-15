<?php
require_once '../../App/auth.php'; // Verifica se o usuário está autenticado
require_once '../../layout/script.php'; // Inclui os scripts necessários
require_once '../../App/Models/vendasView.class.php'; // Inclui a classe Vendas

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

// Campo de busca (pesquisa por nome ou CPF)
echo '
    <div class="form-group">
        <label for="search">Pesquisar por Nome ou CPF:</label>
        <input type="text" id="search" class="form-control" placeholder="Digite o nome ou CPF">
    </div>
';

// Instancia um novo objeto da classe Vendas
$vendas = new Vendas;
$resp =  $vendas->indexView(); // Obtenção de todas as vendas
$resps = json_decode($resp, true);

$clientes = []; // Array para armazenar os CPFs e os nomes dos clientes

// Organiza as vendas por cliente (CPF)
foreach ($resps as $row) {
    if (!isset($clientes[$row['cpfCliente']])) {
        $clientes[$row['cpfCliente']]['idCliente'] = $row['idCliente']; 
        $clientes[$row['cpfCliente']]['NomeCliente'] = $row['NomeCliente'];
        $clientes[$row['cpfCliente']]['cpfCliente'] = $row['cpfCliente']; // Adiciona o CPF ao array do cliente
        $clientes[$row['cpfCliente']]['vendas'] = []; // Inicializa o array de vendas
    }
    $clientes[$row['cpfCliente']]['vendas'][] = $row; // Agrupa as vendas por CPF
}

// Ordenação dos clientes pelo nome e CPF
usort($clientes, function($a, $b) {
    // Ordena primeiro pelo NomeCliente, depois pelo CPF
    return strcmp($a['NomeCliente'], $b['NomeCliente']) ?: strcmp($a['cpfCliente'], $b['cpfCliente']);
});

// Função para ordenar as vendas do cliente
foreach ($clientes as &$cliente) {
    usort($cliente['vendas'], function($a, $b) {
        // Ordena por idVendas em ordem decrescente
        return $b['idVendas'] - $a['idVendas'];
    });
}

echo '<h3>Clientes e suas Vendas</h3>';
echo '<ul class="list-group" id="clientesList">'; // Adiciona o ID da lista para a pesquisa

foreach ($clientes as $cpf => $cliente) {
    $collapseId = 'collapse-' . $cpf;
    // Exibe o nome do cliente seguido do CPF
    echo '<li class="list-group-item">
            <a data-toggle="collapse" href="#' . $collapseId . '" role="button" aria-expanded="false" aria-controls="' . $collapseId . '">
                ' . $cliente['NomeCliente'] . ' (CPF: ' . $cliente['cpfCliente'] . ')
            </a>
          </li>';

    echo '<div class="collapse" id="' . $collapseId . '">
            <div class="card card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>CPF</th>           
                        <th>SKU/Modelo</th>         
                        <th>Nome Produto</th>   
                        <th>Cod. Rastreio</th>             
                        <th>Valor</th>          
                        <th>Data Venda</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($cliente['vendas'] as $venda) {
        echo '<tr>';
        echo '<td>' . $venda['idVendas'] . '</td>';
        echo '<td>' . $venda['NomeCliente'] . '</td>';
        echo '<td>' . $venda['cpfCliente'] . '</td>';
        echo '<td>' . $venda['skuProduto'] . ' / ' . $venda['model'] . '</td>';  
        echo '<td>' . $venda['NomeProduto'] . '</td>';
        echo '<td>' . $venda['CodRastreioV'] . '</td>';        
        echo '<td>R$ ' . (isset($venda['Vd_Tax']) ? number_format($venda['Vd_Tax'], 2, ',', '.') : '0,00') . '</td>';
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

// Adiciona script para pesquisa por nome ou CPF
echo '
<script>
    document.getElementById("search").addEventListener("keyup", function() {
        var input = this.value.toLowerCase();
        var clientesList = document.getElementById("clientesList");
        var items = clientesList.getElementsByTagName("li");

        for (var i = 0; i < items.length; i++) {
            var cliente = items[i].textContent || items[i].innerText;
            if (cliente.toLowerCase().indexOf(input) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    });
</script>
';
?>

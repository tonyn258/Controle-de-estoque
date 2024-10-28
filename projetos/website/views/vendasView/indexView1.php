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
    <div class="box-body">
      
    <table id="example1" class="table table-bordered table-striped dataTable " role="grid" aria-describedby="example1_info">
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
      <tbody>    
    ';

// Instancia um novo objeto da classe Vendas
$vendas = new Vendas;

// Verifica se o usuário selecionou algum critério de ordenação na tabela
if (isset($_POST['sort_by']) && isset($_POST['sort_order'])) {
  $sort_by = $_POST['sort_by'];
  $sort_order = $_POST['sort_order'];
  $order_by = "ORDER BY $sort_by $sort_order";
} else {
  $order_by = "";
}

// Chama o método `indexVendas` da classe Vendas
$resp =  $vendas->indexView($order_by);
$resps = json_decode($resp, true);

// Percorre o array e imprime as linhas da tabela
foreach ($resps as $row) {
  echo '<tr>';
  echo '<td>' . $row['idVendas'] . '</td>';
  echo '<td>' . $row['NomeCliente'] . '</td>';
  echo '<td>' . $row['cpfCliente'] . '</td>';
  echo '<td>' . $row['skuProduto'] . ' / ' . $row['model'] . '</td>';  
  echo '<td>' . $row['NomeProduto'] . '</td>';
  echo '<td>' . $row['CodRastreioV'] . '</td>';

  echo '<td>R$ ' . number_format($row['valor'], 2, ',', '.') . '</td>';
  echo '<td>' . date('d-m-Y', strtotime($row['DataVenda'])) . '</td>';
  echo '<td> <a href="editvenda.php?id=' . $row['idVendas'] . '"<i class="fa fa-edit"></i></a></td>';
  echo '</tr>';
}

echo '</tbody>
    </table>
    </div>
   </div>
  </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;

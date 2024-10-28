<?php
require_once '../../App/auth.php'; // inclui o arquivo auth.php que verifica se o usuário está autenticado
require_once '../../layout/script.php'; // inclui o arquivo script.php que contém o cabeçalho HTML e os scripts necessários
require_once '../../App/Models/compras.class.php'; // inclui o arquivo compras.class.php que contém a classe Compras


// Imprime o cabeçalho HTML, o cabeçalho da página, a barra lateral e abre a seção do conteúdo
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Compras Realizadas</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Compras</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">';

require '../../layout/alert.php'; // inclui o arquivo alert.php que contém a estrutura de alerta para exibir mensagens ao usuário

echo '
  <!-- Small boxes (Stat box) -->
  <div class="row">
   <div class="box box-primary">
    
    <!-- /.box-header -->

    <style>
    .dot {
      display: inline-block;
      height: 12px;
      width: 12px;
      border-radius: 50%;
      margin-left: 10px;
      
    }
    .Orange-dot {
      background-color: Orange;
    }
    .green-dot {
      background-color: green;
    }
  </style>
    
    <div class="box-body">
      
    <!-- Inicio da Tabela -->
    <table id="example1" class="table table-bordered table-striped dataTable " role="grid" 
    aria-describedby="example1_info">
    <thead>
    <tr>
        <th>#</t>
        <th>SKU/Modelo</t>        
        <th>Cod.Rastreio</t> 
        <th>Nome Produto</t>               
        <th>Valor Compra</t>          
        <th>Compra</t>  
        <th>Entrega</t>
        <th>Dias Corridos</t>
        <th>Edit</t> 
              
                
      </tr>
      </thead>
      <tbody>    
    ';
// Instancia um novo objeto da classe Compras
$compras = new Compras;
// Verifica se o usuário selecionou algum critério de ordenação na tabela
if (isset($_POST['sort_by']) && isset($_POST['sort_order'])) {
  $sort_by = $_POST['sort_by'];
  $sort_order = $_POST['sort_order'];
  $order_by = "ORDER BY $sort_by $sort_order";
} else {
  $order_by = "";
}
// Chama o método index da classe Compras, passando as permissões do usuário e o critério de ordenação
$resp =  $compras->index($perm, $order_by);
// Decodifica a resposta JSON obtida do método index da classe Compras
$resps = json_decode($resp, true);
// Percorre o array $resps e imprime as linhas da tabela
foreach ($resps as $row) {
  echo '<tr>';
  echo '<td>' . $row['IdCompra'] . '</td>';
  echo '<td>' . $row['skuProduto'] . ' / ' . $row['model'] . '</td>';
  echo '<td>' . $row['CodRastreio'] . '</td>';
  echo '<td>' . $row['NomeProduto'] . '</td>';
  echo '<td>R$ ' . number_format($row['ValorCompra'], 2, ',', '.') . '</td>';
  echo '<td>' . date('d-m-Y', strtotime($row['DataCompra'])) . '</td>';

  // Verifica se DataEntrega é NULL e adiciona a classe CSS amarela, caso contrário, adiciona a classe CSS verde
  if (isset($row['DataEntrega']) && !empty($row['DataEntrega']) && $row['DataEntrega'] !== '0000-00-00') {
    echo '<td>' . date('d-m-Y', strtotime($row['DataEntrega'])) . ' <span class="dot green-dot"></span></td>';
  } else {
    $dataEntrega = date('d-m-Y');
    echo '<td>' . $dataEntrega . ' <span class="dot Orange-dot"></span></td>';
    //echo '<td>Aguardando <span class="dot Orange-dot"></span></td>';  

  }
  $dataCompra = new DateTime($row['DataCompra']); // inicializa a variável $dataCompra

  // Calcula o Tempo de Entrega em dias
  if (isset($row['DataEntrega']) && !empty($row['DataEntrega']) && $row['DataEntrega'] !== '0000-00-00') {
    $dataEntrega = new DateTime($row['DataEntrega']);
    $tempoEntrega = $dataEntrega->diff($dataCompra)->days; // calcula a diferença em dias
  } else {
    $dataAtual = new DateTime();
    $tempoEntrega = $dataAtual->diff($dataCompra)->days;
  }
  echo '<td>' . $tempoEntrega . '- Dias</td>';
  echo '<td> <a href="editcompra.php?id=' . $row['IdCompra'] . '"<i class="fa fa-edit"></i></a></td>';
  echo '</tr>';

  
  
}
echo '<ul class="todo-list not-done">';
if(isset($_POST['public']) != NULL){               

  $value = $_POST['public']; 
  if($value == 1){
   
    $public = 0;
    $button_name = "Inativos";

  }else{
    $public = 1;
    $button_name = "Publicados";
  }     

}else{
  $value = 1;
  $public = 0;
  $button_name = "Inativos";
}
echo' <ul class="todo-list">';
               $compras->totalcompra($value);
echo '</ul>
                        </tbody>
                    </table>
                    <!-- Fim da tabela -->
                    <br/>
                    <!-- /.box-body -->
                    <div class="left">
                    <form action="totalcompra.php" method="post">
                    <button name="public" type="submit" value="'.$public.'" class="btn btn-default pull-left"><i class="fa fa-plus"></i> '.$button_name.'</button></div></form>           
                        <a href="addcompra.php" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Produto</a>
                    </div>
                </div>
            </div>
        </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
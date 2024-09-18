<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/produto.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Usuário
  </h1>
  <ol class="breadcrumb">
    <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Produtos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  ';
require '../../layout/alert.php';
echo '
  <!-- Small boxes (Stat box) -->
  <div class="row">
   <div class="box box-primary">
    <div class="box-header">
      <i class="ion ion-clipboard"></i>

      <h3 class="box-title">Lista de Produtos</h3>

      <div class="box-tools pull-right">
        <ul class="pagination pagination-sm inline">
          <li><a href="#">&laquo;</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">&raquo;</a></li>
        </ul>
      </div>      
    </div>
    <!-- /.box-header -->
    <div class="box-body">    
      
    <!-- Inicio da Tabela -->
    <table  id="example1" class="table table-bordered table-striped dataTable" role="grid" 
    aria-describedby="example1_info" >
    
      <thead>
          <tr>
            <th>#              </th>
            <th>SKU            </th>
            <th>Modelo         </th>
            <th>Nome do Protudo</th>
            <th>Quant.         </th>
            <th>Tipo de Conexão</th>
            <th>Marca          </th>  
            <th>Status Produto</th>  
            <th>Edit</th>      
          </tr>
      </thead>
        <tbody>    
    ';

    

$produto = new Produto;
$resp =  $produto->index();
$resps = json_decode($resp, true);
foreach ($resps as $row) {
  if (isset($row['idProduto']) != NULL) {
    echo '<tr>';
    echo '<td>' . $row['idProduto']     . '</td>';
    echo '<td>' . $row['skuProduto']    . '</td>';
    echo '<td>' . $row['model']         . '</td>';
    echo '<td>' . $row['NomeProduto']   . '</td>';
    echo '<td>' . $row['Quantidade']    . '</td>';
    echo '<td>' . $row['Conexao']       . '</td>';
    echo '<td>' . $row['Marca']         . '</td>';
    echo '<td>' . $row['statusProduto'] . '</td>';
    echo '<td> <a href="editproduto.php?id='.$row['idProduto'].'"<i class="fa fa-edit"></i></a></td>';
    echo '</tr>';
  }
}
echo '

        </tbody>
        </table>
        <!-- Fim da tabela -->
        <br/>
        <!-- /.box-body -->
        <div class="left">
         <form action="index.php" method="post">
           <a href="addproduto.php" type="button" class="btn btn-success pull-right"><i
            class="fa fa-plus"></i> Add Produto</a>
         </div>
       </div>
       ';
echo '</div>';
echo '</section>';
echo '</div>';
echo  $footer;
echo $javascript;

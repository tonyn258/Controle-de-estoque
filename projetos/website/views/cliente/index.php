<?php
// Include necessary files
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/cliente.class.php';

// Display header, navigation, and page title
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Cliente
          </h1>
          <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cliente</li>
          </ol>
        </section>
        <!-- Main content -->
      <section class="content">';

// Include any alert messages
require '../../layout/alert.php';

// Display the list of clients in a table format
echo '
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="box box-primary">
      <div class="box-header">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">Lista de Clientes</h3> 
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped dataTable" role="grid" 
          aria-describedby="example1_info">
          <thead>
            <tr>
              <th>#</th>
              <th>Nome Cliente</th>
              <th>Cidade</th>
              <th>UF</th>
              <th>Telefone</th>
              <th>CPF</th>
              <th>Status Cliente</th>
            </tr>
          </thead>
          <tbody>';
              
// Retrieve client data from the database and display it in the table
$value = "";
$resp = $cliente->indexCliente($value,$perm);
$resps = json_decode($resp, true);
foreach ($resps as $row) {
  if (isset($row['idCliente']) != NULL) {
    echo '<tr>';
    echo '<td>' . $row['idCliente'] . '</td>';
    echo '<td>' . $row['NomeCliente'] . '</td>';
    echo '<td>' . $row['Cidade'] . '</td>';
    echo '<td>' . $row['UF'] . '</td>';
    echo '<td>' . $row['FoneCliente'] . '</td>';	
    echo '<td>' . $row['cpfCliente'] . '</td>';	
    echo '<td>' . $row['statusCliente'] . '</td>';	
    echo '</tr>';
  }
}

// Finish displaying the table
echo '</tbody>
        </table>
      </div>
      <!-- /.box-body -->
      <div class="box-footer clearfix no-border">
        <a href="addcliente.php" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Cliente</a>
      </div>
    </div>
  </div>
';

// Close the content wrapper and display footer and JavaScript files
echo '</div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
?>

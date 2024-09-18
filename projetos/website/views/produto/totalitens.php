<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/itens.class.php';

$itens = new Itens(); // Crie uma instância da classe Itens

echo $head;
echo $header;
echo $aside;
echo '
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <style>
    .dot {
      display: inline-block;
      height: 12px;
      width: 20px;
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
    <section class="content-header">
        <h1>
            Itens cadastrados
        </h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Itens</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">';
require '../../layout/alert.php';
echo '
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="box box-primary">
                
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Inicio da Tabela -->
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                           aria-describedby="example1_info">
                        <thead>
                        <tr>
                            
                            <th>Produto</th>
                            <th>Comprados</th>
                            <th>Vendidos</th>
                            <th>Em Estoque</th>
                        </tr>
                        </thead>
                        <tbody>';

if (isset($_POST['public']) && $_POST['public'] !== '') {
    $value = $_POST['public'];
    if ($value == 1) {
        $public = 0;
        $button_name = "Inativos";
    } else {
        $public = 1;
        $button_name = "Publicados";
    }
} else {
    $value = 1;
    $public = 0;
    $button_name = "Inativos";
}

$resp = $itens->totalitens($value);
$resps = $resp; 

foreach ($resps as $row) {
  echo '<tr>';
  echo '<td>' . $row['NomeProduto'] . '</td>';
  echo '<td>' . $row['QuantItens'] . '</td>';
  echo '<td>' . $row['QuantItensVend'] . '</td>';
  echo '<td>';
  
  if ($row['Estoque'] == 0) {
      echo $row['Estoque'].str_repeat('&nbsp;', 10) .'<span class="dot green-dot"></span> ';
  } elseif ($row['Estoque'] > 0) {
      echo $row['Estoque']. str_repeat('&nbsp;', 10) .'<span class="dot Orange-dot"></span> ';
  }
  
  echo '</td>';
  echo '</tr>';
}


echo '</tbody>
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footerAgora, você precisa adicionar o formulário abaixo do código para exibir o botão que alterna entre "Publicados" e "Inativos":

```php
                <div class="box-footer clearfix no-border">
                    <form action="totalitens.php" method="post">
                        <button name="public" type="submit" value="'.$public.'" class="btn btn-default pull-left">
                            <i class="fa fa-plus"></i> '.$button_name.'
                        </button>
                    </form>
                    <a href="additens.php" type="button" class="btn btn-success pull-right">
                        <i class="fa fa-plus"></i> Add Itens
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>';

echo $footer;
echo $javascript;
?>

<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">';

require '../../layout/alert.php';

if ($perm != 1) {
  echo "Você não tem permissão! </div>";

  exit();
}

echo '<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Adicionar <small>Produto</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Produto</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">

  <a href="./" class="btn btn-success">Voltar</a>
      <div class="row">
        <!-- left column -->
';   

if(isset($_GET['id'])){
  $IdCompra = $_GET['id']; 
  $resp = $compras->EditCompras($IdCompra);

echo '<!-- Cliente list PHP -->  
      <div class="col-md-9">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Produto</h3>
          </div>
          <!-- /.box-header -->';     
          if (!empty($_SESSION['msg'])) {
            echo ' <div class="col-xs-12 col-md-12 text-success">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg'], $_SESSION['SKU'], $_SESSION['Produto']);
          }        
         // <!-- Cliente list PHP -->      
          if (isset($_POST['SKU'])) {
  
            $produto = new Produto;
            $resps = $produto->searchdata($_POST["SKU"]);

            if ($resps > 0 && $_POST['SKU'] != NULL) {

              foreach ($resps['data'] as $resp) {

                $_SESSION['SKU']     = $resp['skuProduto'];
                $_SESSION['model']   = $resp['model'];
                $_SESSION['Produto'] = $resp['NomeProduto'];
              }
            }
            unset($_POST['SKU']);
          } 
          
          
          ?>

          
           
          <!--Cliente list PHP -->
          <div class="row">
            <form id="form" action="addcompra.php" method="post">
              <div class="box-body">
                <!--<form id="form1" action="index.php" method="post">-->
                <div class="col-lg-8">
                  <label for="exampleInputEmail1">Digite o SKU ou Nome do Produto</label>
                  <div class="input-group">
                    <!--<label for="exampleInputEmail1">Código</label>-->
                    <input type="text" class="form-control" id="skuProduto" name="SKU" placeholder="Pesquisar sku" autocomplete="off">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-floppy-save"></span></button>
                    </span>
                  </div><!-- /input-group -->
                  <div id="Listdata"></div>
                </div><!-- /.col-lg-6 -->
              </div>
            </form>
          </div>
          <!-- Cliente list FIM -->
      
          <!-- form start -->
          <form role="form" action="../../App/Database/insertCompras.php" method="POST">
            <div class="box-body">

              <div class="form-group row"> <!--Primeiro Div-->
                <div class="col-sm-2">
                  <label for="exampleInputEmail1">SKU</label>
                  <input type="text" name="skuProduto" class="form-control" id="exampleInputEmail1" placeholder="sku" 
                  value="<?php echo isset($_SESSION['Produto']) ? $_SESSION['SKU'] : $resp['compras']['SKU']; ?>"> 
                  
                </div>

                <div class="col-sm-2">
                  <label for="exampleInputEmail1">Modelo</label>
                  <input type="text" name="model" class="form-control" id="exampleInputEmail1" placeholder="Modelo"                  
                  value="<?php echo isset($_SESSION['Produto']) ? $_SESSION['model'] : $resp['compras']['Modelo']; ?>">
                </div>

                <div class="col-sm-7">
                  <label for="exampleInputEmail1">Nome do Produto</label>
                  <input type="text" name="NomeProduto" class="form-control" id="exampleInputEmail1" placeholder="Nome do Produto" 
                  value="<?php echo isset($_SESSION['Produto']) ? $_SESSION['Produto'] : $resp['compras']['Nome']; ?>">
                </div>
              </div>


           
              
              <div class="form-group row"><!--Segunda Div-->
                <div class="col-sm-3">
                  <label for="exampleInputEmail1">Cod. de Rastreio</label>
                  <input type="text" name="CodRastreio" class="form-control" id="exampleInputEmail1" placeholder="Cod. de Rastreio"
                  value="<?php echo $resp['compras']['Rastreio']; ?>">
                </div>

                <div class="col-sm-3">
                  <label for="exampleInputEmail1">Valor da Compra</label>
                  <div class="input-group">
                    <span class="input-group-addon">R$</span>
                    <input type="number" name="ValorCompra" class="form-control" id="exampleInputEmail1" placeholder="Valor da Compra" step="0.01" min="0"  
                    value="<?php echo isset($resp['compras']['Valor']) ? $resp['compras']['Valor'] : ''; ?>">
                  </div>
                </div>
                
                

               <div class="col-sm-3">
                  <label for="exampleInputEmail1">Data da Compra</label>
                  <?php
                  // converte a data do banco de dados para o formato ISO 8601
                  $dataCompra = date('Y-m-d\TH:i', strtotime($resp['compras']['Data']));
                  ?>
                  <input type="datetime-local" name="DataCompra" class="form-control" id="exampleInputEmail1" placeholder="MM/DD/YYYY"
                  value="<?php echo $dataCompra; ?>">
                </div>

                <div class="col-sm-3">
                  <label for="exampleInputEmail1">Data da Entrega</label>
                  <?php
                  // converte a data do banco de dados para o formato ISO 8601
                  $DataEntrega = date('Y-m-d\TH:i', strtotime($resp['compras']['Entrega']));
                  ?>
                  <input type="datetime-local" name="DataEntrega" class="form-control" id="exampleInputEmail1" placeholder="MM/DD/YYYY"
                  value="<?php echo $DataEntrega; ?>">
                </div>
              </div>
              
              <?php
                echo '
              <input type="hidden" name="iduser" value="'.$idUsuario.'">
              <input type="hidden" name="IdCompra" value="'.$IdCompra.'">

              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">Cadastrar</button>
                <a class="btn btn-danger" href="../../views/compras">Cancelar</a>
              </div>
          </form>
               
      
        </div>
                 
                 
        <!-- /.box-primaty -->';
        

      echo '</div>';
      echo '</div>';
    
                }///if

    echo '</div>';                
    echo '</div>';                
    echo '</section>';
    echo '</div>';
    echo  $footer;
    echo $javascript;
    
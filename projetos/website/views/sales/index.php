<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/sales.class.php';
require_once '../../App/Models/cliente.class.php';



// Criação das seções do layout
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
  <section class="content-header">
    <h1>Sales</h1>
    <ol class="breadcrumb">
      <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Sales</li>
    </ol>
  </section>

  <section class="content">';

// Inclusão de alertas de mensagens
require '../../layout/alert.php';



echo '<a href="./" class="btn btn-success">Voltar</a>
        <div class="row">
          <!-- left column -->
          <div class="col-md-10">
            <!-- general form elements -->
            <div class="box box-primary">
            
          
              <div class="box-header with-border">
                <h3 class="box-title">Cliente</h3>
              </div>

              <!-- /.box-header -->
              <!-- form start -->';
if (!empty($_SESSION['msg'])) {
  echo '<div class="col-xs-12 col-md-12 text-success">' . $_SESSION['msg'] . '</div>';
  unset($_SESSION['msg'],
        $_SESSION['Cliente'],
        $_SESSION['cpf'],
        $_SESSION['Telefone'],
        $_SESSION['Cidade'],
        $_SESSION['UF']);
}

?>
  <!-- Cliente list PHP -->

  <?php


if (isset($_POST['CPF'])) {
  $cliente = new Cliente;
  $resps = $cliente->searchdata($_POST["CPF"]);
  if ($resps > 0 && $_POST['CPF'] != NULL) {

    foreach ($resps['data'] as $resp) {

      $_SESSION['Cliente']  = $resp['NomeCliente'];
      $_SESSION['cpf']      = $resp['cpfCliente'];
      $_SESSION['Telefone'] = $resp['FoneCliente'];
      $_SESSION['Cidade']   = $resp['Cidade'];
      $_SESSION['UF']       = $resp['UF'];
    }
  }
  unset($_POST['CPF']);
}
?>

<!--Cliente list PHP -->
<div class="row">
  <form id="form1" action="index.php" method="post">
    <div class="box-body">
      <!--<form id="form1" action="index.php" method="post">-->
      <div class="col-lg-6">
        <!--<label for="exampleInputEmail1">Digite o Nome ou CPF</label>-->
        <div class="input-group">
          <!--<label for="exampleInputEmail1">Código</label>-->
          <input type="text" class="form-control" id="cpfCliente" name="CPF" placeholder="Pesquisar Nome" autocomplete="off">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-floppy-save"></span></button>
          </span>
        </div><!-- /input-group -->
        <div id="Listdata2"></div>
      </div><!-- /.col-lg-6 -->
    </div>
  </form>
</div>
<!-- Cliente list FIM -->

<form id="form2" action="../../App/Database/insertSales.php" method="POST">
<div class="box-body">
<div class="form-group row">
  
    <div class="col-sm-8">
      <label for="exampleInputEmail1">Nome Cliente</label>
      <input type="text" name="NomeCliente" class="form-control" id="exampleInputEmail1" placeholder="Nome Cliente"
      value="<?php if (isset($_SESSION['Cliente'])) {echo $_SESSION['Cliente'];} ?>" />
    </div>

      <div class="col-sm-3">
        <label for="exampleInputEmail1">CPF/CNPJ</label>
        <input type="text" name="cpfCliente" class="form-control " id="exampleInputEmail1" placeholder="CPF/CNPJ"
        value="<?php if (isset($_SESSION['cpf'])) {echo $_SESSION['cpf'];} ?>" />
      </div>
  </div>
    
      
    <div class="form-group row">
      <div class="col-sm-3">
        <label for="exampleInputEmail1">Telefone</label>
        <input type="text" name="FoneCliente"  class="form-control" id="exampleInputEmail1" placeholder="Telefone"
        value="<?php if (isset($_SESSION['Telefone'])) {echo $_SESSION['Telefone'];} ?>" />
      </div>

      <div class="col-sm-3">
        <label for="exampleInputEmail1">Cidade</label>
        <input type="text" name="Cidade" class="form-control" id="exampleInputEmail1" placeholder="Cidade Cliente"
        value="<?php if (isset($_SESSION['Cidade'])) {echo $_SESSION['Cidade'];} ?>" />
      </div>

      <div class="col-sm-1">
        <label for="exampleInputEmail1">UF</label>
        <input type="text" name="UF" class="form-control" id="exampleInputEmail1" placeholder="UF"
        value="<?php if (isset($_SESSION['UF'])) {echo $_SESSION['UF'];} ?>" />
      </div>
    </div>

    <div class="box">
      <div class="form-group row">
        <div class="col-sm-3">
          <label for="exampleInputEmail1">Data da Venda</label>
          <input type="datetime-local" name="DataVenda" class="form-control" id="exampleInputEmail1" placeholder="MM/DD/YYYY">
        </div>

        <div class="col-sm-3">
          <label for="exampleInputEmail1">Cod. de Rastreio</label>
          <input type="text" name="CodRastreioV" class="form-control" id="exampleInputEmail1" placeholder="Cod. de Rastreio">
        </div>        
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-2">
        <label for="exampleInputEmail1">ID do Item</label>        
        <input type="number" id="idItem" name="idItem" class="form-control" placeholder="Item">
      </div>

      <div class="col-sm-2">
        <label for="exampleInputEmail1">Quant. Item</label>        
        <input type="number" id="qtd" name="qtde" class="form-control" placeholder="Quantidade">
      </div>

      <div class="col-sm-2">      
        <label for="exampleInputEmail1">Taxa Mercado Livre</label>
        <div class="input-group">
            <span class="input-group-addon">R$</span>
            <input type="number" name="TxMl" class="form-control" placeholder="0,00" step="0.01" min="0">
        </div>
      </div>

      <div class="col-sm-2">        
        <label for="exampleInputEmail1">Valor do Frete</label>
        <div class="input-group">
            <span class="input-group-addon">R$</span>
            <input type="number" name="TxFret" class="form-control" placeholder="0,00" step="0.01" min="0">
        </div>
      </div>

      <div class="col-sm-2">              
        <label for="exampleInputEmail1">Venda sem taxa</label>
        <div class="input-group">
            <span class="input-group-addon">R$</span>
            <input type="number" name="Vd_Tax" class="form-control" placeholder="0,00" step="0.01" min="0">
        </div>
      </div>

    </div>
  </div>



  <div class="box-footer">
    <button class="btn btn-success" type="submit" name="comprar">Vender</button>
    <a class="btn btn-danger" href="../../views/Sales">Cancelar</a>
  </div>
</form>


<?php
echo '</div>';
echo '</section>';
echo '</div>';
// Criação das seções de rodapé e scripts 
echo $footer;
echo $javascript;
?>
<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/cliente.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">';

require '../../layout/alert.php';

if ($perm != 1) {
  echo "Você não tem permissão! </div>";
  exit();
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Adicionar <small>Cliente</small></h1>
  <ol class="breadcrumb">
    <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="index.php">Clientes</a></li>
    <li class="active">Cliente</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <?php require '../../layout/alert.php'; ?>

  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Dados do Cliente</h3>
      <div class="box-tools pull-right">
        <a href="index.php" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> Voltar</a>
      </div>
    </div>
    
    <!-- form start -->
    <form role="form" action="../../App/Database/insertcliente.php" method="POST">
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="NomeCliente">Nome Completo</label>
              <input type="text" name="NomeCliente" class="form-control" id="NomeCliente" placeholder="Nome Completo" required>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              <label for="cpfCliente">CPF</label>
              <input type="text" name="cpfCliente" class="form-control" id="cpfCliente" placeholder="CPF" required>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label for="CepCliente">CEP</label>
              <input type="text" name="CepCliente" class="form-control" id="CepCliente" placeholder="CEP" required>
            </div>
          </div>
        </div>

        <input type="hidden" name="iduser" value="<?php echo $idUsuario; ?>">
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">
          <i class="fa fa-save"></i> Cadastrar
        </button>
      </div>
    </form>
  </div>
</section>
</div>
<?php
echo $footer;
echo $javascript;
?>
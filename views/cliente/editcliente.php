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

// Handle Update
if (isset($_POST['upload']) && $_POST['upload'] == 'Atualizar') {
    $id = $_POST['iduser'];
    $nome = $_POST['NomeCliente'];
    $cpf = $_POST['cpfCliente'];
    $cep = $_POST['CepCliente'];
    
    // Attempt to update using $pdo if available from auth.php, or warn user
    if(isset($pdo)){
        $stmt = $pdo->prepare("UPDATE cliente SET NomeCliente = ?, cpfCliente = ?, CepCliente = ? WHERE idCliente = ?");
        $stmt->execute([$nome, $cpf, $cep, $id]);
        echo '<div class="alert alert-success">Cliente atualizado com sucesso!</div>';
    } else {
        echo '<div class="alert alert-warning">Erro: Conexão com banco de dados ($pdo) não encontrada. Verifique auth.php ou cliente.class.php.</div>';
    }
}

// Fetch Client Data
$id = $_GET['id'] ?? null;
$row = [];

if ($id) {
    $resp = $cliente->indexCliente($id, $perm);
    $resps = json_decode($resp, true);
    if ($resps) {
        foreach ($resps as $r) {
            if ($r['idCliente'] == $id) {
                $row = $r;
                break;
            }
        }
    }
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Editar <small>Cliente</small></h1>
  <ol class="breadcrumb">
    <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="index.php">Clientes</a></li>
    <li class="active">Editar</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Dados do Cliente</h3>
      <div class="box-tools pull-right">
        <a href="index.php" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> Voltar</a>
      </div>
    </div>
    
    <!-- form start -->
    <form role="form" action="" method="POST">
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="NomeCliente">Nome Completo</label>
              <input type="text" name="NomeCliente" class="form-control" id="NomeCliente" placeholder="Nome Completo" value="<?php echo isset($row['NomeCliente']) ? $row['NomeCliente'] : ''; ?>" required>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              <label for="cpfCliente">CPF</label>
              <input type="text" name="cpfCliente" class="form-control" id="cpfCliente" placeholder="CPF" value="<?php echo isset($row['cpfCliente']) ? $row['cpfCliente'] : ''; ?>" required>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label for="CepCliente">CEP</label>
              <input type="text" name="CepCliente" class="form-control" id="CepCliente" placeholder="CEP" value="<?php echo isset($row['CepCliente']) ? $row['CepCliente'] : ''; ?>" required>
            </div>
          </div>
        </div>

        <input type="hidden" name="iduser" value="<?php echo $id; ?>">
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" name="upload" class="btn btn-primary" value="Atualizar">
          <i class="fa fa-save"></i> Atualizar
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
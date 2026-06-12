<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/vendasteste.class.php';
require_once '../../App/Models/cliente.class.php';

echo $head;
echo $header;
echo $aside;

echo '<div class="content-wrapper">
  <section class="content-header">
    <h1>Venda Testa</h1>
    <ol class="breadcrumb">
      <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Venda Testa</li>
    </ol>
  </section>
  <section class="content">';

require '../../layout/alert.php';

echo '<a href="./" class="btn btn-success">Voltar</a>
<div class="row">
  <div class="col-md-10">
    <div class="box box-primary">          
      <div class="box-header with-border">
        <h3 class="box-title">Cliente</h3>
      </div>';

if (!empty($_SESSION['msg'])) {
  echo '<div class="col-xs-12 col-md-12 text-success">' . $_SESSION['msg'] . '</div>';
  unset($_SESSION['msg'], $_SESSION['Cliente'], $_SESSION['cpf'], $_SESSION['Telefone'], $_SESSION['Cidade'], $_SESSION['UF']);
}
?>

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

<div class="row">
  <form id="form1" action="index.php" method="post">
    <div class="box-body">
      <div class="col-lg-6">
        <div class="input-group">
          <input type="text" class="form-control" id="cpfCliente" name="CPF" placeholder="Pesquisar Nome" autocomplete="off">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-floppy-save"></span></button>
          </span>
        </div>
        <div id="Listdata2"></div>
      </div>
    </div>
  </form>
</div>

<form id="form2" action="../../App/Database/insertVendas.php" method="POST">
<div class="box-body">
<div class="form-group row">

    <div class="col-sm-8">
      <label>Nome Cliente</label>
      <input type="text" name="NomeCliente" class="form-control"
      value="<?php if (isset($_SESSION['Cliente'])) echo $_SESSION['Cliente']; ?>" />
    </div>

    <div class="col-sm-3">
      <label>CPF/CNPJ</label>
      <input type="text" name="cpfCliente" class="form-control"
      value="<?php if (isset($_SESSION['cpf'])) echo $_SESSION['cpf']; ?>" />
    </div>
</div>

<div class="form-group row">
  <div class="col-sm-3">
    <label>Telefone</label>
    <input type="text" name="FoneCliente" class="form-control"
    value="<?php if (isset($_SESSION['Telefone'])) echo $_SESSION['Telefone']; ?>" />
  </div>

  <div class="col-sm-3">
    <label>Cidade</label>
    <input type="text" name="Cidade" class="form-control"
    value="<?php if (isset($_SESSION['Cidade'])) echo $_SESSION['Cidade']; ?>" />
  </div>

  <div class="col-sm-1">
    <label>UF</label>
    <input type="text" name="UF" class="form-control"
    value="<?php if (isset($_SESSION['UF'])) echo $_SESSION['UF']; ?>" />
  </div>
</div>

<div class="box">
  <div class="form-group row">
    <div class="col-sm-3">
      <label>Data da Venda</label>
      <input type="datetime-local" name="DataVenda" class="form-control">
    </div>
    <div class="col-sm-3">
      <label>Cod. de Rastreio</label>
      <input type="text" name="CodRastreioV" class="form-control">
    </div>
  </div>
</div>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Lista de Produtos</h3>
  </div>
  <div class="box-body">
    <div class="row">
      <div class="col-sm-2">
        <label>ID do Item</label>
        <input type="number" id="idItem" name="idItem" class="form-control">
      </div>
      <div class="col-sm-2">
        <label>Quant. Item</label>
        <input type="number" id="qtd" name="qtde" class="form-control">
      </div>
      <div class="col-sm-2">
        <label>Taxa Mercado Livre</label>
        <div class="input-group">
          <span class="input-group-addon">R$</span>
          <input type="number" id="TxMl" name="taxa" class="form-control" step="0.01" min="0">
        </div>
      </div>
      <div class="col-sm-2">
        <label>Valor do Frete</label>
        <div class="input-group">
          <span class="input-group-addon">R$</span>
          <input type="number" id="TxFret" name="Frete" class="form-control" step="0.01" min="0">
        </div>
      </div>
      <div class="col-sm-2">
        <label>Venda sem taxa</label>
        <div class="input-group">
          <span class="input-group-addon">R$</span>
          <input type="number" id="Vd_Tax" name="Venda" class="form-control" step="0.01" min="0">
        </div>
      </div>
      <div class="form-group col-xs-12 col-sm-2">
        <label>.</label> 
        <button type="button" id="prodSubmit" onclick="prodSubmit();" class="btn btn-primary col-xs-12">Registrar</button>
      </div>
    </div>

    <table class="table table-bordered" id="products-table">
      <tr>
        <th>#</th>
        <th>Cod.</th>
        <th>Produto</th>
        <th>Qtde</th>
        <th>Taxa ML</th>
        <th>Frete</th>
        <th>Valor Líquido</th>
        <th>Del</th>
      </tr>
      <tbody id="listable">
<?php
$pkCount = isset($_SESSION['itens']) ? count($_SESSION['itens']) : 0;
$TaxaM  = $_POST['taxa']  ?? 0;
$Fretee = $_POST['Frete'] ?? 0;
$Vendaa = $_POST['Venda'] ?? 0;

if ($pkCount == 0) {
  echo '<tr><td colspan="5"><b>Carrinho Vazio</b></td></tr>';
} else {
  $vendas = new Vendas;
  $cont = 1;
  foreach ($_SESSION['itens'] as $produtos => $quantidade) {
    $NomeProduto = $vendas->itemNome($produtos);
    if (!empty($NomeProduto)) {
      echo '<tr>
        <td>'. $cont .'</td>
        <td>'. $produtos .'</td>
        <td>'. $NomeProduto .'</td>
        <td>'. $quantidade .'</td>
        <td>'. $TaxaM .'</td>
        <td>'. $Fretee .'</td>
        <td>'. $Vendaa .'</td>
        <td>
          <input type="hidden" name="idItem['.$produtos.']" value="'.$produtos.'" />
          <input type="hidden" name="qtd['.$produtos.']" value="'.$quantidade.'" />
          <a href="../../App/Database/remover.php?remover=carrinho&id='.$produtos.'"><i class="fa fa-trash text-danger"></i></a>
        </td>
      </tr>';
      $cont++;
    }
  }
}
?>
      </tbody>
    </table>
  </div>
</div>

<div class="box-footer">
  <button class="btn btn-success" type="submit" name="comprar">Vender</button>
  <a class="btn btn-danger" href="../../views/vendatesta">Cancelar</a>
</div>
</form>

<?php
echo '</div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
?>

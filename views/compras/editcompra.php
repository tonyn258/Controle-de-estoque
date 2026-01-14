<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';
require_once '../../App/Models/produto.class.php';

// Permission check
if ($perm != 1) {
    echo "Você não tem permissão! </div>";
    exit();
}

$compras = new Compras;
$produto = new Produto;

$IdCompra = $_GET['id'] ?? null;
$resp = [];

// Fetch existing data
if ($IdCompra) {
    $resp = $compras->EditCompras($IdCompra);
}

// Handle SKU Search for autofill
if (isset($_POST['SKU'])) {
    $resps = $produto->searchdata($_POST["SKU"]);
    if ($resps > 0 && !empty($_POST['SKU'])) {
        foreach ($resps['data'] as $r) {
            $_SESSION['SKU']     = $r['skuProduto'];
            $_SESSION['model']   = $r['model'];
            $_SESSION['Produto'] = $r['NomeProduto'];
        }
    }
    unset($_POST['SKU']);
}

// Prepare variables for view to avoid logic in HTML
$sku = $_SESSION['SKU'] ?? $resp['compras']['SKU'] ?? '';
$model = $_SESSION['model'] ?? $resp['compras']['Modelo'] ?? '';
$nome = $_SESSION['Produto'] ?? $resp['compras']['Nome'] ?? '';
$rastreio = $resp['compras']['Rastreio'] ?? '';
$valor = $resp['compras']['Valor'] ?? '';
$saldo = $resp['compras']['Saldo'] ?? '';

$dataCompra = '';
if (isset($resp['compras']['Data'])) {
    $dataCompra = date('Y-m-d\TH:i', strtotime($resp['compras']['Data']));
}

// Clear session data used for autofill
unset($_SESSION['SKU'], $_SESSION['model'], $_SESSION['Produto']);

echo $head;
echo $header;
echo $aside;
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Editar <small>Produto</small></h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Produto</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php require '../../layout/alert.php'; ?>

        <div class="row">
            <div class="col-md-12">
                <a href="./" class="btn btn-success btn-flat" style="margin-bottom: 15px;">
                    <i class="fa fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <?php if ($IdCompra): ?>
            <div class="row">
                <!-- Search Box -->
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-body">
                            <form id="search-form" action="editcompra.php?id=<?= $IdCompra ?>" method="post">
                                <div class="form-group">
                                    <label>Digite o SKU ou Nome do Produto para preencher</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="skuAnuncio" name="SKU" placeholder="Pesquisar SKU" autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-floppy-save"></i></button>
                                        </span>
                                    </div>
                                    <div id="Listdata"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Dados do Produto</h3>
                        </div>

                        <?php if (!empty($_SESSION['msg'])): ?>
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?= $_SESSION['msg'] ?>
                            </div>
                            <?php unset($_SESSION['msg']); ?>
                        <?php endif; ?>

                        <form role="form" action="../../App/Database/insertCompras.php" method="POST" enctype="multipart/form-data">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>SKU</label>
                                            <input type="text" name="skuAnuncio" class="form-control" placeholder="SKU" value="<?= $sku ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Modelo</label>
                                            <input type="text" name="modelo" class="form-control" placeholder="Modelo" value="<?= $model ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nome do Produto</label>
                                            <input type="text" name="NomeProduto" class="form-control" placeholder="Nome do Produto" value="<?= $nome ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cod. de Rastreio</label>
                                            <input type="text" name="CodRastreio" class="form-control" placeholder="Cod. de Rastreio" value="<?= $rastreio ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Valor da Compra</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">R$</span>
                                                <input type="number" name="ValorCompra" class="form-control" placeholder="Valor" step="0.01" min="0" value="<?= $valor ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Data da Compra</label>
                                            <input type="datetime-local" name="DataCompra" class="form-control" value="<?= $dataCompra ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Estoque</label>
                                            <input type="text" name="QuantItens" class="form-control" placeholder="Quant." value="<?= $saldo ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Adicionar Novas Imagens</label>
                                            <input type="file" name="imagens[]" class="form-control" multiple accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="iduser" value="<?= $idUsuario ?>">
                                <input type="hidden" name="IdCompra" value="<?= $IdCompra ?>">
                                <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">Salvar Alterações</button>
                                <a class="btn btn-danger" href="../../views/compras">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
echo $footer;
echo $javascript;
?>
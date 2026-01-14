<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';

// Lógica de controle do botão Publicados/Inativos
$value = 1;
$public = 0;
$button_name = "Inativos"; // Este botão parece não estar sendo usado na query principal, mantendo lógica original

// Lógica de Filtro de Estoque
$filtro_estoque = isset($_POST['filtro_estoque']) ? $_POST['filtro_estoque'] : 0;

if (isset($_POST['public'])) {
    $value = $_POST['public'];
    $public = ($value == 1) ? 0 : 1;
    $button_name = ($value == 1) ? "Inativos" : "Publicados";
}

// Lógica de ordenação
$order_by = "";
if (isset($_POST['sort_by']) && isset($_POST['sort_order'])) {
    $sort_by = $_POST['sort_by'];
    $sort_order = $_POST['sort_order'];
    $order_by = "ORDER BY $sort_by $sort_order";
}

// Instancia e busca dados
$compras = new Compras;
$resp = $compras->index($perm, $order_by);
$rows = json_decode($resp, true);

echo $head;
echo $header;
echo $aside;
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Gestão de Compras e Estoque</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Compras</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php require '../../layout/alert.php'; ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">Lista de Produtos</h3>
                        
                        <form action="index.php" method="post" style="display:inline-block; margin-left: 20px;">
                            <button name="filtro_estoque" type="submit" value="<?= ($filtro_estoque == 1) ? 0 : 1 ?>" class="btn btn-sm <?= ($filtro_estoque == 1) ? 'btn-warning' : 'btn-default' ?>">
                                <i class="fa fa-filter"></i> <?= ($filtro_estoque == 1) ? 'Exibir Todos' : 'Apenas com Estoque' ?>
                            </button>
                        </form>
                    </div>

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>SKU/Modelo</th>
                                    <th>Nome Produto</th>
                                    <th>Valor Compra</th>
                                    <th>Estoque</th>
                                    <th>Compra</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): ?>
                                    <?php foreach ($rows as $row): 
                                        $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                                        
                                        // Filtro de Estoque
                                        if ($filtro_estoque == 1 && $estoque <= 0) {
                                            continue;
                                        }
                                    ?>
                                        <tr>
                                            <td><?= $row['IdCompra'] ?></td>
                                            <td><?= $row['skuAnuncio'] . ' / ' . $row['model'] ?></td>
                                            <td><?= $row['NomeProduto'] ?></td>
                                            <td>R$ <?= number_format($row['ValorCompra'], 2, ',', '.') ?></td>
                                            <td><?= $estoque ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['DataCompra'])) ?></td>
                                            <td>
                                                <a href="editcompra.php?id=<?= $row['IdCompra'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer clearfix">
                        <form action="index.php" method="post" class="pull-left">
                            <button name="public" type="submit" value="<?= $public ?>" class="btn btn-default">
                                <i class="fa fa-refresh"></i> <?= $button_name ?>
                            </button>
                        </form>
                        <a href="addcompra.php" class="btn btn-success pull-right">
                            <i class="fa fa-plus"></i> Adicionar Produto
                        </a>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
echo $footer;
echo $javascript;
?>

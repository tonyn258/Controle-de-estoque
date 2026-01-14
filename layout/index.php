<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';
require_once '../../App/Models/produto.class.php';

echo $head;
echo $header;
echo $aside;

// Filtro: 1 = Com Estoque (Padrão), 0 = Sem Estoque
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 1;

$compras = new Compras();
$produtoModel = new Produto();

// Busca todas as compras
$resp = $compras->index(1); // Passando 1 apenas para cumprir assinatura, index retorna tudo
$rows = json_decode($resp, true);

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Catálogo de Produtos</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Catálogo</li>
        </ol>
    </section>

    <section class="content">
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-12">
                <a href="index.php?filtro=1" class="btn <?= $filtro == 1 ? 'btn-primary' : 'btn-default' ?>">Com Estoque</a>
                <a href="index.php?filtro=0" class="btn <?= $filtro == 0 ? 'btn-primary' : 'btn-default' ?>">Sem Estoque</a>
            </div>
        </div>

        <div class="row">
            <?php 
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                    
                    // Aplica o filtro
                    if ($filtro == 1 && $estoque <= 0) continue;
                    if ($filtro == 0 && $estoque > 0) continue;

                    // Busca imagem
                    $imagem = 'dist/img/padrao.png'; // Imagem padrão
                    // Tenta buscar idProduto pelo SKU
                    $buscaProd = $produtoModel->searchdata($row['skuAnuncio']);
                    if ($buscaProd && isset($buscaProd['data'][0]['idProduto'])) {
                        $idProduto = $buscaProd['data'][0]['idProduto'];
                        // Query direta para pegar a imagem (idealmente estaria no model, mas simplificando para a view)
                        $queryImg = "SELECT caminhoImagem FROM produto_imagem WHERE idProduto = '$idProduto' ORDER BY ordem LIMIT 1";
                        $resImg = mysqli_query($produtoModel->SQL, $queryImg);
                        if ($resImg && $imgRow = mysqli_fetch_assoc($resImg)) {
                            $imagem = $imgRow['caminhoImagem'];
                        }
                    }
            ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="box box-solid">
                    <div class="box-body text-center">
                        <div style="height: 200px; overflow: hidden; margin-bottom: 10px;">
                            <img src="../../views/<?= $imagem ?>" alt="<?= $row['NomeProduto'] ?>" style="max-width: 100%; max-height: 100%;">
                        </div>
                        <h4 class="text-truncate" title="<?= $row['NomeProduto'] ?>"><?= $row['NomeProduto'] ?></h4>
                        <p class="text-muted"><?= $row['skuAnuncio'] ?></p>
                        <h3 class="text-green">R$ <?= number_format($row['ValorVenda'] ?? $row['ValorCompra'] * 1.5, 2, ',', '.') ?></h3> <!-- ValorVenda ou Simulação -->
                        
                        <?php if ($estoque > 0): ?>
                            <span class="label label-success">Em Estoque: <?= $estoque ?></span>
                        <?php else: ?>
                            <span class="label label-danger">Esgotado</span>
                        <?php endif; ?>
                        
                        <div style="margin-top: 15px;">
                            <a href="../compras/editcompra.php?id=<?= $row['IdCompra'] ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> Editar</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="col-md-12"><div class="alert alert-info">Nenhum produto encontrado.</div></div>';
            }
            ?>
        </div>
    </section>
</div>

<style>
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<?php
echo $footer;
echo $javascript;
?>
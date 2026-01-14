<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/produto.class.php';

// Lógica de Processamento
$produto = new Produto;
$resp = $produto->index();
$rows = json_decode($resp, true);

echo $head;
echo $header;
echo $aside;
?>

<div class="content-wrapper">
  <!-- Cabeçalho da Página -->
  <section class="content-header">
    <h1>Produtos <small>Gerenciamento</small></h1>
    <ol class="breadcrumb">
      <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Produtos</li>
    </ol>
  </section>

  <!-- Conteúdo Principal -->
  <section class="content">
    <?php require '../../layout/alert.php'; ?>

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Produtos</h3>
        <div class="box-tools pull-right">
          <a href="addproduto.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Novo Produto
          </a>
        </div>
      </div>

      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th style="width: 50px">#</th>
              <th>SKU</th>
              <th>Modelo</th>
              <th>Nome do Produto</th>
              <th>Conexão</th>
              <th>Marca</th>
              <th>Status</th>
              <th style="width: 60px" class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (is_array($rows)) {
              foreach ($rows as $row) {
                if (isset($row['idProduto'])) {
                  echo '<tr>';
                  echo '<td>' . $row['idProduto'] . '</td>';
                  echo '<td>' . ($row['skuProduto'] ?? '') . '</td>';
                  echo '<td>' . ($row['modelo'] ?? '') . '</td>';
                  echo '<td>' . ($row['nomeProduto'] ?? '') . '</td>';
                  echo '<td>' . ($row['Conexao'] ?? '') . '</td>';
                  echo '<td>' . ($row['marca'] ?? '') . '</td>';
                  echo '<td>' . ($row['statusProduto'] ?? '') . '</td>';
                  echo '<td class="text-center">
                          <a href="addproduto.php?id=' . $row['idProduto'] . '" class="btn btn-primary btn-xs" title="Editar"><i class="fa fa-edit"></i></a>
                        </td>';
                  echo '</tr>';
                }
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
<?php
echo $footer;
echo $javascript;
?>

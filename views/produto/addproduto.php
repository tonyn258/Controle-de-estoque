<?php
  require_once '../../App/auth.php';
  require_once '../../layout/script.php';
  require_once '../../App/Models/produto.class.php';

  // Lógica de Permissão
  if ($perm != 1) {
    echo '<div class="content-wrapper"><section class="content"><div class="alert alert-danger">Você não tem permissão!</div></section></div>';
    echo $footer;
    echo $javascript;
    exit();
  }

  // Inicialização de Variáveis
  $idProduto = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
  $isEdit = false;
  $produto = new Produto;
  
  // Valores padrão (vazios)
  $sku = '';
  $modelo = '';
  $nome = '';
  $quantidade = '';
  $conexaoVal = '';
  $marca = '';

  // Se for edição, busca os dados
  if ($idProduto) {
      $resp = $produto->EditProduto($idProduto);
      
      // Verifica se retornou dados (estrutura baseada no seu editproduto.php)
      if (isset($resp['produto'])) {
          $isEdit = true;
          $data = $resp['produto'];
          
          $sku = $data['SKU'] ?? '';
          $modelo = $data['modelo'] ?? '';
          $nome = $data['NomeProduto'] ?? '';
          $quantidade = $data['Quantidade'] ?? '';
          $conexaoVal = $data['Conexao'] ?? '';
          $marca = $data['Marca'] ?? '';
      }
  }

  echo $head;
  echo $header;
  echo $aside;
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $isEdit ? 'Editar' : 'Adicionar'; ?> <small>Produto</small></h1>
    <ol class="breadcrumb">
      <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="index.php">Produtos</a></li>
      <li class="active"><?php echo $isEdit ? 'Editar' : 'Novo'; ?></li>
    </ol>
  </section>

  <section class="content">
    <?php require '../../layout/alert.php'; ?>

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Dados do Produto</h3>
        <div class="box-tools pull-right">
          <a href="index.php" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> Voltar</a>
        </div>
      </div>

      <form role="form" action="../../App/Database/InsertProduto.php" method="POST">
        <div class="box-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="skuProduto">SKU</label>
                <input type="text" name="skuProduto" class="form-control" id="skuProduto" placeholder="Ex: PROD-001" value="<?php echo $sku; ?>">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="modelo">Modelo/Variação</label>
                <input type="text" name="modelo" class="form-control" id="modelo" placeholder="Ex: V1" value="<?php echo $modelo; ?>">
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="NomeProduto">Nome do Produto</label>
                <input type="text" name="NomeProduto" class="form-control" id="NomeProduto" placeholder="Nome completo do produto" value="<?php echo $nome; ?>" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="Quantidade">Quantidade (EAN)</label>
                <input type="number" name="Quantidade" class="form-control" id="Quantidade" placeholder="0" value="<?php echo $quantidade; ?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="Conexao">Categoria / Conexão</label>
                <select name="Conexao" id="Conexao" class="form-control" required>
                  <option value="">Selecione...</option>
                  <?php
                  $queryCategoria = "SELECT `idCategoria`, `nomeCategoria`, `statusCategoria`, `dataCadastro` FROM `categoria_produto` WHERE 1";
                  $resCat = mysqli_query($produto->SQL, $queryCategoria);
                  if ($resCat) {
                      while ($rowCat = mysqli_fetch_assoc($resCat)) {
                          $selected = ($conexaoVal == $rowCat['idCategoria']) ? 'selected' : '';
                          echo '<option value="' . $rowCat['idCategoria'] . '" ' . $selected . '>' . $rowCat['nomeCategoria'] . '</option>';
                      }
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="Marca">Marca</label>
                <input type="text" name="Marca" class="form-control" id="Marca" placeholder="Marca do produto" value="<?php echo $marca; ?>">
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer">
          <input type="hidden" name="iduser" value="<?php echo $idUsuario; ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="idProduto" value="<?php echo $idProduto; ?>">
          <?php endif; ?>
          
          <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">
            <i class="fa fa-save"></i> <?php echo $isEdit ? 'Salvar Alterações' : 'Cadastrar Produto'; ?>
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
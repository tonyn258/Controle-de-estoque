<?php
require_once '../../App/auth.php'; // inclui o arquivo auth.php que verifica se o usuário está autenticado
require_once '../../layout/script.php'; // inclui o arquivo script.php que contém o cabeçalho HTML e os scripts necessários
require_once '../../App/Models/compras.class.php'; // inclui o arquivo compras.class.php que contém a classe Compras


// Imprime o cabeçalho HTML, o cabeçalho da página, a barra lateral e abre a seção do conteúdo
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Compras Realizadas</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Compras</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">';

require '../../layout/alert.php'; // inclui o arquivo alert.php que contém a estrutura de alerta para exibir mensagens ao usuário

echo '
  <!-- Small boxes (Stat box) -->
  <div class="row">
   <div class="box box-primary">
    
    <!-- /.box-header -->

    <style>
    .dot {
      display: inline-block;
      height: 12px;
      width: 12px;
      border-radius: 50%;
      margin-left: 10px;
      
    }
    .Orange-dot {
      background-color: Orange;
    }
    .green-dot {
      background-color: green;
    }
  </style>
    
    <div class="box-body">
    
    <!-- Formulário para o filtro -->
    <form action="" method="post">
      <button name="filtro_estoque" type="submit" value="' . (isset($_POST['filtro_estoque']) && $_POST['filtro_estoque'] == 1 ? 0 : 1) . '" class="btn btn-default">
        ' . (isset($_POST['filtro_estoque']) && $_POST['filtro_estoque'] == 1 ? 'Maior que 0' : 'Exibir Geral') . '
      </button>
    </form>
    <br/>
      
    <!-- Inicio da Tabela -->
    <table id="example1" class="table table-bordered table-striped dataTable " role="grid" 
    aria-describedby="example1_info">
    <thead>
    <tr>
        <th>#</t>
        <th>SKU/Modelo</t>             
        <th>Nome Produto</t>               
        <th>Valor Compra</t>
        <th>Estoque</t>        
        <th>Edit</t> 
              
                
      </tr>
      </thead>
      <tbody>    
    ';


// Instancia um novo objeto da classe Compras
$compras = new Compras;
// Verifica se o usuário selecionou algum critério de ordenação na tabela
if (isset($_POST['sort_by']) && isset($_POST['sort_order'])) {
  $sort_by = $_POST['sort_by'];
  $sort_order = $_POST['sort_order'];
  $order_by = "ORDER BY $sort_by $sort_order";
} else {
  // Define a ordenação padrão por ID em ordem decrescente
  $order_by = "ORDER BY IdCompra DESC";
}
// Chama o método index da classe Compras, passando as permissões do usuário e o critério de ordenação
$resp =  $compras->index($perm, $order_by);
// Decodifica a resposta JSON obtida do método index da classe Compras
$resps = json_decode($resp, true);
// Verifica se o filtro de estoque está ativo
$filtro_estoque_ativo = isset($_POST['filtro_estoque']) && $_POST['filtro_estoque'] == 1;

$total_estoque = 0;
$produto_pesquisa = isset($_POST['produto_pesquisa']) ? $_POST['produto_pesquisa'] : '';

// Percorre o array $resps e imprime as linhas da tabela
foreach ($resps as $row) {
  $estoque = $row['QuantItens'] - $row['QuantItensVend'];
  if ((!$filtro_estoque_ativo || $estoque > 0) && (empty($produto_pesquisa) || stripos($row['NomeProduto'], $produto_pesquisa) !== false)) { // Verifica se o estoque é maior que 0
    echo '<tr>';
    echo '<td>' . $row['IdCompra'] . '</td>';
    echo '<td>' . $row['skuProduto'] . ' / ' . $row['model'] . '</td>';
    echo '<td>' . $row['NomeProduto'] . '</td>';
    echo '<td>R$ ' . number_format($row['ValorCompra'], 2, ',', '.') . '</td>';
    echo '<td>' . $estoque . '</td>';


    echo '<td> <a href="editestoque.php?id=' . $row['IdCompra'] . '"<i class="fa fa-edit"></i></a></td>';
    echo '</tr>';
  }
}



echo '<ul class="todo-list not-done">';
if (isset($_POST['public']) != NULL) {

  $value = $_POST['public'];
  if ($value == 1) {

    $public = 0;
    $button_name = "Inativos";
  } else {
    $public = 1;
    $button_name = "Publicados";
  }
} else {
  $value = 0; // Alteração: Definir o valor padrão como 0 (desmarcado)
  $public = 0;
  $button_name = "Inativos";
}
$compras->index($value);

echo '</ul>
                        </tbody>
                    </table>
                    <!-- Fim da tabela -->
                    <br/>
                    <!-- /.box-body -->
                    <div class="left">
                    <form action="index.php" method="post">                             
                        <a href="addcompra.php" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Produto</a>
                    </div>
                </div>
            </div>
        </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;

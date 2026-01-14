<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/sales.class.php';
require_once '../../App/Models/cliente.class.php';
require_once '../../App/Models/connect.php'; // Garantir conexão para formatação

// --- LÓGICA PHP ---

// 1. Processamento da Busca de Cliente
if (isset($_POST['CPF'])) {
    $clienteModel = new Cliente();
    $cpf = filter_input(INPUT_POST, 'CPF', FILTER_SANITIZE_STRING);
    
    // Tenta busca ampla (Nome ou CPF parcial)
    $resps = $clienteModel->search($cpf);
    $clienteEncontrado = null;

    if (is_array($resps) && isset($resps['data2']) && count($resps['data2']) > 0) {
        $clienteEncontrado = $resps['data2'][0];
    } else {
        // Tenta busca específica de CPF (limpa pontuação)
        $resps = $clienteModel->searchdata($cpf);
        if (is_array($resps) && isset($resps['data']) && count($resps['data']) > 0) {
            $clienteEncontrado = $resps['data'][0];
        }
    }

    if ($clienteEncontrado) {
        $_SESSION['Cliente']  = $clienteEncontrado['NomeCliente'];
        $_SESSION['cpf']      = $clienteEncontrado['cpfCliente'];
        $_SESSION['Cep'] = $clienteEncontrado['CepCliente'];
    } else {
        $_SESSION['msg'] = "Cliente não encontrado.";
        unset($_SESSION['Cliente'], $_SESSION['cpf'], $_SESSION['Cep']);
    }
    unset($_POST['CPF']);
}

if (isset($_GET['clear'])) {
    unset($_SESSION['Cliente'], $_SESSION['cpf'], $_SESSION['Cep'], $_SESSION['msg']);
    header('Location: index.php');
    exit();
}

// 2. Tratamento de Mensagens de Sessão
$msg = '';
if (!empty($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// 3. Lógica do Carrinho de Compras
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar Item
if (isset($_POST['add_item'])) {
    $id = $_POST['prod_id'];
    $sku = $_POST['prod_sku'];
    $nome = $_POST['prod_nome'];
    $qtd = (int)$_POST['prod_qtd'];
    $preco = (float)str_replace(',', '.', $_POST['prod_preco']);

    // Verifica se já existe para somar quantidade (opcional, aqui cria nova linha)
    $item = [
        'id' => $id,
        'sku' => $sku,
        'nome' => $nome,
        'qtd' => $qtd,
        'preco' => $preco,
        'subtotal' => $qtd * $preco
    ];
    
    $_SESSION['carrinho'][] = $item;
    
    // Limpa POST para evitar reenvio
    header("Location: index.php");
    exit;
}

// Remover Item
if (isset($_GET['remove_item'])) {
    $index = $_GET['remove_item'];
    if (isset($_SESSION['carrinho'][$index])) {
        unset($_SESSION['carrinho'][$index]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar array
    }
    header("Location: index.php");
    exit;
}

$totalVenda = 0;

// --- INÍCIO DO HTML ---
echo $head;
echo $header;
echo $aside;
?>
<!-- Incluindo CSS do jQuery UI para formatar a lista de sugestões corretamente -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .ui-autocomplete {
        z-index: 9999 !important;
    }
    /* Configuração para a lista ficar fixa dentro do container #listaSugestoes */
    #listaSugestoes .ui-autocomplete, #listaSugestoesProduto .ui-autocomplete {
        position: static !important; /* Remove o posicionamento absoluto/flutuante */
        top: auto !important;
        left: auto !important;
        width: 100% !important; /* Ocupa toda a largura do container */
    }
    /* Estilos do Carrinho PDV */
    .pos-box {
        background: #f9fafc;
        border: 1px solid #d2d6de;
        padding: 15px;
        border-radius: 3px;
    }
    .total-display {
        font-size: 2em;
        font-weight: bold;
        color: #00a65a;
        text-align: right;
    }
</style>

<div class="content-wrapper">
    <!-- Cabeçalho da Página -->
    <section class="content-header">
        <h1>Nova Venda <small>Lançamento de Pedido</small></h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Vendas</li>
        </ol>
    </section>

    <!-- Conteúdo Principal -->
    <section class="content">
        
        <?php require '../../layout/alert.php'; ?>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fa fa-info-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- COLUNA DA ESQUERDA: Busca de Cliente e Adição de Produtos -->
            <div class="col-md-5">
                <div class="box box-primary">
            <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user-plus"></i> 1. Identificar Cliente</h3>
            </div>
            <div class="box-body">
                        <!-- Busca Cliente -->
                        <form id="searchClientForm" action="index.php" method="post">
                            <div class="form-group">
                                <label>Buscar Cliente (Nome ou CPF)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cpfClienteSearch" name="CPF" placeholder="Digite para buscar..." autocomplete="off">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                        </form>
                        <div id="listaSugestoes" style="margin-bottom: 10px;"></div>
                    </div>
                </div>

                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-cart-plus"></i> 2. Adicionar Produto</h3>
                    </div>
                    <div class="box-body">
                        <form action="index.php" method="POST" id="formAddItem">
                            <input type="hidden" name="add_item" value="1">
                            <input type="hidden" id="prod_id" name="prod_id">
                            <input type="hidden" id="prod_sku" name="prod_sku">
                            
                            <div class="form-group">
                                <label>Buscar Produto (SKU ou Nome)</label>
                                <input type="text" id="productSearch" name="prod_nome" class="form-control input-lg" placeholder="Digite SKU ou Nome..." required autocomplete="off">
                            </div>
                            <div id="listaSugestoesProduto" style="margin-bottom: 10px;"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Quantidade</label>
                                        <input type="number" id="prod_qtd" name="prod_qtd" class="form-control input-lg" value="1" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Valor Unit. (R$)</label>
                                        <input type="text" id="prod_preco" name="prod_preco" class="form-control input-lg" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> ADICIONAR AO CARRINHO</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUNA DA DIREITA: Carrinho e Finalização -->
            <div class="col-md-7">
                <form id="salesForm" action="../../App/Database/insertSales.php" method="POST">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Carrinho de Compras</h3>
                            <div class="box-tools pull-right">
                                <a href="index.php?clear=1" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Limpar Tudo</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="well well-sm" style="margin-bottom: 0;">
                                <strong>Cliente:</strong> <span id="clienteNomeDisplay"><?php echo $_SESSION['Cliente'] ?? 'Não selecionado'; ?></span><br>
                                <strong>CPF:</strong> <span id="clienteCpfDisplay"><?php echo $_SESSION['cpf'] ?? '-'; ?></span><br>
                                <strong>CEP:</strong> <span id="clienteCepDisplay"><?php echo $_SESSION['Cep'] ?? '-'; ?></span>
                            </div>
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Produto</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-right">Preço</th>
                                        <th class="text-right">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($_SESSION['carrinho'])): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted" style="padding: 20px;">Nenhum item adicionado.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($_SESSION['carrinho'] as $key => $item): 
                                            $totalVenda += $item['subtotal'];
                                        ?>
                                            <tr>
                                                <td><?php echo $item['sku']; ?></td>
                                                <td><?php echo $item['nome']; ?></td>
                                                <td class="text-center"><?php echo $item['qtd']; ?></td>
                                                <td class="text-right">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                                <td class="text-right"><strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong></td>
                                                <td class="text-center">
                                                    <a href="index.php?remove_item=<?php echo $key; ?>" class="text-danger"><i class="fa fa-times"></i></a>
                                                    
                                                    <!-- Inputs Ocultos para Envio ao insertSales.php -->
                                                    <input type="hidden" name="idItem[]" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="qtd[]" value="<?php echo $item['qtd']; ?>">
                                                    <input type="hidden" name="Vd_Tax_Array[]" value="<?php echo $item['preco']; ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Data da Venda</label>
                                        <input type="datetime-local" name="DataVenda" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Cód. Rastreio (Opcional)</label>
                                        <input type="text" name="CodRastreioV" class="form-control" placeholder="Código de Rastreio">
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p>Total da Venda:</p>
                                    <div class="total-display">R$ <?php echo number_format($totalVenda, 2, ',', '.'); ?></div>
                                </div>
                            </div>
                            
                            <!-- Campos ocultos do cliente para manter compatibilidade -->
                            <input type="hidden" name="NomeCliente" value="<?php echo $_SESSION['Cliente'] ?? ''; ?>">
                            <input type="hidden" name="cpfCliente" value="<?php echo $_SESSION['cpf'] ?? ''; ?>">
                            <input type="hidden" name="CepCliente" value="<?php echo $_SESSION['Cep'] ?? ''; ?>">
                            <!-- Campos extras exigidos pelo insertSales antigo -->
                            <input type="hidden" name="Vd_Tax" value="0"> <!-- Dummy, usamos o array agora -->

                            <hr>
                            <button type="submit" class="btn btn-success btn-block btn-lg" <?php echo empty($_SESSION['carrinho']) ? 'disabled' : ''; ?>>
                                <i class="fa fa-check"></i> FINALIZAR PEDIDO
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php
echo $footer;
echo $javascript;
?>
<script>
  $(function() {
    $("#cpfClienteSearch").autocomplete({
      appendTo: "#listaSugestoes", // Define que a lista será criada dentro da div #listaSugestoes
      source: "search_client.php",
      minLength: 2, // Começa a buscar após 2 caracteres
      select: function(event, ui) {
        // Preenche os campos do formulário com os dados retornados
        // Submete o formulário de busca para salvar os dados na SESSÃO PHP.
        // Isso garante que os dados do cliente persistam ao adicionar produtos (recarregar página).
        $("#cpfClienteSearch").val(ui.item.cpf);
        $("#searchClientForm").submit();
        return false;
      }
    });
  });

  // Autocomplete de Produtos
  $(function() {
    $("#productSearch").autocomplete({
      appendTo: "#listaSugestoesProduto",
      source: "search_product.php",
      minLength: 1,
      select: function(event, ui) {
        // Preenche os campos ocultos e visíveis
        $('#prod_id').val(ui.item.id);
        $('#prod_sku').val(ui.item.sku);
        $('#productSearch').val(ui.item.nome); // Mostra o nome no campo de busca
        $('#prod_preco').val(ui.item.preco);
        
        // Foca na quantidade para agilizar
        $('#prod_qtd').focus();
        $('#prod_qtd').select();
        
        return false; // Evita que o valor padrão do autocomplete substitua o nosso
      }
    });
  });
</script>
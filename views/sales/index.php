<?php
require_once '../../App/auth.php';
require_once '../../App/Models/connect.php';
date_default_timezone_set('America/Sao_Paulo');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Venda (Carrinho)</title>
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --radius: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; color: #333; }
        
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; margin-bottom: 20px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); }
        .btn-back { text-decoration: none; color: var(--secondary); font-weight: 500; }
        
        /* Form Styles */
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; flex: 1; }
        .form-group.small { flex: 0 0 100px; }
        
        label { font-weight: 600; margin-bottom: 5px; color: #555; font-size: 0.9rem; }
        input, select { padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; width: 100%; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; font-size: 1rem; }
        .btn-primary { background-color: var(--primary); }
        .btn-success { background-color: var(--success); }
        .btn-danger { background-color: var(--danger); }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: var(--dark); }
        
        /* Autocomplete UI */
        .ui-autocomplete { background: white; border: 1px solid #ddd; list-style: none; padding: 0; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; }
        .ui-menu-item { padding: 10px; cursor: pointer; }
        .ui-menu-item:hover { background-color: #f1f1f1; }
        .ui-helper-hidden-accessible { display: none; }
    </style>
    
    <!-- jQuery & jQuery UI (Required for Autocomplete) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Nova Venda (Carrinho)</h1>
            <a href="../index.php" class="btn-back">🏠 Voltar ao Início</a>
        </div>

        <!-- Mensagens de Erro/Sucesso -->
        <?php
        if(isset($_SESSION['msg'])){
            echo '<div style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 15px; border-radius: 4px;">'.$_SESSION['msg'].'</div>';
            unset($_SESSION['msg']);
        }
        ?>

        <!-- Formulário de Adição de Produto -->
        <form action="index.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar Produto (Nome ou SKU)</label>
                    <input type="text" id="searchProduct" placeholder="Digite para buscar..." required>
                    <input type="hidden" name="idItem" id="idItem">
                    <input type="hidden" name="prodSubmit" value="carrinho">
                </div>
                <div class="form-group small">
                    <label>Qtd</label>
                    <input type="number" name="qtd" value="1" min="1" required>
                </div>
                <!-- Campos extras que o carrinho.php pode esperar -->
                <input type="hidden" name="taxa" value="0">
                <input type="hidden" name="Frete" value="0">
                <div class="form-group small">
                    <label>Valor Unit.</label>
                    <input type="text" name="Venda" id="Venda" value="0,00" required>
                </div>
                
                <div class="form-group small">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Formulário Final de Venda -->
    <form action="../../App/Database/insertSales.php" method="POST">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <h3 style="margin:0;">Itens no Carrinho</h3>
                <a href="../../App/Database/remover.php?limpar=tudo" style="color: #6c757d; text-decoration: none; font-size: 0.9rem;" onclick="return confirm('Tem certeza que deseja limpar todo o carrinho?');">🗑️ Limpar carrinho</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Inclui o carrinho que processa a adição e exibe as linhas
                        require_once '../../App/Database/carrinho.php'; 
                    ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Dados do Cliente e Entrega</h3>
            
            <!-- Busca de Cliente -->
            <div class="form-group" id="box-search-client">
                <label>Buscar Cliente</label>
                <input type="text" id="searchClient" placeholder="Digite o nome ou CPF...">
            </div>

            <!-- Cliente Selecionado (Texto) -->
            <div id="box-selected-client" style="display:none; background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size: 1.1rem; color: #333;">
                        <strong id="txtNome"></strong> - CPF: <span id="txtCPF"></span> - CEP: <span id="txtCEP"></span>
                    </span>
                    <button type="button" id="btnRemoveClient" style="border:none; background:transparent; color:#dc3545; cursor:pointer; font-weight:bold;">Trocar</button>
                </div>
            </div>

            <!-- Inputs Ocultos -->
            <input type="hidden" name="NomeCliente" id="hNomeCliente">
            <input type="hidden" name="cpfCliente" id="hCpfCliente">
            <input type="hidden" name="CepCliente" id="hCepCliente">

            <div class="form-row">
                <div class="form-group">
                    <label>Data Venda</label>
                    <input type="date" name="DataVenda" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Código Rastreio</label>
                    <input type="text" name="CodRastreioV">
                </div>
            </div>
            
            <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 10px;">Finalizar Venda</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Autocomplete Produto
        $("#searchProduct").autocomplete({
            source: "search_product.php",
            minLength: 2,
            select: function(event, ui) {
                $("#idItem").val(ui.item.id);
                // Atualizar campo de preço oculto para o carrinho
                $("input[name='Venda']").val(ui.item.preco); 
            }
        });

        // Autocomplete Cliente
        $("#searchClient").autocomplete({
            source: "search_client.php",
            minLength: 2,
            select: function(event, ui) {
                // Preenche inputs ocultos
                $("#hNomeCliente").val(ui.item.nome);
                $("#hCpfCliente").val(ui.item.cpf);
                $("#hCepCliente").val(ui.item.cep);

                // Preenche texto visual
                $("#txtNome").text(ui.item.nome);
                $("#txtCPF").text(ui.item.cpf);
                $("#txtCEP").text(ui.item.cep);

                // Alterna visualização
                $("#box-search-client").hide();
                $("#box-selected-client").show();
                
                $(this).val(""); // Limpa busca
                return false;
            }
        });

        // Botão Trocar Cliente
        $("#btnRemoveClient").click(function(){
            $("#hNomeCliente").val("");
            $("#hCpfCliente").val("");
            $("#hCepCliente").val("");
            
            $("#box-selected-client").hide();
            $("#box-search-client").show();
            $("#searchClient").focus();
        });
    });
</script>

</body>
</html>
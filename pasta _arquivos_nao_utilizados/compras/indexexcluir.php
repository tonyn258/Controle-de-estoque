<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';

$compras = new Compras;
$idUsuario = $_SESSION['idUsuario'];

// --- Lógica AJAX para Filtros ---
if (isset($_GET['action']) && $_GET['action'] == 'search') {
    $busca = $_GET['busca'] ?? '';
    $status = $_GET['status'] ?? '';
    $categoria = $_GET['categoria'] ?? '';
    $data_inicio = $_GET['data_inicio'] ?? '';
    $data_fim = $_GET['data_fim'] ?? '';

    $rows = $compras->filtrar($idUsuario, $busca, $status, $categoria, $data_inicio, $data_fim);
    
    // Retorna JSON para o JavaScript
    header('Content-Type: application/json');
    echo json_encode($rows);
    exit;
}

// --- Renderização da Página ---

echo $head;
echo $header;
echo $aside;

// Busca categorias para o filtro
$categorias = $compras->getCategorias();
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 15px 0 15px;">
        <h1 style="margin: 0; font-size: 24px;">
            Gestão de Compras e Estoque
            <small>Controle de produtos</small>
        </h1>
        <div class="header-actions">
            <a href="../" class="btn btn-default btn-flat"><i class="fa fa-home"></i> Home</a>
            <a href="addcompra.php" class="btn btn-primary btn-flat"><i class="fa fa-plus"></i> Nova Compra</a>
            <a href="../produto/" class="btn btn-success btn-flat"><i class="fa fa-box"></i> Cadastrar Produto</a>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php require '../../layout/alert.php'; ?>

        <div class="box box-primary" style="border-top-width: 3px;">
            <div class="box-body">
                
                <!-- Barra de Filtros -->
                <div class="row" style="margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; margin-left: 0; margin-right: 0;">
                    <div class="col-md-12" style="margin-bottom: 15px;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input type="text" id="busca" class="form-control input-lg" placeholder="Buscar por SKU, nome do produto ou modelo...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="com_estoque">Com Estoque</option>
                            <option value="sem_estoque">Sem Estoque</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Categoria</label>
                        <select id="categoria" class="form-control">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['idCategoria'] ?? $cat['id'] ?>"><?= $cat['NomeCategoria'] ?? $cat['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Data Inicial</label>
                        <input type="date" id="data_inicio" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Data Final</label>
                        <input type="date" id="data_fim" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button id="btnFiltrar" class="btn btn-primary btn-block btn-flat"><i class="fa fa-filter"></i> Filtrar</button>
                        <button id="btnLimpar" class="btn btn-default btn-block btn-flat btn-xs" style="margin-top: 5px;">Limpar Filtros</button>
                    </div>
                </div>

                <!-- Tabela de Dados -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead style="background-color: #3c8dbc; color: white;">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>SKU / Modelo</th>
                                <th>Nome do Produto</th>
                                <th>Categoria</th>
                                <th>Valor Compra</th>
                                <th>Estoque</th>
                                <th>Data Compra</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaDados">
                            <!-- Dados carregados via AJAX -->
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </section>
</div>

<?php
echo $footer;
echo $javascript;
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Carrega dados iniciais
    carregarDados();

    // Eventos dos botões
    document.getElementById('btnFiltrar').addEventListener('click', carregarDados);
    
    document.getElementById('btnLimpar').addEventListener('click', function() {
        document.getElementById('busca').value = '';
        document.getElementById('status').value = '';
        document.getElementById('categoria').value = '';
        document.getElementById('data_inicio').value = '';
        document.getElementById('data_fim').value = '';
        carregarDados();
    });

    // Busca ao pressionar Enter
    document.getElementById('busca').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') carregarDados();
    });
});

function carregarDados() {
    const busca = document.getElementById('busca').value;
    const status = document.getElementById('status').value;
    const categoria = document.getElementById('categoria').value;
    const data_inicio = document.getElementById('data_inicio').value;
    const data_fim = document.getElementById('data_fim').value;

    const params = new URLSearchParams({
        action: 'search',
        busca: busca,
        status: status,
        categoria: categoria,
        data_inicio: data_inicio,
        data_fim: data_fim
    });

    const tbody = document.getElementById('tabelaDados');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Carregando dados...</td></tr>';

    fetch('index.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center" style="padding: 20px;">Nenhum registro encontrado.</td></tr>';
                return;
            }

            data.forEach(row => {
                const estoque = (parseInt(row.QuantItens) || 0) - (parseInt(row.QuantItensVend) || 0);
                
                let statusLabel = '';
                if (estoque > 0) {
                    statusLabel = '<span class="label label-success">Em estoque</span>';
                } else {
                    statusLabel = '<span class="label label-danger">Sem estoque</span>';
                }
                
                // Formatação de Data
                let dateStr = '-';
                if(row.DataCompra) {
                    const dateObj = new Date(row.DataCompra);
                    dateStr = dateObj.toLocaleDateString('pt-BR');
                }

                // Formatação de Moeda
                const valor = parseFloat(row.ValorCompra).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.IdCompra}</td>
                    <td><strong>${row.skuAnuncio || ''}</strong> <br> <small class="text-muted">${row.model || ''}</small></td>
                    <td>${row.NomeProduto}</td>
                    <td>${row.idCategoria || '-'}</td>
                    <td>${valor}</td>
                    <td><strong>${estoque}</strong></td>
                    <td>${dateStr}</td>
                    <td>${statusLabel}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="editcompra.php?id=${row.IdCompra}" class="btn btn-default btn-sm" title="Editar"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-default btn-sm" title="Visualizar"><i class="fa fa-eye"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erro:', error);
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Erro ao carregar dados. Verifique o console.</td></tr>';
        });
}
</script>

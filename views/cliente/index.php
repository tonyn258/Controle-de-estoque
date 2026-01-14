<?php
require_once '../../App/auth.php';
require_once '../../App/Models/cliente.class.php';

// Lógica para buscar dados
$value = "";
$resp = $cliente->indexCliente($value, $perm);
$resps = json_decode($resp, true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema de Vendas</title>
    
 <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        .nav-menu { text-align: center; margin-bottom: 20px; }
        .nav-menu a { display: inline-block; padding: 10px 20px; margin: 0 5px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .nav-menu a:hover { background: #0056b3; }
        .search-box { margin-bottom: 20px; }
        .search-box input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .btn { padding: 5px 10px; margin: 2px; text-decoration: none; border-radius: 3px; font-size: 12px; }
        .btn-edit { background: #28a745; color: white; }
        .btn-edit:hover { background: #218838; }
        .actions { text-align: center; }
    </style>


</head>
<body>

<div class="container">
    <div class="page-header">
        <h1>Lista de Clientes</h1>
        <div class="nav-menu">
            <a href="../index.php" class="btn btn-secondary">🏠 Menu</a>
            <a href="../sales/" class="btn btn-secondary">🛒 Nova Venda</a>
            <a href="addcliente.php" class="btn btn-secondary">👤 Cadastrar Cliente</a>
        </div>
    </div>

    <div class="search-box">
        <input type="text" id="search" placeholder="Buscar por nome, CPF ou telefone...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome Cliente</th>                    
                    <th>CPF</th>
                    <th>Cep</th>
                    <th>Status</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resps) {
                    foreach ($resps as $row) {
                        if (isset($row['idCliente'])) {
                            echo '<tr>';
                            echo '<td>' . $row['idCliente'] . '</td>';
                            echo '<td>' . $row['NomeCliente'] . '</td>';
                            echo '<td>' . $row['cpfCliente'] . '</td>';
                            echo '<td>' . $row['CepCliente'] . '</td>';                            
                            echo '<td>' . ($row['statusCliente'] == 1 ? 'Ativo' : 'Inativo') . '</td>';
                            echo '<td style="text-align: center;">
                                    <a href="editcliente.php?id=' . $row['idCliente'] . '" class="btn btn-edit" style="padding: 5px 10px; font-size: 0.8rem;">Editar</a>
                                  </td>';
                            echo '</tr>';
                        }
                    }
                } else {
                    echo '<tr><td colspan="6" style="text-align:center;">Nenhum cliente encontrado.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var searchValue = this.value.toLowerCase();
        var tableRows = document.querySelectorAll('table tbody tr');

        tableRows.forEach(function(row) {
            var rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
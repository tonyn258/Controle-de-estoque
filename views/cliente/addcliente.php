<?php
require_once '../../App/auth.php';
require_once '../../App/Models/cliente.class.php';

if ($perm != 1) {
  echo "Você não tem permissão!";
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Cliente</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; }
        
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); }
        .btn-back { text-decoration: none; color: var(--secondary); font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        .alert { padding: 15px; border-radius: var(--radius); margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .alert-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        input[type="text"], input[type="number"], select, textarea {
            padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; }
        
        .btn-submit {
            grid-column: 1 / -1; background-color: var(--primary); color: white; border: none; padding: 15px;
            font-size: 1.1rem; font-weight: bold; border-radius: var(--radius); cursor: pointer; margin-top: 10px;
        }
        .btn-submit:hover { background-color: #0056b3; }

        @media (max-width: 768px) {
            form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Adicionar Cliente</h1>
            <a href="index.php" class="btn-back">&larr; Voltar para Lista</a>
        </div>

        <?php require '../../layout/alert.php'; ?>

        <form action="../../App/Database/insertcliente.php" method="POST">
            <div class="form-group full">
                <label for="NomeCliente">Nome Completo</label>
                <input type="text" name="NomeCliente" id="NomeCliente" placeholder="Nome Completo" required>
            </div>

            <div class="form-group">
                <label for="cpfCliente">CPF</label>
                <input type="text" name="cpfCliente" id="cpfCliente" placeholder="CPF" required>
            </div>

            <div class="form-group">
                <label for="CepCliente">CEP</label>
                <input type="text" name="CepCliente" id="CepCliente" placeholder="CEP" required>
                <div id="cep-result" style="margin-top: 5px; font-weight: 600; min-height: 20px;"></div>
            </div>

            <input type="hidden" name="iduser" value="<?php echo $idUsuario; ?>">

            <button type="submit" name="upload" class="btn-submit" value="Cadastrar">Cadastrar Cliente</button>
        </form>
    </div>
</div>

<script>
    // Capitalização Automática do Nome (Ex: joao silva -> Joao Silva)
    document.getElementById('NomeCliente').addEventListener('input', function(e) {
        var start = this.selectionStart;
        var end = this.selectionEnd;
        
        // Regex para pegar a primeira letra de cada palavra e transformar em maiúscula
        this.value = this.value.toLowerCase().replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
        
        this.setSelectionRange(start, end);
    });

    // Consulta de CEP e Exibição de Cidade/UF
    $('#CepCliente').on('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
        var cep = this.value;
        var result = $('#cep-result');
        
        if (cep.length === 8) {
            result.text('Buscando localização...').css('color', '#007bff');
            
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!("erro" in data)) {
                    result.text('Localização: ' + data.localidade + ' - ' + data.uf).css('color', '#28a745');
                } else {
                    result.text('CEP não encontrado.').css('color', '#dc3545');
                }
            }).fail(function() {
                result.text('Erro ao buscar CEP.').css('color', '#dc3545');
            });
        } else {
            result.text('');
        }
    });
</script>
</body>
</html>
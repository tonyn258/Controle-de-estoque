<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';

echo $head;
echo $header;
echo $aside;

echo '<div class="content-wrapper">
    <section class="content-header">
        <h1>Adicionar Cliente</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cliente</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastro de Cliente</h3>
                    </div>
                    <form role="form" action="../../App/Database/insertCliente.php" method="POST">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="NomeCliente">Nome do Cliente</label>
                                <input type="text" class="form-control" id="NomeCliente" name="NomeCliente" placeholder="Nome completo" required>
                            </div>
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="email" class="form-control" id="Email" name="Email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label for="CPF">CPF</label>
                                <input type="text" class="form-control" id="CPF" name="CPF" placeholder="CPF">
                            </div>
                            <div class="form-group">
                                <label for="Telefone">Telefone</label>
                                <input type="text" class="form-control" id="Telefone" name="Telefone" placeholder="Telefone">
                            </div>
                            <div class="form-group">
                                <label for="CEP">CEP</label>
                                <input type="text" class="form-control" id="CEP" name="CEP" placeholder="CEP (apenas números)">
                                <p id="cep-result" class="help-block" style="color: #3c8dbc; font-weight: bold; display: none; margin-top: 5px;"></p>
                            </div>
                            <div class="form-group">
                                <label for="Endereco">Endereço</label>
                                <input type="text" class="form-control" id="Endereco" name="Endereco" placeholder="Endereço">
                            </div>
                            <div class="form-group">
                                <label for="Numero">Número</label>
                                <input type="text" class="form-control" id="Numero" name="Numero" placeholder="Número">
                            </div>
                            <div class="form-group">
                                <label for="Bairro">Bairro</label>
                                <input type="text" class="form-control" id="Bairro" name="Bairro" placeholder="Bairro">
                            </div>
                            <div class="form-group">
                                <label for="Cidade">Cidade</label>
                                <input type="text" class="form-control" id="Cidade" name="Cidade" placeholder="Cidade">
                            </div>
                            <div class="form-group">
                                <label for="Estado">Estado</label>
                                <input type="text" class="form-control" id="Estado" name="Estado" placeholder="Estado">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>';

echo $footer;
echo $javascript;
?>
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
    document.getElementById('CEP').addEventListener('keyup', function() {
        var cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
        var resultElement = document.getElementById('cep-result');
        
        if (cep.length === 8) {
            resultElement.style.display = 'block';
            resultElement.innerText = 'Buscando localização...';
            
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        // Exibe a cidade e UF abaixo do campo CEP
                        resultElement.innerText = 'Localização encontrada: ' + data.localidade + ' - ' + data.uf;
                        resultElement.style.color = '#00a65a'; // Cor verde para sucesso
                    } else {
                        resultElement.innerText = 'CEP não encontrado.';
                        resultElement.style.color = '#dd4b39'; // Cor vermelha para erro
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    resultElement.innerText = 'Erro ao buscar CEP.';
                    resultElement.style.color = '#dd4b39';
                });
        } else {
            // Limpa a mensagem se o CEP for apagado ou incompleto
            if (cep.length == 0) {
                resultElement.style.display = 'none';
                resultElement.innerText = '';
            }
        }
    });
</script>
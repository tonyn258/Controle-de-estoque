<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
echo $head;
echo $header;
echo $aside;

echo '<div class="content-wrapper">';
require '../../layout/alert.php';

if ($perm != 1) {
  echo "Você não tem permissão!</div>";
  exit();
}

echo '<!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Adicionar <small>Cliente</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Cliente</li>
        </ol>
      </section>';

echo '<!-- Main content -->
      <section class="content">
        <div class="row">
          <a href="./" class="btn btn-success">Voltar</a>
          <div class="row">
            <div class="col-md-10">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Cliente</h3>
                </div>
                <form role="form" action="../../App/Database/InsertCliente.php" method="POST">

              <div class="box-body">
                <div class="col-sm-3">
                  <label for="exampleInputEmail1">CPF/CNPJ</label>
                  <input type="text" name="cpfCliente" class="form-control" id="exampleInputEmail1" placeholder="CPF/CNPJ">
                </div> 
                
                <div class="col-sm-7">
                  <label for="exampleInputEmail1">Nome Cliente</label>
                  <input type="text" name="NomeCliente" class="form-control" id="exampleInputEmail1" placeholder="Nome Cliente">
                </div>
                
              </div>  
              
              <div class="box-body">

                <div class="col-sm-3">
                  <label for="exampleInputEmail1">Cep</label>
                   <input type="text" name="FoneCliente" class="form-control" id="exampleInputEmail1" placeholder="Cep">
                </div>

                <div class="col-sm-4">
                  <label for="exampleInputEmail1">Cidade</label>
                  <input type="text" name="Cidade" class="form-control" id="exampleInputEmail1" placeholder="Cidade Cliente">
                </div>

                <div class="col-sm-3">
                  <label for="exampleInputEmail1">UF</label>
                   <select name="UF" class="form-control" required>
                    <option value="">Selecione um estado</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AP">Amapá</option>
                    <option value="AM">Amazonas</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PR">Paraná</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SP">São Paulo</option>
                    <option value="SE">Sergipe</option>
                    <option value="TO">Tocantins</option>
                  </select>
                </div>

              </div> 

              <div class="box-body">
                 <div class="box-footer">
                  <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">Cadastrar</button>
                  <a class="btn btn-danger" href="../../views/cliente">Cancelar</a>
                 </div> 
              </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>';
echo '</div>';
echo $footer;
echo $javascript;
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
    $('input[name="FoneCliente"]').on('blur', function() {
      var cep = $(this).val().replace(/\D/g, '');
      if (cep !== "") {
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(cep)) {
          $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {
            if (!("erro" in dados)) {
              $('input[name="Cidade"]').val(dados.localidade);
              $('select[name="UF"]').val(dados.uf);
            } else {
              alert("CEP não encontrado.");
            }
          });
        } else {
          alert("Formato de CEP inválido.");
        }
      } else {
        alert("CEP não informado.");
      }
    });
  });
</script>
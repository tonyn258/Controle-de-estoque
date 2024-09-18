<?php
require_once '../../App/auth.php'; // Verifica se o usuário está autenticado
require_once '../../layout/script.php'; // Inclui os scripts necessários
require_once '../../App/Models/vendasView.class.php'; // Inclui a classe Vendas

echo $head;
echo $header;
echo $aside;

echo '<div class="content-wrapper">
    <section class="content-header">
        <h1>Clientes e suas Vendas</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Clientes</li>
        </ol>
    </section>
    <section class="content">';

require '../../layout/alert.php'; // Inclui alertas para mensagens ao usuário

echo '
  <div class="row">  
   <div class="box box-primary">
    <div class="box-body">';

// Campo de busca (pesquisa por nome ou CPF)
echo '
    <div class="form-group">
        <label for="search">Pesquisar por Nome ou CPF:</label>
        <input type="text" id="search" class="form-control" placeholder="Digite o nome ou CPF">
    </div>
';

// Instancia um novo objeto da classe Vendas
$vendas = new Vendas; // Ou use uma classe Cliente se existir
$resp =  $vendas->indexView(); // Obtenção de todas as vendas e clientes
$resps = json_decode($resp, true);

echo '<h3>Clientes e suas Vendas</h3>';
echo '<ul class="list-group" id="clientesList">'; // Adiciona o ID da lista para a pesquisa

foreach ($resps as $row) {
    // Verifique se as informações estão presentes no array
    $Cidade = isset($row['Cidade']) && !empty($row['Cidade']) ? $row['Cidade'] : 'Cidade não informada';
    $nomeCliente = isset($row['NomeCliente']) ? $row['NomeCliente'] : 'Nome não informado';
    $cpfCliente = isset($row['cpfCliente']) ? $row['cpfCliente'] : 'CPF não informado';

    echo '<li class="list-group-item">
            ' . $nomeCliente . ' (CPF: ' . $cpfCliente . ') - Cidade: ' . $Cidade . '
          </li>';
}

echo '</ul>';
echo '</div>
</table>
   </div>
  </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;

// Adiciona script para pesquisa por nome ou CPF
echo '
<script>
    document.getElementById("search").addEventListener("keyup", function() {
        var input = this.value.toLowerCase();
        var clientesList = document.getElementById("clientesList");
        var items = clientesList.getElementsByTagName("li");

        for (var i = 0; i < items.length; i++) {
            var cliente = items[i].textContent || items[i].innerText;
            if (cliente.toLowerCase().indexOf(input) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    });
</script>
';
?>

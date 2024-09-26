<?php
require_once '../../App/auth.php'; // Verifica se o usuário está autenticado
require_once '../../layout/script.php'; // Inclui os scripts necessários
require_once '../../App/Models/vendasViewC.class.php'; // Inclui a classe Vendas

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
    <div class="box-body">' ;

// Campo de busca (pesquisa por nome ou CPF)
echo '
    <div class="form-group">
        <label for="search">Pesquisar por Nome ou CPF:</label>
        <input type="text" id="search" class="form-control" placeholder="Digite o nome ou CPF">
    </div>
';

// Instancia um novo objeto da classe Vendas
$vendas = new Vendas; 
$resp = $vendas->indexView("ORDER BY v.idVendas DESC"); // Ordena pelo idVendas de forma decrescente
$resps = json_decode($resp, true);

// Agrupa as vendas pelo código de rastreio, somando as taxas e acumulando os produtos
$vendasAgrupadas = [];
foreach ($resps as $row) {
    $CodRastreioV = $row['CodRastreioV'];
    
    // Se já existir um registro com o mesmo código de rastreio, acumule a taxa e os produtos
    if (isset($vendasAgrupadas[$CodRastreioV])) {
        $vendasAgrupadas[$CodRastreioV]['Vd_Tax'] += $row['Vd_Tax'];
        $vendasAgrupadas[$CodRastreioV]['Produtos'][] = [
            'NomeProduto' => $row['NomeProduto'],
            'Itensquant' => $row['Itensquant'],
            'Vd_Tax' => $row['Vd_Tax']
        ];
    } else {
        // Caso contrário, crie um novo registro
        $vendasAgrupadas[$CodRastreioV] = [
            'NomeCliente' => $row['NomeCliente'],
            'Cidade' => $row['Cidade'],
            'UF' => $row['UF'],
            'Vd_Tax' => $row['Vd_Tax'],  // Adiciona Vd_Tax
            'Produtos' => [[
                'NomeProduto' => $row['NomeProduto'],
                'Itensquant' => $row['Itensquant'],
                'Vd_Tax' => $row['Vd_Tax']
            ]]  // Adiciona o primeiro produto
        ];
    }
}

echo '<h3>Clientes e suas Vendas</h3>';
echo '<ul class="list-group" id="clientesList">'; // Adiciona o ID da lista para a pesquisa

// Exibe os resultados agrupados
foreach ($vendasAgrupadas as $CodRastreioV => $venda) {
    // Formatação da taxa como moeda brasileira
    $Vd_Tax = 'R$' . number_format($venda['Vd_Tax'], 2, ',', '.');
    $nomeCliente = $venda['NomeCliente'];
    $Cidade = $venda['Cidade'];
    $UF = $venda['UF'];

    // Exibição no formato solicitado
    echo '<li class="list-group-item">
            <div style="cursor:pointer;" onclick="toggleDetails(this)">
                ' . $CodRastreioV . ' - ' . $nomeCliente . ' - ' . $Cidade . ' - ' . $UF . ' - ' . $Vd_Tax . '
            </div>
            <div class="product-details" style="display:none; margin-top: 10px;">';

    // Exibe todos os produtos relacionados ao código de rastreio
    foreach ($venda['Produtos'] as $produto) {
        $produtoTax = 'R$' . number_format($produto['Vd_Tax'], 2, ',', '.');
        
        // Exibe o NomeProduto de acordo com o valor de Itensquant
        for ($i = 0; $i < $produto['Itensquant']; $i++) {
            echo '<div>
                    <strong>Nome do Produto:</strong> ' . $produto['NomeProduto'] . ' - 
                    <strong>Valor da Venda:</strong> ' . $produtoTax . '
                  </div>';
        }
    }

    echo '</div>
          </li>';
}

echo '</ul>';
echo '</div>
   </div>
  </div>';
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;

// Adiciona script para pesquisa por nome ou CPF e exibição dos detalhes do produto
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

    // Função para alternar a exibição dos detalhes do produto
    function toggleDetails(element) {
        var details = element.nextElementSibling; // Seleciona o próximo elemento (detalhes do produto)
        if (details.style.display === "none") {
            details.style.display = "block"; // Mostra os detalhes
        } else {
            details.style.display = "none"; // Oculta os detalhes
        }
    }
</script>
';
?>

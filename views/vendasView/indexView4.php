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
        <div class="nav-menu">
            <a href="../">🏠 Home</a>
            <a href="index.php">🛒 Vendas</a>
        </div>
    </section>
    <style>
        .nav-menu { position: absolute; top: 10px; right: 15px; }
        .nav-menu a { display: inline-block; padding: 10px 20px; margin: 0 5px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .nav-menu a:hover { background: #0056b3; }
    </style>
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
$vendas = new Vendas;
$idUsuario = $_SESSION['idUsuario'];
$resp = $vendas->indexView($idUsuario, "ORDER BY v.idVendas DESC"); // Ordena pelo idVendas de forma decrescente
$resps = json_decode($resp, true);

// Agrupa as vendas pelo código de rastreio, somando as taxas e acumulando os produtos
$vendasAgrupadas = [];
foreach ($resps as $row) {
    $CodRastreioV = $row['CodRastreioV'];

    // Se já existir um registro com o mesmo código de rastreio, acumule a taxa e os produtos
    if (isset($vendasAgrupadas[$CodRastreioV])) {
        $vendasAgrupadas[$CodRastreioV]['Vd_Tax'] += $row['Vd_Tax'];
        $vendasAgrupadas[$CodRastreioV]['Venda_Total'] += $row['Venda_Total']; // Acumula o valor de Venda_Total
        $vendasAgrupadas[$CodRastreioV]['Diferenca_Quantidade'] += $row['Diferenca_Quantidade']; // Acumula o valor de Diferenca_Quantidade
        //$vendasAgrupadas[$CodRastreioV]['Diferenca_Quantidade'] += $row['Itensquant']; // Soma as quantidades
        $vendasAgrupadas[$CodRastreioV]['Produtos'][] = [
            'NomeProduto' => $row['NomeProduto'],
            'Itensquant' => $row['Itensquant'],
            'Vd_Tax' => $row['Vd_Tax']
        ];
    } else {
        // Caso contrário, crie um novo registro
        $vendasAgrupadas[$CodRastreioV] = [
            'NomeCliente' => $row['NomeCliente'],
            'CepCliente' => $row['CepCliente'],
            'Vd_Tax' => $row['Vd_Tax'],  // Adiciona Vd_Tax
            'Venda_Total' => $row['Venda_Total'], // Adiciona Venda_Total
            'Diferenca_Quantidade' => $row['Diferenca_Quantidade'], // Adiciona Diferenca_Quantidade
            //'Diferenca_Quantidade' => $row['Itensquant'], // Inicializa com o valor de Itensquant
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
    // Formatação da Venda_Total como moeda brasileira
    $Venda_Total = 'R$' . number_format($venda['Venda_Total'], 2, ',', '.');
    $Diferenca_Quantidade = 'R$' . number_format($venda['Diferenca_Quantidade'], 2, ',', '.');
    //$Diferenca_Quantidade = $venda['Diferenca_Quantidade'];
    $nomeCliente = $venda['NomeCliente'];
    $CepCliente = $venda['CepCliente'];

    // Exibição no formato solicitado
    echo '<li class="list-group-item">
            <div style="cursor:pointer;" onclick="toggleDetails(this)">
                ' . $CodRastreioV . ' - ' . $nomeCliente . ' - ' . $CepCliente . ' - ' . $Venda_Total . ' - ' . $Diferenca_Quantidade . '
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

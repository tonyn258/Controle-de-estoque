<?php
require_once '../App/auth.php';
require_once '../layout/script.php';
require_once '../views/index.php';
require_once '../App/Models/vendas.class.php';
require_once '../App/Models/compras.class.php';

// Imprime o cabeçalho HTML, o cabeçalho da página, a barra lateral e abre a seção do conteúdo
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper"> 
      <section class="content">';

// informações de conexão com o BANCO DE DADOS
$conexao = new connect();
$vendas = new Vendas($conexao);
$compras = new Compras($conexao);

// Obtém o ano atual
$currentYear = date('Y');

// Verifica se o usuário selecionou um ano no formulário, caso contrário, usa o ano atual ou a opção "Todos"
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

?>

<!-- Formulário para seleção do ano -->
<form method="post" action="">
  <label for="year">Selecione O ano:</label>
  <select name="year" id="year">
    <option value="all" <?php if ($selectedYear === 'all') echo 'selected'; ?>>Todos os anos</option>
    <?php for ($i = $currentYear; $i >= 2021; $i--) { ?>
      <option value="<?php echo $i; ?>" <?php if ($i === $selectedYear) echo 'selected'; ?>><?php echo $i; ?></option>
    <?php } ?>
  </select>
  <button type="submit">Gerar Gráfico</button>
</form>

<?php

// Query para buscar as informações de vendas do banco de dados para o ano selecionado ou todos os anos
if ($selectedYear === 'all') {
  $sql_vendas = "SELECT Compra_id, Vd_Tax, DataVenda FROM vendas";
  $sql_compras = "SELECT NULL AS Vd_Tax, NULL AS DataVenda, DataCompra, ValorCompra FROM compras";
} else {
  $sql_vendas = "SELECT Compra_id, Vd_Tax, DataVenda FROM vendas WHERE YEAR(DataVenda) = $selectedYear";
  $sql_compras = "SELECT NULL AS Vd_Tax, NULL AS DataVenda, DataCompra, ValorCompra FROM compras WHERE YEAR(DataCompra) = $selectedYear";
}


                
$result_vendas = mysqli_query($conexao->SQL, $sql_vendas);
$result_compras = mysqli_query($conexao->SQL, $sql_compras);

$result = mysqli_query($conexao->SQL, $sql_compras);
// Obtém o valor total das compras
$totalCompras = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $totalCompras += $row['ValorCompra'];
}

// Obtém o valor total das vendas
$result = mysqli_query($conexao->SQL, $sql_vendas);
$totalVendas = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $totalVendas += $row['Vd_Tax'];
}

// Obtém o valor total dos lucros
$result = mysqli_query($conexao->SQL, $sql_vendas);
$totalLucros = 0;
while ($row = mysqli_fetch_assoc($result)) {
  $valorVendas = (float)$row["Vd_Tax"];
   $valorCompra = (float)$row["Compra_id"];
    $valorLucro = $valorVendas - $valorCompra;
$totalLucros += $valorLucro;
}

echo '<div class="row"><!--Fim More info-->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
            <h3>R$ ' . $totalCompras . '</h3>
              <p>Compras realizadas</p>
            </div>            
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
            <h3>R$ ' . $totalVendas . '</h3>
              <p>Faturamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
            <h3>R$ ' . $totalLucros . '</h3>

              <p>Lucro</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col55 -->
      </div>';
// Fim More info

// Array com os meses do ano
$meses = array("", "Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");

// Cria um array com os dados de vendas e lucro por mês
$dataArrayVendas = array();
for ($mes = 0; $mes <= 12; $mes++) {
    //$dataArrayVendas[] = array($meses[$mes], 0, 0, 0, ''); // [Mês, Valor de vendas (R$), Valor acumulado (R$)]
    $dataArrayVendas[] = array($meses[$mes], 0, 0, 0, 0, ''); // [Mês, Valor de vendas (R$), Lucro (R$), Valor de compra (R$), Data de compra]

}

// Preenche o array com os dados da compras
while ($row = mysqli_fetch_assoc($result_compras)) {
    $mes = (int)date("n", strtotime($row["DataCompra"]));
    $valorCompra = (float)$row["ValorCompra"];

    $dataArrayVendas[$mes][1] += $valorCompra; // Soma o valor de compra por mês
    //$dataArrayVendas[$mes][4] = $row["DataCompra"]; // Define a data de compra
}

// Preenche o array com os dados de vendas
while ($row = mysqli_fetch_assoc($result_vendas)) {
    $mes = (int)date("n", strtotime($row["DataVenda"]));    
    $valorVendas = (float)$row["Vd_Tax"];
    //$valorLucro = $valorVendas - (float)$row["Compra_id"];
    $valorLucro = $valorVendas - (float)$row["Compra_id"] ;   
     
    $dataArrayVendas[$mes][3] += $valorVendas; // Soma o valor de vendas por mês
    $dataArrayVendas[$mes][2] += $valorLucro; // Soma o valor do lucro por mês    
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao->SQL);

// Cria o gráfico utilizando a API do Google Charts
?>
<!-- Gráfico HTML -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Mês', 'Valor de compra (R$)','Faturamento (R$)', 'Lucro (R$)',  { role: 'style' }], // Cabeçalho das colunas
            <?php foreach ($dataArrayVendas as $venda) { ?>
                
                // Defina as cores com base no valor de Vd_Tax
                <?php $color = ($venda[1] > $venda[2]) ? 'SpringGreen' : 'SpringGreen';?>
                <?php echo json_encode([$venda[0], $venda[1],$venda[3], $venda[2],   $color]) . ','; ?>
            <?php } ?>
        ]);

        var options = {
            title: 'Gráfico de Vendas & Lucro',
            legend: {position: 'bottom'},
            hAxis: {title: 'Mês'},
            vAxis: {title: 'Valor (R$)'},
            colors: ['DodgerBlue', 'OrangeRed','SpringGreen'], 
            width: '100%',
            height: 300,
        };

        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
        chart.draw(data, options);
    }
</script>
<div class="box box-danger" id="columnchart_values" ></div>
<!-- Fim do Gráfico HTML -->
<div class="row">
    <div class="col-md-6">        
    </div>
</div>

<?php
// Fecha a seção do conteúdo e imprime o rodapé HTML
echo '</section>';
echo '</div >';
//echo $footer;
echo $javascript;
?>
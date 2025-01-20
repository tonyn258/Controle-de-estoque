<?php
require_once '../App/auth.php';
require_once '../layout/script.php';
require_once '../views/index.php';
require_once '../App/Models/vendas.class.php';
require_once '../App/Models/compras.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper"><section class="content">';

// Conexão com o banco de dados
$conexao = new connect();
$vendas = new Vendas($conexao);
$compras = new Compras($conexao);

// Obtém o ano atual
$currentYear = date('Y');

// Verifica se o usuário selecionou um ano no formulário
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

// Formulário de seleção de ano
?>
<form method="post" action="">
  <label for="year">Selecione o ano:</label>
  <select name="year" id="year">
    <option value="all" <?php if ($selectedYear === 'all') echo 'selected'; ?>>Todos os anos</option>
    <?php for ($i = $currentYear; $i >= 2021; $i--) { ?>
      <option value="<?php echo $i; ?>" <?php if ($i == $selectedYear) echo 'selected'; ?>><?php echo $i; ?></option>
    <?php } ?>
  </select>
  <button type="submit">Filtrar</button>
</form>

<?php
// Consultas para compras e vendas
$sql_compras = ($selectedYear === 'all') ?
  "SELECT DataCompra, ValorCompra, QuantItens FROM compras" :
  "SELECT DataCompra, ValorCompra, QuantItens FROM compras WHERE YEAR(DataCompra) = $selectedYear";

$sql_vendas = ($selectedYear === 'all') ?
  "SELECT DataVenda, Venda_Total, Diferenca_Quantidade FROM vendas" :
  "SELECT DataVenda, Venda_Total, Diferenca_Quantidade FROM vendas WHERE YEAR(DataVenda) = $selectedYear";

$result_compras = mysqli_query($conexao->SQL, $sql_compras);
$result_vendas = mysqli_query($conexao->SQL, $sql_vendas);

// Inicializa o array $dataArray com os meses do ano e valores padrão
$meses = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
$dataArray = array_fill(1, 12, ['compra' => 0, 'venda' => 0, 'lucro' => 0]);

// Preenche os dados de compras
while ($row = mysqli_fetch_assoc($result_compras)) {
  $mes = (int)date("n", strtotime($row['DataCompra']));
  $dataArray[$mes]['compra'] += $row['ValorCompra'] * $row['QuantItens'];
}

// Preenche os dados de vendas e lucro
while ($row = mysqli_fetch_assoc($result_vendas)) {
  $mes = (int)date("n", strtotime($row['DataVenda']));
  $dataArray[$mes]['venda'] += $row['Venda_Total'];
  $dataArray[$mes]['lucro'] += $row['Diferenca_Quantidade'];
}

// Calcula o total de compras realizadas
$totalCompras = array_sum(array_column($dataArray, 'compra'));

// Calcula o total de faturamento
$totalFaturamento = array_sum(array_column($dataArray, 'venda'));

// Calcula o total de lucro
$totalLucro = array_sum(array_column($dataArray, 'lucro'));

// Calcula o total de lucro mensal e a média
$totalLucroMensal = 0;
$mesesComLucro = 0;

foreach ($dataArray as $mes => $valores) {
  if ($valores['lucro'] > 0) { // Conta apenas meses com lucro
    $totalLucroMensal += $valores['lucro'];
    $mesesComLucro++;
  }
}

$mediaLucroMensal = ($mesesComLucro > 0) ? $totalLucroMensal / $mesesComLucro : 0;

// Exibição dos Small Boxes
echo '<div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>R$ ' . number_format($totalCompras, 2, ',', '.') . '</h3>
              <p>Compras realizadas</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3>R$ ' . number_format($totalFaturamento, 2, ',', '.') . '</h3>
              <p>Faturamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>R$ ' . number_format($totalLucro, 2, ',', '.') . '</h3>
              <p>Lucro</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>R$ ' . number_format($mediaLucroMensal, 2, ',', '.') . '</h3>
              <p>Lucro mensal</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
      </div>';
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", {
    packages: ["corechart"]
  });
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Mês', 'Compras (R$)', 'Faturamento (R$)', 'Lucro (R$)'],
      <?php foreach ($dataArray as $mes => $valores) { ?>['<?php echo $meses[$mes - 1]; ?>', <?php echo $valores['compra']; ?>, <?php echo $valores['venda']; ?>, <?php echo $valores['lucro']; ?>],
      <?php } ?>
    ]);

    var options = {
      title: 'Gráfico de Compras, Faturamento e Lucro',
      legend: {
        position: 'bottom'
      },
      hAxis: {
        title: 'Mês'
      },
      vAxis: {
        title: 'Valor (R$)'
      },
      colors: ['DodgerBlue', 'OrangeRed', 'SpringGreen'],
      width: '100%',
      height: 400,
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>
<div id="chart_div"></div>

<?php
// Fecha a conexão
mysqli_close($conexao->SQL);

echo '</section></div>';
echo $javascript;
?>
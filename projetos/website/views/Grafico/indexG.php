<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/vendas.class.php';

// Imprime o cabeçalho HTML, o cabeçalho da página, a barra lateral e abre a seção do conteúdo
echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper"> 
      <section class="content">';

// informações de conexão com o BANCO DE DADOS
$conexao = new connect();
$vendas = new Vendas($conexao);

// Obtém o ano e mês atual
$currentYear = date('Y');
$currentMonth = date('n');

// Verifica se o usuário selecionou um mês e ano no formulário, caso contrário, usa o mês e ano atuais
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : $currentMonth;
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

// Gráfico Mensal: Query para buscar as vendas por mês no ano selecionado
$sql_vendas_mes = "SELECT SUM(Itensquant) as totalItens, MONTH(DataVenda) as mes FROM vendas WHERE YEAR(DataVenda) = $selectedYear GROUP BY MONTH(DataVenda)";
$result_vendas_mes = mysqli_query($conexao->SQL, $sql_vendas_mes);
$vendasMensais = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($result_vendas_mes)) {
  $mesVenda = (int)$row["mes"];
  $totalItens = (int)$row["totalItens"];
  $vendasMensais[$mesVenda] = $totalItens;
}

// Gráfico Diário: Query para buscar as vendas diárias no mês e ano selecionados
$lastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
$sql_vendas_dia = "SELECT Itensquant, DataVenda FROM vendas WHERE MONTH(DataVenda) = $selectedMonth AND YEAR(DataVenda) = $selectedYear";
$result_vendas_dia = mysqli_query($conexao->SQL, $sql_vendas_dia);
$vendasDiarias = array_fill(1, $lastDayOfMonth, 0);
while ($row = mysqli_fetch_assoc($result_vendas_dia)) {
  $diaVenda = (int)date("d", strtotime($row["DataVenda"]));
  $quantItens = (int)$row["Itensquant"];
  $vendasDiarias[$diaVenda] += $quantItens;
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao->SQL);
?>

<!-- Formulário para seleção do mês e ano -->
<form method="post" action="">
  <label for="year">Ano:</label>
  <select name="year" id="year">
    <?php for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++): ?>
      <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>>
        <?php echo $y; ?>
      </option>
    <?php endfor; ?>
  </select>

  <label for="month">Mês:</label>
  <select name="month" id="month">
    <?php for ($m = 1; $m <= 12; $m++): ?>
      <option value="<?php echo $m; ?>" <?php echo $m == $selectedMonth ? 'selected' : ''; ?>>
        <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
      </option>
    <?php endfor; ?>
  </select>

  <button type="submit">Filtrar</button>
</form>

<!-- Inclui o Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", { packages: ["corechart"] });
  google.charts.setOnLoadCallback(drawCharts);

  function drawCharts() {
    // Gráfico Mensal
    var dataMensal = google.visualization.arrayToDataTable([
      ['Mês', 'Itens Vendidos'],
      <?php 
      for ($mes = 1; $mes <= 12; $mes++) { 
        $quantidade = isset($vendasMensais[$mes]) ? $vendasMensais[$mes] : 0;
        $mesNome = date('F', mktime(0, 0, 0, $mes, 10));
        echo "['$mesNome', $quantidade],"; 
      } 
      ?>
    ]);

    var optionsMensal = {
      title: 'Vendas por Mês - <?php echo $selectedYear; ?>',
      legend: { position: 'bottom' },
      hAxis: {
        title: 'Mês',
        slantedText: true,
        slantedTextAngle: 45,
      },
      vAxis: { title: 'Quantidade de Itens Vendidos' },
      colors: ['DodgerBlue'],
      width: '100%',
      height: 400,
    };

    var chartMensal = new google.visualization.ColumnChart(document.getElementById("chart_mensal"));
    chartMensal.draw(dataMensal, optionsMensal);

    // Gráfico Diário
    var dataDiario = google.visualization.arrayToDataTable([
      ['Dia', 'Itens Vendidos', { role: 'style' }],
      <?php 
      for ($dia = 1; $dia <= $lastDayOfMonth; $dia++) { 
        $quantidade = isset($vendasDiarias[$dia]) ? $vendasDiarias[$dia] : 0;
        echo json_encode([$dia, $quantidade, 'color: DodgerBlue']) . ','; 
      } 
      ?>
    ]);

    var optionsDiario = {
      title: 'Vendas por Dia - <?php echo date('F', mktime(0, 0, 0, $selectedMonth, 10)) . " " . $selectedYear; ?>',
      legend: { position: 'bottom' },
      hAxis: {
        title: 'Dia do Mês',
        ticks: <?php echo json_encode(range(1, $lastDayOfMonth)); ?>,
      },
      vAxis: { title: 'Quantidade de Itens Vendidos' },
      width: '100%',
      height: 300,
    };

    //var chartDiario = new google.visualization.LineChart(document.getElementById("chart_diario"));
    var chartDiario = new google.visualization.ColumnChart(document.getElementById("chart_diario"));
    chartDiario.draw(dataDiario, optionsDiario);
  }
</script>

<!-- Divs onde os gráficos serão exibidos -->
<div class="box box-danger" id="chart_mensal"></div>
<div class="box box-danger" id="chart_diario"></div>

<?php
// Fecha a seção do conteúdo e imprime o rodapé HTML
echo '</section>';
echo '</div>';
?>

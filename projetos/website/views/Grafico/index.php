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

// Obtém o número de dias do mês selecionado
$lastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

// Query para buscar as informações de vendas do banco de dados para o mês e ano selecionados
$sql_vendas = "SELECT Itensquant, DataVenda FROM vendas WHERE MONTH(DataVenda) = $selectedMonth AND YEAR(DataVenda) = $selectedYear";
$result_vendas = mysqli_query($conexao->SQL, $sql_vendas);

// Array para armazenar as vendas diárias, inicializando com 0 para todos os dias do mês
$vendasDiarias = array_fill(1, $lastDayOfMonth, 0);

// Preenche o array com as vendas por dia
while ($row = mysqli_fetch_assoc($result_vendas)) {
  $diaVenda = (int)date("d", strtotime($row["DataVenda"])); // Obtém o dia da venda
  $quantItens = (int)$row["Itensquant"]; // Quantidade de itens vendidos
  $vendasDiarias[$diaVenda] += $quantItens; // Acumula o total de vendas para o dia
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao->SQL);
?>

<!-- Formulário para seleção do mês e ano -->
<form method="post" action="">
  <label for="month">Mês:</label>
  <select name="month" id="month">
    <?php for ($m = 1; $m <= 12; $m++): ?>
      <option value="<?php echo $m; ?>" <?php echo $m == $selectedMonth ? 'selected' : ''; ?>>
        <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
      </option>
    <?php endfor; ?>
  </select>

  <label for="year">Ano:</label>
  <select name="year" id="year">
    <?php for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++): ?>
      <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>>
        <?php echo $y; ?>
      </option>
    <?php endfor; ?>
  </select>

  <button type="submit">Filtrar</button>
</form>

<!-- Inclui o Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", { packages: ["corechart"] });
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Dia', 'Itens Vendidos', { role: 'style' }],
      <?php 
      // Gera os dados para cada dia do mês
      for ($dia = 1; $dia <= $lastDayOfMonth; $dia++) { 
        $quantidade = isset($vendasDiarias[$dia]) ? $vendasDiarias[$dia] : 0;
        echo json_encode([$dia, $quantidade, 'color: DodgerBlue']) . ','; 
      } 
      ?>
    ]);

    var options = {
      title: 'Vendas por Dia - <?php echo date('F', mktime(0, 0, 0, $selectedMonth, 10)) . " " . $selectedYear; ?>',
      legend: { position: 'bottom' },
      hAxis: {
        title: 'Dia do Mês',
        ticks: <?php echo json_encode(range(1, $lastDayOfMonth)); ?>,
        minValue: 1,
        maxValue: <?php echo $lastDayOfMonth; ?>
      },
      vAxis: { title: 'Quantidade de Itens Vendidos' },
      colors: ['DodgerBlue'],
      width: '100%',
      height: 300,
    };

    var chart = new google.visualization.LineChart(document.getElementById("linechart_values"));
    chart.draw(data, options);
  }
</script>

<!-- Div onde o gráfico será exibido -->
<div class="box box-danger" id="linechart_values"></div>

<?php
// Fecha a seção do conteúdo e imprime o rodapé HTML
echo '</section>';
echo '</div>';
?>

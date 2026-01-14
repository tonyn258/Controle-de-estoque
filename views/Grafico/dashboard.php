<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/vendas.class.php';
require_once '../../App/Models/compras.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper"><section class="content">';

// Conexão com banco de dados
$conexao = new connect();
$vendas = new Vendas($conexao);
$compras = new Compras($conexao);

// Obtém o ano atual
$currentYear = date('Y');

// Verifica se o usuário selecionou um ano no formulário
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('n');

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
  <label for="month" style="margin-left: 15px;">Mês:</label>
  <select name="month" id="month">
    <?php 
    $nomesMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    foreach ($nomesMeses as $index => $nome): 
        $valor = $index + 1;
    ?>
        <option value="<?php echo $valor; ?>" <?php echo $valor == $selectedMonth ? 'selected' : ''; ?>><?php echo $nome; ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Filtrar</button>
</form>

<?php
// Consultas para compras e vendas
$sql_compras = ($selectedYear === 'all') ?
  "SELECT DataCompra, ValorCompra, QuantItens FROM anuncio" :
  "SELECT DataCompra, ValorCompra, QuantItens FROM anuncio WHERE YEAR(DataCompra) = '$selectedYear'";

$sql_vendas = ($selectedYear === 'all') ?
  "SELECT DataVenda, Vd_Tax, valor FROM vendas" :
  "SELECT DataVenda, Vd_Tax, valor FROM vendas WHERE YEAR(DataVenda) = '$selectedYear'";

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
  
  $dataArray[$mes]['venda'] += $row['Vd_Tax'];
  $dataArray[$mes]['lucro'] += ($row['Vd_Tax'] - $row['valor']);
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

// 2. Dados Diários (Restaurado para o gráfico diário)
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
$dailyData = array_fill(1, $daysInMonth, 0);

// Consulta para vendas diárias
$sqlDaily = "SELECT DataVenda, Vd_Tax FROM vendas WHERE MONTH(DataVenda) = '$selectedMonth' AND YEAR(DataVenda) = '$selectedYear'";
$resultDaily = mysqli_query($conexao->SQL, $sqlDaily);

while ($row = mysqli_fetch_assoc($resultDaily)) {
    $day = intval(date('d', strtotime($row["DataVenda"])));
    $dailyData[$day] += (float)$row["Vd_Tax"];
}
$dailyLabels = array_keys($dailyData);
$dailyValues = array_values($dailyData);

// 3. Dados de Clientes por Estado (Mapa)
$sql_clientes = "SELECT CepCliente FROM cliente";
$result_clientes = mysqli_query($conexao->SQL, $sql_clientes);

$clientesPorEstado = [];

// Função auxiliar para mapear CEP para UF (caso UF esteja vazio)
function getUfFromCep($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) < 8) return '';
    $prefix = (int)substr($cep, 0, 5);
    
    if ($prefix >= 1000 && $prefix <= 19999) return 'SP';
    if ($prefix >= 20000 && $prefix <= 28999) return 'RJ';
    if ($prefix >= 29000 && $prefix <= 29999) return 'ES';
    if ($prefix >= 30000 && $prefix <= 39999) return 'MG';
    if ($prefix >= 40000 && $prefix <= 48999) return 'BA';
    if ($prefix >= 49000 && $prefix <= 49999) return 'SE';
    if ($prefix >= 50000 && $prefix <= 56999) return 'PE';
    if ($prefix >= 57000 && $prefix <= 57999) return 'AL';
    if ($prefix >= 58000 && $prefix <= 58999) return 'PB';
    if ($prefix >= 59000 && $prefix <= 59999) return 'RN';
    if ($prefix >= 60000 && $prefix <= 63999) return 'CE';
    if ($prefix >= 64000 && $prefix <= 64999) return 'PI';
    if ($prefix >= 65000 && $prefix <= 65999) return 'MA';
    if ($prefix >= 66000 && $prefix <= 68999) return 'PA';
    if ($prefix >= 69000 && $prefix <= 69299) return 'AM';
    if ($prefix >= 69300 && $prefix <= 69399) return 'RR';
    if ($prefix >= 69400 && $prefix <= 69899) return 'AM';
    if ($prefix >= 69900 && $prefix <= 69999) return 'AC';
    if ($prefix >= 70000 && $prefix <= 73699) return 'DF';
    if ($prefix >= 73700 && $prefix <= 76799) return 'GO';
    if ($prefix >= 76800 && $prefix <= 76999) return 'RO';
    if ($prefix >= 77000 && $prefix <= 77999) return 'TO';
    if ($prefix >= 78000 && $prefix <= 78899) return 'MT';
    if ($prefix >= 79000 && $prefix <= 79999) return 'MS';
    if ($prefix >= 80000 && $prefix <= 87999) return 'PR';
    if ($prefix >= 88000 && $prefix <= 89999) return 'SC';
    if ($prefix >= 90000 && $prefix <= 99999) return 'RS';
    return '';
}

while ($row = mysqli_fetch_assoc($result_clientes)) {
    $uf = getUfFromCep($row['CepCliente']);
    
    if (!empty($uf)) {
        if (!isset($clientesPorEstado[$uf])) $clientesPorEstado[$uf] = 0;
        $clientesPorEstado[$uf]++;
    }
}

// Prepara dados para o Google GeoChart
$mapData = [['Estado', 'Clientes', ['role' => 'tooltip', 'p' => ['html' => true]]]];
foreach ($clientesPorEstado as $uf => $count) {
    $label = $count . ($count == 1 ? ' cliente' : ' clientes');
    $tooltip = '<div style="padding:5px; white-space:nowrap;"><strong>' . $uf . '</strong><br>' . $label . '</div>';
    $mapData[] = ['BR-' . $uf, $count, $tooltip];
}

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

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Comparativo: Compras, Faturamento e Lucro</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="comparisonChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Charts (Restaurados) -->
<div class="row">
    <!-- Bar Chart -->
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Vendas por Mês (Barras)</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart -->
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Evolução Mensal (Linha)</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="lineChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Detalhamento Diário: <?php echo $nomesMeses[$selectedMonth - 1] . '/' . $selectedYear; ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="dailyChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mapa de Clientes -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Distribuição de Clientes por Estado</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="regions_div" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = <?php echo json_encode($meses); ?>;
    // Extraindo dados de vendas do array $dataArray já calculado
    const dataValues = <?php echo json_encode(array_column($dataArray, 'venda')); ?>;
    const comprasValues = <?php echo json_encode(array_column($dataArray, 'compra')); ?>;
    const lucroValues = <?php echo json_encode(array_column($dataArray, 'lucro')); ?>;
    const dailyLabels = <?php echo json_encode($dailyLabels); ?>;
    const dailyValues = <?php echo json_encode($dailyValues); ?>;

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    };

    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Compras',
                    data: comprasValues,
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Faturamento',
                    data: dataValues,
                    backgroundColor: 'rgba(60, 141, 188, 0.9)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Lucro',
                    data: lucroValues,
                    backgroundColor: 'rgba(0, 166, 90, 0.9)',
                    borderColor: 'rgba(0, 166, 90, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Vendas', data: dataValues, backgroundColor: 'rgba(60, 141, 188, 0.8)', borderColor: 'rgba(60, 141, 188, 1)', borderWidth: 1 }] },
        options: commonOptions
    });

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: { 
            labels: labels, 
            datasets: [{ label: 'Vendas', data: dataValues, backgroundColor: 'rgba(0, 192, 239, 0.2)', borderColor: 'rgba(0, 192, 239, 1)', borderWidth: 2, fill: true, tension: 0.4 }] 
        },
        options: commonOptions
    });

    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: { labels: dailyLabels, datasets: [{ label: 'Vendas Diárias', data: dailyValues, backgroundColor: 'rgba(221, 75, 57, 0.8)', borderColor: 'rgba(221, 75, 57, 1)', borderWidth: 1 }] },
        options: commonOptions
    });
});
</script>

<!-- Google Charts (GeoChart) -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {'packages':['geochart']});
  google.charts.setOnLoadCallback(drawRegionsMap);

  function drawRegionsMap() {
    var data = google.visualization.arrayToDataTable(<?php echo json_encode($mapData); ?>);

    var options = {
      region: 'BR',
      resolution: 'provinces',
      colorAxis: {colors: ['#e0f7fa', '#006064']}, // Gradiente de azul claro para escuro
      backgroundColor: '#fff',
      datalessRegionColor: '#f5f5f5',
      defaultColor: '#f5f5f5',
      tooltip: {isHtml: true}
    };

    var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
    chart.draw(data, options);
  }
</script>

<?php
// Fecha a conexão
mysqli_close($conexao->SQL);

echo '</section></div>';
echo $javascript;
?>
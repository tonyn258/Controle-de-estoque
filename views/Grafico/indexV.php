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

// Obtém o ano atual
$currentYear = date('Y');

// Verifica se o usuário selecionou um ano no formulário, caso contrário, usa o ano atual
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;

// Query para buscar as informações de vendas do banco de dados para o ano selecionado, agrupando por mês
$sql_vendas = "SELECT SUM(Itensquant) as totalItens, MONTH(DataVenda) as mes FROM vendas WHERE YEAR(DataVenda) = $selectedYear GROUP BY MONTH(DataVenda)";
$result_vendas = mysqli_query($conexao->SQL, $sql_vendas);

// Array para armazenar as vendas mensais, inicializando com 0 para todos os meses do ano
$vendasMensais = array_fill(1, 12, 0);

// Preenche o array com as vendas por mês
while ($row = mysqli_fetch_assoc($result_vendas)) {
  $mesVenda = (int)$row["mes"]; // Obtém o mês da venda
  $totalItens = (int)$row["totalItens"]; // Quantidade total de itens vendidos no mês
  $vendasMensais[$mesVenda] = $totalItens; // Armazena o total de vendas no mês correspondente
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao->SQL);
?>

<!-- Formulário para seleção do ano -->
<form method="post" action="">
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
      ['Mês', 'Itens Vendidos'],
      <?php 
      // Gera os dados para cada mês do ano com descrição textual do mês
      for ($mes = 1; $mes <= 12; $mes++) { 
        $quantidade = isset($vendasMensais[$mes]) ? $vendasMensais[$mes] : 0;
        $mesNome = date('F', mktime(0, 0, 0, $mes, 10)); // Obtém o nome do mês
        echo "['$mesNome', $quantidade],"; 
      } 
      ?>
    ]);

    var options = {
      title: 'Vendas por Mês - <?php echo $selectedYear; ?>',
      legend: { position: 'bottom' },
      hAxis: {
        title: 'Mês',
        slantedText: true, // Inclina o texto para melhor legibilidade
        slantedTextAngle: 45, // Ângulo de inclinação (45 graus)
        minValue: 1,
        maxValue: 12,
      },
      vAxis: { title: 'Quantidade de Itens Vendidos' },
      colors: ['DodgerBlue'],
      width: '100%',
      height: 400, // Aumenta a altura do gráfico
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("linechart_values"));
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

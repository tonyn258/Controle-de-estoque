<?php

require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/compras.class.php';


echo $head;
echo $header;
echo $aside;


echo '<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Grafico Movimentação </h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Compras</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">';

// informações de conexão com o banco de dados
$localhost = "localhost";
$root = "root";
$passwd = "";
$database = "controlestoque";

// cria uma conexão com o banco de dados
$conn = mysqli_connect($localhost, $root, $passwd, $database);

// verifica se a conexão foi bem sucedida
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// prepara a consulta SQL para recuperar os dados do banco de dados
//$sql = "SELECT `DataCompra`, `ValorCompra` FROM `compras`";

// prepara a consulta SQL para recuperar os dados do banco de dados apenas para o ano de 2023
$sql = "SELECT `DataCompra`, `ValorCompra` FROM `compras` WHERE `DataCompra` BETWEEN '2023-01-01' AND '2023-12-31'";

// executa a consulta SQL
$result = mysqli_query($conn, $sql);

// cria um array com os dados recuperados do banco de dados
$dataArray = array(array("Data da compra", "Valor da compra",));
while ($row = mysqli_fetch_assoc($result)) {
    $dataArray[] = array($row["DataCompra"], (float)$row["ValorCompra"]);
}

// adiciona o mês por extenso e o valor da compra no array
$meses = array("", "Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");

$dataArray = array(array("Data da compra", "Valor da compra"));
for ($i = 1; $i <= 12; $i++) {
  $mes = $meses[$i];
  $valor = 0;
  foreach ($result as $row) {
    $data = strtotime($row["DataCompra"]);
    if (intval(date('m', $data)) == $i) {
      $valor += (float)$row["ValorCompra"];
    }
  }
  //$dataArray[] = array($mes . " " . date('Y'), $valor);
  //$dataArray[] = array($mes . " " , $valor);
  $dataArray[] = array($mes . " 2023" , $valor);
}

// obtém o ano correspondente à última data no array de dados
$ultimoMes = end($dataArray);
$ano = substr($ultimoMes[0], -4);

// fecha a conexão com o banco de dados
mysqli_close($conn);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", {packages:['corechart']});
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable (<?php echo json_encode($dataArray); ?>);

   
    
    var options = {
      title: "Valor das compras (R$)",
      width: 900,
      height: 300,
      legend: { position: "none" },
      hAxis: {title: "Mês",format: "MMM yyyy" },
      vAxis: {title: "Valor total das compras"},
      colors: ['#e53935', '#cccccc'],
      bar: { groupWidth: '90%' }
      
    };
   

    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
    
    chart.draw(data, options);
  }
</script>
<div id="columnchart_values" style="width: 900px; height: 300px;"></div>



<?php
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
?>
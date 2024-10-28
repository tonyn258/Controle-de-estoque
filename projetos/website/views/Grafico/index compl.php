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

?>
<!-- Grafico HTML -->
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
          ['Month', 'Compras', 'Vendas', 'Lucros'],
          ['2023/01',  136,      691,         629],
          ['2023/02',  136,      691,         629],
          ['2023/03',  136,      691,         629],
          ['2023/04',  136,      691,         629],
          ['2023/05',  165,      938,         522],
          ['2023/06',  135,      1120,        599],
          ['2023/07',  157,      1167,        587],
          ['2023/08',  139,      1110,        615],
          ['2023/09',  136,      691,         629],
          ['2023/10',  136,      691,         629],
          ['2023/11',  136,      691,         629],
          ['2023/12',  136,      691,         629]
        ]);


        

        var options = {
          title : 'Compras Realizadas',
          width: 1000,
          height: 300,     
          vAxis: {title: 'R$'},
          hAxis: {title: 'Mes'},
          seriesType: 'bars',
          series: {5: {type: 'line'}},
          //series: {
           // 0: {color: '#7D1CE8'},
           // 1: {color: '#40EA6E'},
           // 2: {color: '#EB442F'}
         // }
         bar: { groupWidth: '90%' }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>



<?php
echo '</section>';
echo '</div>';
echo $footer;
echo $javascript;
?>
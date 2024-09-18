<?php
// Inclua os arquivos necessários.
require_once '../../App/auth.php';
require_once '../../layout/script.php';


// Cabeçalho de impressão, barra de navegação e barra lateral.
echo $head;
echo $header;
echo $aside;

// Seção de conteúdo de impressão.
echo '<div class="content-wrapper">
    <section class="content-header">
        <h1>Gráfico</h1>
        <ol class="breadcrumb">
            <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Produtos</li>
        </ol>
    </section>
    
    <section class="content">
        <div id="columnchart_values" style="width: 900px; height: 300px;"></div>
    </section>
</div>';

echo $footer;
echo $javascript;
?>

<style>
    #columnchart_values {
        width: 900px;
        height: 300px;
    }
</style>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {
        packages: ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Element", "Density", {role: "style"}],
            ["Copper", 8.94, "#b87333"],
            ["Silver", 10.49, "silver"],
            ["Gold", 19.30, "gold"],
            ["Platinum", 21.45, "color: #e5e4e2"]



        ]);

        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
            {
                calc: "stringify",
                sourceColumn: 1,
                type: "string",
                role: "annotation"
            },
            2
        ]);

        var options = {
            title: "Compras Realizadas",
            width: 800,
            height: 400,
            bar: {
                groupWidth: "50%"
            },
            legend: {
                position: "none"
            },
            backgroundColor: {
                fill: 'linear-gradient(45deg, #2196F3 0%, #E91E63 100%)',
                stroke: 'black',
                strokeWidth: 2,
                color: '#ddd'
            },
            is3D: true // Define o fundo como 3D

        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
        chart.draw(view, options);

    }
</script>
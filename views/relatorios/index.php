<?php
require_once '../../App/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Sistema de Controle</title>
    
    <!-- Import Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/relatorios.css">

    <style>
        /* Estilos de Layout (Baseado no Catálogo) */
        :root {
            --bg-body: #f4f6f9;
            --font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
            --dark: #343a40;
            --secondary: #6c757d;
            --primary: #007bff;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: var(--font-family); 
            background-color: var(--bg-body); 
            color: #333; 
            line-height: 1.5; 
        }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        
        .btn-back { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: var(--secondary); 
            text-decoration: none; 
            font-weight: 500; 
            font-size: 1rem;
            transition: color 0.2s;
        }
        .btn-back:hover { color: var(--primary); }
        
        .page-header { 
            text-align: center; 
            margin-bottom: 40px; 
        }
        .page-header h1 { 
            font-size: 2rem; 
            margin-bottom: 5px; 
            color: var(--dark); 
        }
        .page-header small { 
            color: var(--secondary); 
            font-size: 1rem; 
            font-weight: normal; 
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="btn-back">🏠 Voltar ao Início</a>

    <header class="page-header">
        <h1>
            📊 Relatórios
            <small>Central de Análise</small>
        </h1>
    </header>

    <div class="report-grid">
        <!-- Relatório de Vendas -->
        <a href="view_vendas.php" class="report-card">
            <div class="report-icon">📋</div>
            <div class="report-info">
                <h3>Relatório de Vendas</h3>
                <p>Listagem detalhada de transações</p>
            </div>
        </a>

        <!-- Gráficos -->
        <a href="dashboard.php" class="report-card">
            <div class="report-icon">📈</div>
            <div class="report-info">
                <h3>Gráficos & Dashboard</h3>
                <p>Análise visual de desempenho</p>
            </div>
        </a>
    </div>
</div>

<!-- Import Custom JS -->
<script src="../../assets/js/relatorios.js"></script>

</body>
</html>
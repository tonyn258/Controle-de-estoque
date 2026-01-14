<?php
require_once '../App/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vendas</title>
    <style>
        body {
            margin: 0;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
        }

        .content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-container {
            background: #ffffff;
            width: 100%;
            max-width: 1100px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            margin: 20px;
        }

        .dashboard-header h1 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 2.5rem;
            letter-spacing: -0.5px;
        }

        .dashboard-header .subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            padding: 10px;
        }

        .menu-card {
            background: #ffffff;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 35px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: #3c8dbc;
        }

        .menu-card .icon-wrapper {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .icon-wrapper {
            transform: scale(1.1);
        }

        .menu-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #34495e;
            transition: color 0.3s ease;
        }

        .menu-card:hover .card-title {
            color: #3c8dbc;
        }

        .dashboard-footer {
            margin-top: 50px;
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
            color: #95a5a6;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<section class="content">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Sistema de Vendas</h1>
            <p class="subtitle">Controle de Estoque e Vendas</p>
        </div>

        <div class="menu-grid">
            <a href="sales/" class="menu-card">
                <div class="icon-wrapper">🛒</div>
                <span class="card-title">Nova Venda</span>
            </a>

            <a href="Grafico/dashboard.php" class="menu-card">
                <div class="icon-wrapper">📊</div>
                <span class="card-title">Relatórios</span>
            </a>

            <a href="cliente/" class="menu-card">
                <div class="icon-wrapper">👥</div>
                <span class="card-title">Clientes</span>
            </a>

            <a href="usuarios/" class="menu-card">
                <div class="icon-wrapper">👤</div>
                <span class="card-title">Usuários</span>
            </a>

            <a href="produto/" class="menu-card">
                <div class="icon-wrapper">📦</div>
                <span class="card-title">Produtos</span>
            </a>

            <a href="catalogo/" class="menu-card">
                <div class="icon-wrapper">🛍️</div>
                <span class="card-title">Catálogo</span>
            </a>

            <a href="compras/" class="menu-card">
                <div class="icon-wrapper">🧾</div>
                <span class="card-title">Compras</span>
            </a>

            <a href="estoque/index.php" class="menu-card">
                <div class="icon-wrapper">🏬</div>
                <span class="card-title">Estoque</span>
            </a>
        </div>

        <div class="dashboard-footer">
            <p>Sistema desenvolvido com PHP 8+ e MySQL</p>
            <p>Acesse via: <strong>http://localhost/xampp/htdocs/www/projetos/website/</strong></p>
        </div>
    </div>
</section>

</body>
</html>
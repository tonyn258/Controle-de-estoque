<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Sistema de Vendas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center; max-width: 500px; width: 90%; }
        h1 { color: #333; margin-bottom: 30px; font-size: 2.5em; }
        .menu-item { display: block; background: #007bff; color: white; text-decoration: none; padding: 15px 25px; margin: 15px 0; border-radius: 8px; font-size: 18px; transition: all 0.3s; }
        .menu-item:hover { background: #0056b3; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,123,255,0.3); }
        .menu-item.back { background: #6c757d; }
        .menu-item.back:hover { background: #5a6268; }
        
        /* Estilo para Legacy/Antigo */
        .legacy-section { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .legacy-title { font-size: 0.9em; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .menu-item.legacy { background: #f8f9fa; color: #666; border: 1px solid #ddd; font-size: 16px; }
        .menu-item.legacy:hover { background: #e2e6ea; color: #333; transform: none; box-shadow: none; }
        
        .icon { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Relatórios</h1>
        <p style="margin-bottom: 30px; color: #666;">Análise de Vendas e Dados</p>
        
        <a href="../" class="menu-item back">
            <span class="icon">🏠</span>Menu Principal
        </a>
        
        <!-- Relatórios Atuais -->
        <a href="view_vendas.php" class="menu-item">
            <span class="icon">📋</span>Relatório de Vendas
        </a>
        
        <a href="dashboard.php" class="menu-item">
            <span class="icon">📊</span>Gráficos
        </a>
        
     
    </div>
</body>
</html>
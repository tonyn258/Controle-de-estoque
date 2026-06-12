<?php
require_once '../App/auth.php';

// Define user role label and class based on permission
$userRole = ($perm == 1) ? 'Administrador' : 'Usuário';
$roleClass = ($perm == 1) ? 'badge-admin' : 'badge-user';
// Handle user image
$userImage = !empty($foto) && file_exists('../uploads/usuarios/' . $foto) 
    ? '../uploads/usuarios/' . $foto 
    : 'https://ui-avatars.com/api/?name=' . urlencode($usuario) . '&background=random&color=fff';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Vendas</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables for easy theming */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #f1f5f9;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --sidebar-bg: #ffffff;
            --sidebar-width: 260px;
            --header-height: 70px;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
            height: 100vh;
            overflow: hidden;
        }

        /* Layout Structure */
        .app-container {
            display: flex;
            height: 100%;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            z-index: 1000;
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px;
            list-style: none;
        }

        .menu-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            margin-top: 20px;
            padding-left: 12px;
        }
        
        .menu-label:first-child {
            margin-top: 0;
        }

        .menu-item {
            margin-bottom: 4px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            gap: 12px;
        }

        .menu-link:hover, .menu-link.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .menu-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* User Profile in Sidebar Footer */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            width: fit-content;
            margin-top: 2px;
            font-weight: 500;
        }

        .badge-admin {
            background-color: #e0e7ff;
            color: var(--primary-color);
        }

        .badge-user {
            background-color: #f1f5f9;
            color: var(--text-muted);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .top-header {
            height: var(--header-height);
            background-color: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: var(--text-muted);
            display: none;
        }

        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
        }

        .dashboard-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: var(--text-color);
            transition: var(--transition);
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 16px;
            transition: var(--transition);
        }

        .dashboard-card:hover .card-icon {
            background-color: var(--primary-color);
            color: #fff;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .card-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                left: -100%;
                height: 100%;
                box-shadow: var(--shadow-md);
            }

            .sidebar.active {
                left: 0;
            }

            .toggle-sidebar {
                display: block;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body>

<div class="app-container">
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="brand-logo">
                <i class="fa-solid fa-cubes"></i>
                <span>EstoquePro</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Principal</li>
            <li class="menu-item">
                <a href="#" class="menu-link active">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="sales/" class="menu-link">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Nova Venda</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="relatorios/index.php" class="menu-link">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Relatórios</span>
                </a>
            </li>

            <li class="menu-label">Gestão</li>
            <li class="menu-item">
                <a href="cliente/" class="menu-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="produto/" class="menu-link">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Produtos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="catalogo/" class="menu-link">
                    <i class="fa-solid fa-tags"></i>
                    <span>Catálogo</span>
                </a>
            </li>
            
            <?php if($perm == 1): ?>
            <li class="menu-label">Administração</li>
            <li class="menu-item">
                <a href="usuarios/" class="menu-link">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Usuários</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <div class="user-profile">
                <img src="<?= htmlspecialchars($userImage) ?>" alt="User" class="user-avatar">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($usuario) ?></span>
                    <span class="user-role <?= $roleClass ?>"><?= $userRole ?></span>
                </div>
                <a href="../login.php" title="Sair" style="margin-left: auto; color: var(--text-muted);"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-header">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title">
                <h2 style="font-size: 1.25rem; font-weight: 600;">Visão Geral</h2>
            </div>
        </header>

        <div class="page-content">
            <div class="dashboard-grid">
                <a href="sales/" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-cart-plus"></i>
                    </div>
                    <h3 class="card-title">Nova Venda</h3>
                    <p class="card-desc">Registrar saída de produtos</p>
                </a>

                <a href="relatorios/index.php" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="card-title">Relatórios</h3>
                    <p class="card-desc">Análise de desempenho</p>
                </a>

                <a href="cliente/" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <h3 class="card-title">Clientes</h3>
                    <p class="card-desc">Gerenciar base de clientes</p>
                </a>

                <a href="produto/" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <h3 class="card-title">Produtos</h3>
                    <p class="card-desc">Controle de estoque</p>
                </a>

                <a href="catalogo/" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h3 class="card-title">Catálogo</h3>
                    <p class="card-desc">Visualizar itens disponíveis</p>
                </a>

                <?php if($perm == 1): ?>
                <a href="usuarios/" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h3 class="card-title">Usuários</h3>
                    <p class="card-desc">Gestão de acesso</p>
                </a>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 40px; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                <p>&copy; <?= date('Y') ?> Sistema de Vendas. Todos os direitos reservados.</p>
            </div>
        </div>
    </main>
</div>

<script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function toggleMenu() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    toggleBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
</script>

</body>
</html>
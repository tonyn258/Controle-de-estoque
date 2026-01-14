<?php
$url = 'http://localhost/www/projetos/website/views/'; // Remova em caso de utilizar o código para hospedagem web

// Lógica de Permissão para exibição no Header
$roleLabel = 'Cliente';
if (isset($perm)) {
    switch ($perm) {
        case 1: $roleLabel = 'Administrador'; break;
        case 2: $roleLabel = 'Vendedor'; break;
    }
}

// HEAD
$head = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="content-language" content="pt-br" /> 
  <title>Sistema de Controle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{$url}bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="{$url}dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="{$url}dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="{$url}plugins/iCheck/flat/blue.css">
  <link rel="stylesheet" href="{$url}plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="{$url}plugins/daterangepicker/daterangepicker.css"> 
  <link rel="stylesheet" href="{$url}plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">

  <!-- Custom Styles for Modern Look -->
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
    .content-wrapper { background-color: #ecf0f5; }
    .box { border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 3px solid #d2d6de; }
    .box-header { border-bottom: 1px solid #f4f4f4; }
    .btn { border-radius: 3px; box-shadow: none; }
    .main-header .logo { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
HTML;

// HEADER
$header = <<<HTML
<header class="main-header">
<!-- Logo -->
<a href="{$url}" class="logo">
  <!-- mini logo for sidebar mini 50x50 pixels -->
  <span class="logo-mini"><b>S</b>CE</span>
  <!-- logo for regular state and mobile devices -->
  <span class="logo-lg"><b>SCE</b> System</span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
  <!-- Sidebar toggle button-->
  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
  </a>

  <div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
      <!-- Messages -->
      <li class="dropdown messages-menu">
        
        <ul class="dropdown-menu">
          <li class="header">You have 4 messages</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li><!-- start message -->
                <a href="#">
                  <div class="pull-left">
                    <img src="{$url}dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                  </div>
                  <h4>
                    Support Team
                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="footer"><a href="#">See All Messages</a></li>
        </ul>
      </li>
      <!-- Notifications -->
      <li class="dropdown notifications-menu">
        
        <ul class="dropdown-menu">
          <li class="header">You have 10 notifications</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li>
                <a href="#">
                  <i class="fa fa-users text-red"></i> 5 new members joined
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-user text-red"></i> You changed your username
                </a>
              </li>
            </ul>
          </li>
          <li class="footer"><a href="#">View all</a></li>
        </ul>
      </li>
      <!-- Tasks -->
      <li class="dropdown tasks-menu">
       
        </a>
        <ul class="dropdown-menu">
          <li class="header">You have 9 tasks</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li><!-- Task item -->
                <a href="#">
                  <h3>
                    Design some buttons
                    <small class="pull-right">20%</small>
                  </h3>
                  <div class="progress xs">
                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">20% Complete</span>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
          <li class="footer">
            <a href="#">View all tasks</a>
          </li>
        </ul>
      </li>
      <!-- User Account -->
      <li class="dropdown user user-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <img src="{$url}{$foto}" class="user-image" alt="User Image">
          <span class="hidden-xs">{$usuario}</span>
        </a>
        <ul class="dropdown-menu">
          <!-- User image -->
          <li class="user-header">
            <img src="{$url}{$foto}" class="img-circle" alt="User Image">
            <p>
              {$usuario} - {$roleLabel}
              <small>Member since Nov. 2012</small>
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer">
            <div class="pull-left">
              <a href="{$url}usuarios/profile.php" class="btn btn-default btn-flat">Profile</a>
            </div>
            <div class="pull-right">
              <a href="{$url}destroy.php" class="btn btn-default btn-flat">Sign out</a>
            </div>
          </li>
        </ul>
      </li>
      <!-- Control Sidebar Toggle Button -->
      <li>
        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
      </li>
    </ul>
  </div>
</nav>
</header>
HTML;

// ASIDE
$aside = '
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="' . $url . $foto . '" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>' . $usuario . '</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
      </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">NAVEGAÇÃO PRINCIPAL</li>

      <li class="treeview">
        <a href="' . $url . '">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-table"></i> <span>Relatórios</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'relatorios/"><i class="fa fa-circle-o"></i> Relatórios</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-cube"></i> <span>Produtos</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'produto/"><i class="fa fa-circle-o"></i> Lista</a></li>
          <li><a href="' . $url . 'produto/addproduto.php"><i class="fa fa-circle-o"></i> Adicionar Produto</a></li>
          <li><a href="' . $url . 'catalogo/"><i class="fa fa-circle-o"></i> Catálogo</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-shopping-cart"></i> <span>Compras</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'compras/"><i class="fa fa-circle-o"></i> Lista</a></li>
          <li><a href="' . $url . 'compras/addcompra.php"><i class="fa fa-circle-o"></i> Adicionar Compra</a></li>
          <li><a href="' . $url . 'estoque/index.php"><i class="fa fa-circle-o"></i> Estoque</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-money"></i> <span>Vendas</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'vendasView/indexView4.php"><i class="fa fa-circle-o"></i> Visualizar Vendas</a></li>
          <li><a href="' . $url . 'sales/"><i class="fa fa-circle-o"></i> Nova Venda</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-users"></i> <span>Clientes</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'cliente/"><i class="fa fa-circle-o"></i> Lista</a></li>
          <li><a href="' . $url . 'cliente/addcliente.php"><i class="fa fa-circle-o"></i> Adicionar Cliente</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-user"></i> <span>Usuários</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'usuarios/"><i class="fa fa-circle-o"></i> Lista</a></li>
          <li><a href="' . $url . 'usuarios/addusuarios.php"><i class="fa fa-circle-o"></i> Adicionar Usuário</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-bar-chart"></i> <span>Gráficos</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="' . $url . 'Grafico/dashboard.php"><i class="fa fa-circle-o"></i> Dashboard</a></li>
        </ul>
      </li>

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>';

$footer = '<footer class="main-footer">
<div class="pull-right hidden-xs">
  <b>Version</b> 2.3.8
</div>
<strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
reserved.
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Create the tabs -->
<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
  <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
  <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
  <!-- Home tab content -->
  <div class="tab-pane" id="control-sidebar-home-tab">
    <h3 class="control-sidebar-heading">Recent Activity</h3>
    <ul class="control-sidebar-menu">
      <li>
        <a href="javascript:void(0)">
          <i class="menu-icon fa fa-birthday-cake bg-red"></i>

          <div class="menu-info">
            <h4 class="control-sidebar-subheading">Langdon\'s Birthday</h4>

            <p>Will be 23 on April 24th</p>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <i class="menu-icon fa fa-user bg-yellow"></i>

          <div class="menu-info">
            <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

            <p>New phone +1(800)555-1234</p>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

          <div class="menu-info">
            <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

            <p>nora@example.com</p>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <i class="menu-icon fa fa-file-code-o bg-green"></i>

          <div class="menu-info">
            <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

            <p>Execution time 5 seconds</p>
          </div>
        </a>
      </li>
    </ul>
    <!-- /.control-sidebar-menu -->

    <h3 class="control-sidebar-heading">Tasks Progress</h3>
    <ul class="control-sidebar-menu">
      <li>
        <a href="javascript:void(0)">
          <h4 class="control-sidebar-subheading">
            Custom Template Design
            <span class="label label-danger pull-right">70%</span>
          </h4>

          <div class="progress progress-xxs">
            <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <h4 class="control-sidebar-subheading">
            Update Resume
            <span class="label label-success pull-right">95%</span>
          </h4>

          <div class="progress progress-xxs">
            <div class="progress-bar progress-bar-success" style="width: 95%"></div>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <h4 class="control-sidebar-subheading">
            Laravel Integration
            <span class="label label-warning pull-right">50%</span>
          </h4>

          <div class="progress progress-xxs">
            <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
          </div>
        </a>
      </li>
      <li>
        <a href="javascript:void(0)">
          <h4 class="control-sidebar-subheading">
            Back End Framework
            <span class="label label-primary pull-right">68%</span>
          </h4>

          <div class="progress progress-xxs">
            <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
          </div>
        </a>
      </li>
    </ul>
    <!-- /.control-sidebar-menu -->

  </div>
  <!-- /.tab-pane -->
  <!-- Stats tab content -->
  <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
  <!-- /.tab-pane -->
  <!-- Settings tab content -->
  <div class="tab-pane" id="control-sidebar-settings-tab">
    <form method="post">
      <h3 class="control-sidebar-heading">General Settings</h3>

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Report panel usage
          <input type="checkbox" class="pull-right" checked>
        </label>

        <p>
          Some information about this general settings option
        </p>
      </div>
      <!-- /.form-group -->

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Allow mail redirect
          <input type="checkbox" class="pull-right" checked>
        </label>

        <p>
          Other sets of options are available
        </p>
      </div>
      <!-- /.form-group -->

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Expose author name in posts
          <input type="checkbox" class="pull-right" checked>
        </label>

        <p>
          Allow the user to show his name in blog posts
        </p>
      </div>
      <!-- /.form-group -->

      <h3 class="control-sidebar-heading">Chat Settings</h3>

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Show me as online
          <input type="checkbox" class="pull-right" checked>
        </label>
      </div>
      <!-- /.form-group -->

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Turn off notifications
          <input type="checkbox" class="pull-right">
        </label>
      </div>
      <!-- /.form-group -->

      <div class="form-group">
        <label class="control-sidebar-subheading">
          Delete chat history
          <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
        </label>
      </div>
      <!-- /.form-group -->
    </form>
  </div>
  <!-- /.tab-pane -->
</div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar\'s background. This div must be placed
   immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>';

$javascript = '
</div>

<!-- jQuery 3.6.0 (mais recente) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI 1.11.4 -->
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge(\'uibutton\', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="' . $url . 'bootstrap/js/bootstrap.min.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="' . $url . 'plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="' . $url . 'plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="' . $url . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="' . $url . 'plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="' . $url . 'plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="' . $url . 'dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="' . $url . 'dist/js/demo.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
<!-- DataTables JS -->
<script type="text/javascript" src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>

</body>
</html>
';
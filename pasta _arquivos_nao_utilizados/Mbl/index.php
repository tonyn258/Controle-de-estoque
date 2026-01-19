<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/produto.class.php';

echo $head;
echo $header;
echo $aside;
echo '<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Usuário
  </h1>
  <ol class="breadcrumb">
    <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Produto</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  ';
require '../../layout/alert.php';
echo '
  <!-- Small boxes (Stat box) -->
  <div class="row">
   <div class="box box-primary">

    <div class="box-header">
      <i class="ion ion-clipboard"></i>
      <h3 class="box-title">Lista de Produto8</h3>            
    </div>

    <!-- /.box-header -->
    <div class="box-body">    
      
     
    ';
echo '

<!DOCTYPE html>
 <html lang="en">
 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Posts - My Meli Application</title>
   <link rel="stylesheet" type="text/css" href="/css/style.css" />
 </head>
 <body>
   <div class="container">
     <div class="items-list">
       <% for (item of items) { %>
         <a target="_blank" rel="noopener noreferrer nofollow" href="<%= item.permalink %>">
           <div>
             <img src="<%= item.secure_thumbnail %>" />
             <h3><%= item.title %></h3>
           </div>
         </a>
       <% } %>
     </div>
   </div>
 </body>
 </html>     

';
$produto = new Produto;
$resp =  $produto->index();
$resps = json_decode($resp, true);
foreach ($resps as $row) {
}
echo '

        </tbody>
        
        <!-- Fim da tabela -->
        <br/>
        <!-- /.box-body -->
        <div class="left">
         <form action="index.php" method="post">
           <a href="addproduto.php" type="button" class="btn btn-success pull-right"><i
            class="fa fa-plus"></i> Add Produto</a>
         </div>
       </div>
       ';
echo '</div>';
echo '</section>';
echo '</div>';
echo  $footer;
echo $javascript;

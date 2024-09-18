<?php
require_once '../../App/auth.php';
require_once '../../layout/script.php';
require_once '../../App/Models/usuario.class.php';
// Criação das seções do layout
echo $head;
echo $header;
echo $aside;
// Criação da seção de conteúdo
echo '<div class="content-wrapper">
  <section class="content-header">
    <h1>Usuário</h1>
    <ol class="breadcrumb">
      <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Usuario</li>
    </ol>
  </section>


<section class="content">';
// Inclusão de alertas de mensagens
  require '../../layout/alert.php';

  echo '<div class="row">
    <div class="box box-primary">
      <div class="box-header">
        <i class="ion ion-clipboard"></i>
        <h3 class="box-title">Lista de Usuário</h3>
      </div>
    

      <div class="box-body">
      <table id="example1" class="table table-bordered table-striped dataTable" role="grid" 
      aria-describedby="example1_info">
        <thead>
        <tr>
        <th>#</th>
        <th>Nome Cliente</th>
        <th>Email</th>
        <th>Data de Registro</th>
        <th>Permissão</th>
      </tr>
    </thead>
    <tbody>';
     // Obtenção de dados dos usuários
        //$usuario = new Usuario;
       $resp =  $usuario->index($perm);
       $resps = json_decode($resp, true);

       // Loop para exibição dos usuários na tabela
       foreach ($resps as $row){
        if(isset($row['idUser']) != NULL){
          echo '<tr>';
          echo '<td>' .$row['idUser'] . '</td>';
          echo '<td>' . $row['Username'] . '</td>';
          echo '<td>' . $row['Email'] . '</td>';
          echo '<td>' . $row['DataRegistro'] . '</td>';
          echo '<td>' . $row['Permissão'] . '</td>';
          echo '</tr>';
        }
      }

        echo '</tbody>
        </table>
            </div>
            
                <div class="left">
                  <form action="index.php" method="post">
                    <a href="addusuarios.php" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Usuario</a>
                  </form> 
                </div>
            </div>';
  echo '</div>';
  echo '</section>';          
  echo '</div>'; 
  // Criação das seções de rodapé e scripts 
  echo  $footer;
  echo $javascript;
  ?>
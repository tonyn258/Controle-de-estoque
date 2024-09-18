<?php
  require_once '../../App/auth.php';
  require_once '../../layout/script.php';
  echo $head;
  echo $header;
  echo $aside; 
  echo '<div class="content-wrapper">'; 
 
 require '../../layout/alert.php';

if($perm != 1){
          echo "Você não tem permissão! </div>";

          exit();
        }

  echo '<!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Adicionar <small>Produto</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="../"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Produto</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">';

  echo ' <a href="./" class="btn btn-success">Voltar</a>
        <div class="row">
          <!-- left column -->
          <div class="col-md-9">
            <!-- general form elements -->
            <div class="box box-solid box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Produto</h3>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
              <form role="form" action="../../App/Database/InsertProduto.php" method="POST">
                <div class="box-body">
               
                  <div class="form-group row">

                    <div class="col-sm-2">
                      <label for="exampleInputEmail1">SKU</label>
                      <input type="text" name="skuProduto" class="form-control" id="exampleInputEmail1" placeholder="sku">
                    </div>

                    <div class="col-sm-2">
                      <label for="exampleInputEmail1">Variação</label>
                      <input type="text" name="model" class="form-control" id="exampleInputEmail1" placeholder="Variação">
                        
                    </div>

                    <div class="col-sm-8">
                      <label for="exampleInputEmail1">Nome do Produto</label>
                      <input type="text" name="NomeProduto" class="form-control" id="exampleInputEmail1" placeholder="Nome do Produto">
                    </div>
                  </div>

                  <div class="form-group row">

                    <div class="col-sm-4">
                      <label for="exampleInputEmail1">EAN</label>
                      <input type="number" name="Quantidade" class="form-control" id="exampleInputEmail1" placeholder="EAN">
                    </div>

                    <div class="col-sm-4">
                      <label for="exampleInputEmail1">Categoria</label>
                      <select name="Conexao" class="form-control" required>
                      <option value="">Selecione</option>
                      <option value="AcessoriosVeiculos">Acessórios para Veículos</option>
                      <option value="Beleza e Cuidado Pessoal">Beleza e Cuidado Pessoal</option>
                      <option value="CasaMoveisDecoracao">Casa, Móveis e Decoração</option>
                      <option value="Construcao">Construção</option>
                      <option value="Ferramentas">Ferramentas</option>
                      <option value="Informatica">Informática</option>
                      <option value="Wi-Fi">Wi-Fi</option>
                      <option value="ZigBee">ZigBee</option>
                                           
                        </select>
                    </div>

                    <div class="col-sm-3">
                      <label for="exampleInputEmail1">Marca</label>
                      <input type="text" name="Marca" class="form-control" id="exampleInputEmail1" placeholder="Marca">
                    </div>

                  </div>
                  

                <input type="hidden" name="iduser" value="'.$idUsuario.'">
                  
                <!-- /.box-body -->

                <div class="box-footer">
                  <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">Cadastrar</button>
                  <a class="btn btn-danger" href="../../views/cliente">Cancelar</a>
                </div>
              </form>
            </div>
            <!-- /.box -->
            </div>
        </div>';

  echo '</div>';
  echo '</div>';
  echo '</section>';
  echo '</div>';
  echo  $footer;
  echo $javascript;
  ?>
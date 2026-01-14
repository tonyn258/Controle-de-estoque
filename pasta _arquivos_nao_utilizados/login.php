<!DOCTYPE html>
<html lang="br">

<head>
  <link rel="stylesheet" href='css/login.css'><!-- importação do arquivo CSS -->
  <!--<script src="script/login.js"></script>  importação do arquivo JavaScript -->


  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minha Loja</title>
</head>

<body class="aurora">
  <section>
    <div class="form-box">
      <div class="form-value">
        <form action="App/session.php" method="post" class="form">

          <h2>Login</h2>

          <div class="input-box">
            <ion-icon name="person"></ion-icon>
            <input type="text" id="username" name="username" value="Antonio Carlos" placeholder="Antonio Carlos" class="form-control" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <label for="username"></label>
          </div>

          <div class="input-box">
            <ion-icon name="lock"></ion-icon>
            <input type="password" id="password" name="password" value="admin" placeholder="admin" class="form-control" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <label for="password"></label>
          </div>



          <div class="row">
            <div class="col-xs-4">
              <button>Entrar</button>
            </div>
          </div>

      </div>
      </form>
    </div>
    </div>

  </section>
  <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
  <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
</body>

</html>
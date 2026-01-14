<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Particle Network</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <canvas id="particleCanvas"></canvas>

    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (isset($_GET['alert'])) {
            if ($_GET['alert'] == 1) {
                echo '<p style="color: #dd4b39; margin-bottom: 15px; font-weight: 600;">Usuário não encontrado!</p>';
            } elseif ($_GET['alert'] == 2) {
                echo '<p style="color: #dd4b39; margin-bottom: 15px; font-weight: 600;">Senha incorreta!</p>';
            }
        }
        ?>
    <form action="App/session.php" method="post" class="form">   
        <input type="text" id="username" name="username"placeholder="Nome" class="form-control" required>
        <input type="password" id="password" name="password" placeholder="Senha" class="form-control" required>
        <button>Entrar</button>
    </form>
    </div>

    <script src="assets/js/scriptcanva.js"></script>
</body>
</html>

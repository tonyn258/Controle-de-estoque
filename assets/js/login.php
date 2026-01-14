<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Particle Network</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
            background-color: #f4f6f9;
            color: #2c3e50;
            overflow: hidden;
        }

        canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .login-container {
            position: relative;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(60, 141, 188, 0.4);
            box-shadow: 0 0 20px rgba(60, 141, 188, 0.3), 0 0 40px rgba(60, 141, 188, 0.1);
            z-index: 1;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-weight: 700;
            color: #2c3e50;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d2d6de;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            color: #555;
        }

        .login-container button {
            width: 100%;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #3c8dbc;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #367fa9;
        }
    </style>
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
<?php
if (isset($_POST['cnpj'])) {
    $cnpj = preg_replace('/\D/', '', $_POST['cnpj']); // Remove caracteres não numéricos

    if (strlen($cnpj) !== 14) {
        $erro = "CNPJ inválido!";
    } else {
        $url = "https://www.receitaws.com.br/v1/cnpj/{$cnpj}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['status']) && $data['status'] === "ERROR") {
            $erro = "Erro: " . $data['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CNPJ</title>
    <script>
        function buscarEmpresas(nome) {
            window.location.href = 'buscar_empresas.php?nome=' + encodeURIComponent(nome);
        }
    </script>
</head>

<body>
    <h2>Consultar CNPJ</h2>
    <form method="post">
        <input type="text" name="cnpj" placeholder="Digite o CNPJ" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if (isset($erro)) { ?>
        <p style="color: red;"> <?php echo $erro; ?> </p>
    <?php } elseif (isset($data) && empty($erro)) { ?>
        <h3>Resultado:</h3>
        <p><strong>Razão Social:</strong> <?php echo $data['nome'] ?? 'N/A'; ?></p>
        <p><strong>Nome Fantasia:</strong> <?php echo $data['fantasia'] ?? 'N/A'; ?></p>
        <p><strong>Nome do Proprietário:</strong>
            <a href="#" onclick="buscarEmpresas('<?php echo addslashes($data['qsa'][0]['nome'] ?? 'N/A'); ?>')">
                <?php echo $data['qsa'][0]['nome'] ?? 'N/A'; ?>
            </a>
        </p>
        <p><strong>Situação Cadastral:</strong> <?php echo $data['situacao'] ?? 'N/A'; ?></p>
        <p><strong>Endereço:</strong> <?php echo $data['logradouro'] . ', ' . $data['bairro'] . ', ' . $data['municipio'] . ' - ' . $data['uf']; ?></p>
    <?php } ?>
</body>

</html>
<?php
if (!isset($_GET['nome']) || empty($_GET['nome'])) {
    echo "<p>Nome do sócio não informado.</p>";
    exit;
}

$socio = urlencode($_GET['nome']);
$url = "https://www.receitaws.com.br/v1/socios/{$socio}";
$response = @file_get_contents($url);

if ($response === false) {
    echo "<p>Erro ao buscar informações. O serviço pode estar indisponível.</p>";
    exit;
}

$dados = json_decode($response, true);

echo "<h2>Empresas do Sócio: {$_GET['nome']}</h2>";

if (isset($dados['empresas']) && is_array($dados['empresas']) && count($dados['empresas']) > 0) {
    echo "<ul>";
    foreach ($dados['empresas'] as $empresa) {
        echo "<li><strong>{$empresa['nome']}</strong> - CNPJ: {$empresa['cnpj']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Não foram encontradas outras empresas para este sócio.</p>";
}

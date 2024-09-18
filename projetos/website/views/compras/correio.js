// Função para buscar o status do rastreamento e atualizar a tabela
function buscarStatusRastreio(codRastreio, linha) {
  // URL da API dos Correios
  const url = `https://api.linketrack.com/track/json?user=[seu_usuario]&key=[sua_chave]&objetos=${codRastreio}`;

  // Realiza a requisição utilizando a biblioteca cURL do PHP
  const curl = curl_init();
  curl_setopt(curl, CURLOPT_URL, url);
  curl_setopt(curl, CURLOPT_RETURNTRANSFER, true);
  const response = curl_exec(curl);
  curl_close(curl);

  // Converte a resposta da API para JSON
  const json = JSON.parse(response);

  // Verifica se o rastreamento foi encontrado
  if (json && json.length > 0) {
    // Extrai o último evento do rastreamento
    const evento = json[0].track[0];

    // Atualiza a coluna "Status de Rastreio" na tabela
    linha.find('.status-rastreio').html(evento.status);
  }
}

// Percorre todas as linhas da tabela e busca o status do rastreio
$('#example1 tbody tr').each(function() {
  const codRastreio = $(this).find('td:nth-child(4)').text();
  const linha = $(this);
  buscarStatusRastreio(codRastreio, linha);
});

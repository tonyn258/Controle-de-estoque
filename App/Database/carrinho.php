<?php
require_once __DIR__ . '/../auth.php';// Inclui o arquivo 'auth.php' que contém a lógica de autenticação
require_once __DIR__ . '/../Models/vendas.class.php';// Inclui o arquivo 'vendas.class.php' que contém a definição da classe Vendas
require_once __DIR__ . '/../Models/compras.class.php';

if (!isset($_SESSION['itens'])) {
    $_SESSION['itens'] = array();// Inicializa a variável de sessão 'itens' como um array vazio, se ainda não estiver definida
}

$TaxaM  = isset($_SESSION['taxas'])  ? $_SESSION['taxas']  : array();    // Inicializa a variável 'TaxaM' com o valor da sessão 'taxas', ou um array vazio se não estiver definida
$Fretee = isset($_SESSION['fretes']) ? $_SESSION['fretes'] : array();// Inicializa a variável 'Fretee' com o valor da sessão 'fretes', ou um array vazio se não estiver definida
$Vendaa = isset($_SESSION['vendas']) ? $_SESSION['vendas'] : array();// Inicializa a variável 'Vendaa' com o valor da sessão 'vendas', ou um array vazio se não estiver definida

// Função auxiliar para converter moeda (ex: 1.000,00 ou 234,00) para float
function toFloat($str) {
    if(is_numeric($str)) return (float)$str;
    // Remove ponto de milhar se houver e troca vírgula decimal por ponto
    return (float)str_replace(',', '.', str_replace('.', '', (string)$str));
}

if (isset($_POST['prodSubmit']) && $_POST['prodSubmit'] == "carrinho") {
    // Verifica se o formulário foi enviado e se o valor de 'prodSubmit' é igual a "carrinho"
    $qtd = $_POST['qtd'];// Obtém o valor do campo 'qtd' do formulário
    $idProduto = $_POST['idItem'];// Obtém o valor do campo 'idItem' do formulário

    $_SESSION['itens'][$idProduto] = $qtd;// Armazena a quantidade do produto na sessão 'itens'
    // Converte os valores para float antes de salvar
    // $Vendaa[$idProduto] = isset($_POST['Venda']) ? toFloat($_POST['Venda']) : 0;
    $compras = new Compras();
    $dadosProd = $compras->EditCompras($idProduto);
    if (isset($_POST['Venda'])) {
        $Vendaa[$idProduto] = toFloat($_POST['Venda']);
    } elseif (is_array($dadosProd) && isset($dadosProd['compras']['ValorVenda'])) {
        $Vendaa[$idProduto] = toFloat($dadosProd['compras']['ValorVenda']);
    } else {
        $Vendaa[$idProduto] = 0;
    }

    $_SESSION['taxas']  = $TaxaM;    // Atualiza a sessão 'taxas' com o valor da variável 'TaxaM'
    $_SESSION['fretes'] = $Fretee; // Atualiza a sessão 'fretes' com o valor da variável 'Fretee'
    $_SESSION['vendas'] = $Vendaa;// Atualiza a sessão 'vendas' com o valor da variável 'Vendaa'    

}
// Definir os valores das variáveis
$pkCount = (is_array($_SESSION['itens']) ? count($_SESSION['itens']) : 0);

if ($pkCount == 0) {// Exibe a mensagem de "Carrinho Vazio" se o contador for igual a 0
    echo ' Carrinho Vazios</br> ';
} else {

    $vendas = new Vendas;// Cria uma nova instância da classe Vendas
    $cont = 1;// Inicializa o contador como 1
    $nomesProdutos = array(); // Array para armazenar os nomes dos produtos
    $sumVendaa = 0;// Variável para armazenar a soma das vendas    

    foreach ($_SESSION['itens'] as $produtos => $quantidade) {
        $NomeProduto = $vendas->itemNome($produtos);// Obtém o nome do produto usando o método 'itemNome' da classe Vendas
        $nomesProdutos[$produtos] = $NomeProduto; // Armazena o nome do produto no array 'nomesProdutos'

        if (!empty($NomeProduto)) { // Exibir o nome do produto apenas se ele existir
            $valorUnitario = (float)$Vendaa[$produtos];
            $subTotal = $valorUnitario * (int)$quantidade;
            echo '<tr>
			<td>' . $cont .       '</td>
			<td>' . $produtos .   '</td>
            <td>' . $NomeProduto .'</td>
			<td>' . $quantidade . '</td>
            <td>R$ ' . number_format($subTotal, 2, ',', '.') . ' <small style="color:#999; font-size:0.8em;">('. number_format($valorUnitario, 2, ',', '.') .' un.)</small></td>			

			<td>
			<input type="hidden" id="idItem" name="idItem[' . $produtos . ']" value="' . $produtos . '" />
			<input type="hidden" id="qtd"    name="qtd[' . $produtos . ']"    value="' . $quantidade . '" />
            <input type="hidden" name="Vd_Tax_Array[' . $produtos . ']" value="' . $Vendaa[$produtos] . '" />
			<a href="../../App/Database/remover.php?remover=carrinho&id=' . $produtos . '" style="text-decoration: none; font-size: 1.2rem;" title="Excluir Item">🗑️</a></td>
			</tr>';

            $sumVendaa += $subTotal;// Incrementa a venda na soma das vendas            
            $cont = $cont + 1; // Incrementa o contador        
        }
    } 
    echo '<tr>
        <td colspan="4"></td>
        <td><b>R$ ' . number_format($sumVendaa, 2, ',', '.') . '</b></td>
        <td></td>
    </tr>';     
}

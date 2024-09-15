<?php
require_once '../auth.php';// Inclui o arquivo 'auth.php' que contém a lógica de autenticação
require_once '../../App/Models/vendas.class.php';// Inclui o arquivo 'vendas.class.php' que contém a definição da classe Vendas

if (!isset($_SESSION['itens'])) {
    $_SESSION['itens'] = array();// Inicializa a variável de sessão 'itens' como um array vazio, se ainda não estiver definida
}

$TaxaM  = isset($_SESSION['taxas'])  ? $_SESSION['taxas']  : array();    // Inicializa a variável 'TaxaM' com o valor da sessão 'taxas', ou um array vazio se não estiver definida
$Fretee = isset($_SESSION['fretes']) ? $_SESSION['fretes'] : array();// Inicializa a variável 'Fretee' com o valor da sessão 'fretes', ou um array vazio se não estiver definida
$Vendaa = isset($_SESSION['vendas']) ? $_SESSION['vendas'] : array();// Inicializa a variável 'Vendaa' com o valor da sessão 'vendas', ou um array vazio se não estiver definida

if (isset($_POST['prodSubmit']) && $_POST['prodSubmit'] == "carrinho") {
    // Verifica se o formulário foi enviado e se o valor de 'prodSubmit' é igual a "carrinho"
    $qtd = $_POST['qtd'];// Obtém o valor do campo 'qtd' do formulário
    $idProduto = $_POST['idItem'];// Obtém o valor do campo 'idItem' do formulário

    $_SESSION['itens'][$idProduto] = $qtd;// Armazena a quantidade do produto na sessão 'itens'
    $TaxaM[$idProduto]  = isset($_POST['taxa'])  ? $_POST['taxa']  : 0;     // Armazena o valor da taxa na variável 'TaxaM' ou 0 se não estiver definida no formulário
    $Fretee[$idProduto] = isset($_POST['Frete']) ? $_POST['Frete'] : 0; // Armazena o valor do frete na variável 'Fretee' ou 0 se não estiver definido no formulário
    $Vendaa[$idProduto] = isset($_POST['Venda']) ? $_POST['Venda'] : 0;// Armazena o valor da venda na variável 'Vendaa' ou 0 se não estiver definida no formulário

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
    $sumTaxaM = 0;// Variável para armazenar a soma das taxas
    $sumFretee = 0;// Variável para armazenar a soma dos fretes
    $sumVendaa = 0;// Variável para armazenar a soma das vendas    

    foreach ($_SESSION['itens'] as $produtos => $quantidade) {
        $NomeProduto = $vendas->itemNome($produtos);// Obtém o nome do produto usando o método 'itemNome' da classe Vendas
        $nomesProdutos[$produtos] = $NomeProduto; // Armazena o nome do produto no array 'nomesProdutos'

        if (!empty($NomeProduto)) { // Exibir o nome do produto apenas se ele existir
            echo '<tr>
			<td>' . $cont .       '</td>
			<td>' . $produtos .   '</td>
            <td>' . $NomeProduto .'</td>
			<td>' . $quantidade . '</td>
			<td>R$ ' . number_format($TaxaM[$produtos],  2, ',', '.') . '</td>
			<td>R$ ' . number_format($Fretee[$produtos], 2, ',', '.') . '</td>
            <td>R$ ' . number_format($Vendaa[$produtos], 2, ',', '.') . '</td>			

			<td>
			<input type="hidden" id="idItem" name="idItem[' . $produtos . ']" value="' . $produtos . '" />
			<input type="hidden" id="qtd"    name="qtd[' . $produtos . ']"    value="' . $quantidade . '" />						
			<a href="../../App/Database/remover.php?remover=carrinho&id=' . $produtos . '"><i class="fa fa-trash text-danger"></i></a></td>
			</tr>';

            $sumTaxaM  += $TaxaM[$produtos];    // Incrementa a taxa na soma das taxas
            $sumFretee += $Fretee[$produtos]; // Incrementa o frete na soma dos fretes
            $sumVendaa += $Vendaa[$produtos];// Incrementa a venda na soma das vendas            
            $cont = $cont + 1; // Incrementa o contador        
        }
    } 
    echo '<tr>
        <td colspan="4"></td>
        <td><b>R$ ' . number_format($sumTaxaM, 2, ',', '.') . '</b></td>
        <td><b>R$ ' . number_format($sumFretee, 2, ',', '.') . '</b></td>
        <td><b>R$ ' . number_format($sumVendaa, 2, ',', '.') . '</b></td>
        <td></td>
    </tr>';     
}

<?php
// Este é um script PHP que processa uma solicitação de envio de formulário para cadastrar ou atualizar informações de compra de produtos.
// Incluindo o arquivo de autenticação e o arquivo que contém a classe Compras
require_once '../auth.php';
require_once '../Models/compras.class.php';
require_once '../Models/produto.class.php'; // Necessário para buscar ID do produto e salvar imagem
// Verificando se a solicitação POST contém o parâmetro 'upload' e seu valor é 'Cadastrar'
if (isset($_POST['upload']) && $_POST['upload'] == 'Cadastrar') {
    // Recuperando os valores enviados pelo formulário via POST
    $skuAnuncio   = $_POST['skuAnuncio'];
    $modelo        = $_POST['modelo'];
    $NomeProduto  = $_POST['NomeProduto'];
    $ValorCompra  = $_POST['ValorCompra'];
    $DataCompra   = $_POST['DataCompra'];
    $QuantItens   = $_POST['QuantItens'];    
    $usuario_id   = $_SESSION['idUsuario'];
    
    // Criando um novo objeto da classe Compras
    $compras = new Compras;
    // Verificando se todas as informações necessárias foram preenchidas pelo usuário
    if ($skuAnuncio != null && $modelo != null && $NomeProduto != null && $ValorCompra != null && $DataCompra != null && $QuantItens != null)
     {
        // Verificando se o parâmetro 'IdCompra' foi definido. Se não, é uma nova compra e o método insertCompras é chamado.
        if (!isset($_POST['IdCompra'])) {
            $compras->insertCompras($skuAnuncio, $modelo, $NomeProduto, $ValorCompra, $DataCompra, $QuantItens, $usuario_id);
        } else { // Se o parâmetro 'IdCompra' foi definido, é uma atualização e o método UpdateCompras é chamado.
            $IdCompra = $_POST['IdCompra'];
            $compras->UpdateCompras($IdCompra, $skuAnuncio, $modelo, $NomeProduto, $ValorCompra, $DataCompra, $QuantItens, $usuario_id);
        }

        // --- Lógica de Upload de Imagens ---
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $produtoModel = new Produto();
            // Busca o idProduto baseado no SKU (skuAnuncio)
            $buscaProduto = $produtoModel->searchdata($skuAnuncio);
            
            $idProduto = null;
            if ($buscaProduto && isset($buscaProduto['data'][0]['idProduto'])) {
                $idProduto = $buscaProduto['data'][0]['idProduto'];
            }

            if ($idProduto) {
                $totalImagens = count($_FILES['imagens']['name']);
                for ($i = 0; $i < $totalImagens; $i++) {
                    if ($_FILES['imagens']['error'][$i] == 0) {
                        $extensao = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
                        $novoNome = md5(uniqid(rand(), true)) . '.' . $extensao;
                        $diretorio = '../../views/dist/img/produtos/';
                        
                        if (!is_dir($diretorio)) {
                            mkdir($diretorio, 0777, true);
                        }

                        if (move_uploaded_file($_FILES['imagens']['tmp_name'][$i], $diretorio . $novoNome)) {
                            $caminhoImagem = 'dist/img/produtos/' . $novoNome;
                            $produtoModel->insertImagem($idProduto, $caminhoImagem);
                        }
                    }
                }
            }
        }

        // Configurando a mensagem de alerta e redirecionando para a página de compras
        $_SESSION['msg'] = 'Produto cadastrado';
        header('Location: ../../views/compras/index.php');//header('Location: ../../views/compras/addcompra.php');
    } else { // Se alguma informação estiver faltando, redireciona de volta para a página de compras com um alerta de erro.
        header('Location: ../../views/compras/addcompra.php?alert=3');
    }
} else { // Se o parâmetro 'upload' não estiver definido como 'Cadastrar', redireciona de volta para a página de compras com um alerta de erro.
    header('Location: ../../views/compras/addcompra.php?alert=0');
}
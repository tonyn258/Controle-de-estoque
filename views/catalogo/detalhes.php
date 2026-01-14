<?php
require_once '../../App/Models/catalogo.class.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$catalogo = new Catalogo();

// Busca dados do produto
$produto = $catalogo->getProduto($id);

if (!$produto) {
    header("Location: index.php");
    exit;
}

// Busca imagens
$imagens = $catalogo->getImagens($id);

// Busca variações (produtos com mesmo SKU)
$variacoes = $catalogo->getVariacoes($produto['skuAnuncio']);

$pathImagens = '../../uploads/produtos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['NomeProduto']) ?> | Detalhes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <!-- Cabeçalho Simples -->
        <header class="page-header">
            <a href="index.php" class="btn-back">
                &larr; Voltar ao Catálogo
            </a>
        </header>

        <!-- Layout de Detalhes -->
        <div class="product-detail-layout">
            
            <!-- Coluna Esquerda: Galeria -->
            <div class="gallery-container">
                <div class="main-image">
                    <?php 
                        $imgPrincipal = !empty($imagens) ? $pathImagens . $imagens[0]['caminhoImagem'] : 'https://via.placeholder.com/600x600/f0f0f0/cccccc?text=Sem+Imagem';
                    ?>
                    <img id="mainImg" src="<?= $imgPrincipal ?>" alt="Imagem Principal">
                </div>
                
                <?php if (count($imagens) > 1): ?>
                <div class="thumbnails">
                    <?php foreach ($imagens as $k => $img): ?>
                        <img src="<?= $pathImagens . $img['caminhoImagem'] ?>" 
                             class="thumb <?= $k === 0 ? 'active' : '' ?>" 
                             alt="Thumb <?= $k ?>"
                             onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Coluna Direita: Informações -->
            <div class="info-container">
                <div class="product-info-header">
                    <div class="detail-brand"><?= htmlspecialchars($produto['model']) ?></div>
                    <h1 class="detail-title"><?= htmlspecialchars($produto['NomeProduto']) ?></h1>
                    <div class="detail-meta">
                        <span>SKU: <?= $produto['skuAnuncio'] ?></span> &bull; 
                        <span>Categoria ID: <?= $produto['idCategoria'] ?></span>
                    </div>
                </div>

                <div class="description-box">
                    <p><strong>Detalhes do Produto:</strong></p>
                    <p>Modelo: <?= htmlspecialchars($produto['model']) ?></p>
                    <p>Data de Cadastro: <?= date('d/m/Y', strtotime($produto['DataCompra'])) ?></p>
                </div>

                <!-- Tabela de Variações (Produtos com mesmo SKU) -->
                <div class="variations-section">
                    <h3>Variações Disponíveis</h3>
                    <table class="variations-table">
                        <thead>
                            <tr>
                                <th>Modelo/Variação</th>
                                <th>SKU</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variacoes as $var): ?>
                                <?php 
                                    $isCurrent = $var['IdCompra'] == $produto['IdCompra'];
                                    $stk = $var['estoque'];
                                    $statusClass = $stk > 5 ? 'stock-high' : ($stk > 0 ? 'stock-low' : 'stock-low');
                                    $statusText = $stk > 0 ? $stk . ' un.' : 'Esgotado';
                                    if($stk <= 0) $statusClass = 'stock-low'; // Vermelho para esgotado
                                ?>
                                <tr style="<?= $isCurrent ? 'background-color: #f0f7ff;' : '' ?>">
                                    <td>
                                        <a href="detalhes.php?id=<?= $var['IdCompra'] ?>" style="display:block; color: inherit;">
                                            <?= htmlspecialchars($var['model']) ?>
                                            <?= $isCurrent ? ' <strong>(Atual)</strong>' : '' ?>
                                        </a>
                                    </td>
                                    <td><?= $var['skuAnuncio'] ?></td>
                                    <td class="price-highlight">R$ <?= number_format($var['ValorVenda'], 2, ',', '.') ?></td>
                                    <td><span class="stock-status <?= $statusClass ?>"><?= $statusText ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        function changeImage(element) {
            document.getElementById('mainImg').src = element.src;
            document.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }
    </script>
</body>
</html>
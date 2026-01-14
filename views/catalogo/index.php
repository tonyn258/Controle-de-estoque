<?php
require_once '../../App/Models/catalogo.class.php';

// Inicializa o Model
$catalogo = new Catalogo();

// Captura filtro da URL
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';

// Busca produtos
$produtos = $catalogo->getProdutos($filtro);

// Caminho base para imagens (ajuste conforme sua estrutura de pastas real)
$pathImagens = '../../uploads/produtos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos | E-commerce</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <!-- Cabeçalho -->
        <header class="page-header">
            <h1 class="page-title">Catálogo de Produtos</h1>
            <a href="../compras/index.php" class="btn-back">
                &larr; Voltar
            </a>
        </header>

        <!-- Filtros -->
        <div class="filters">
            <a href="?filtro=todos" class="filter-pill <?= $filtro == 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="?filtro=com_estoque" class="filter-pill <?= $filtro == 'com_estoque' ? 'active' : '' ?>">Com estoque</a>
            <a href="?filtro=sem_estoque" class="filter-pill <?= $filtro == 'sem_estoque' ? 'active' : '' ?>">Sem estoque</a>
        </div>

        <!-- Grid de Produtos -->
        <div class="product-grid">
            
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $prod): ?>
                    <?php 
                        $temEstoque = $prod['estoque'] > 0;
                        // Verifica se existe imagem, senão usa placeholder
                        $imgSrc = !empty($prod['imagem']) ? $pathImagens . $prod['imagem'] : 'https://via.placeholder.com/300x300/e9ecef/adb5bd?text=Sem+Imagem';
                    ?>
                    
                    <a href="detalhes.php?id=<?= $prod['IdCompra'] ?>" class="product-card">
                        <div class="card-image">
                            <?php if (!$temEstoque): ?>
                                <span class="card-badge out-of-stock">Esgotado</span>
                            <?php endif; ?>
                            
                            <img src="<?= $imgSrc ?>" 
                                 alt="<?= htmlspecialchars($prod['NomeProduto']) ?>" 
                                 style="<?= !$temEstoque ? 'opacity: 0.6;' : '' ?>">
                        </div>
                        
                        <div class="card-content">
                            <div class="product-sku">SKU: <?= $prod['skuAnuncio'] ?></div>
                            <!-- Marca não está na tabela compras explicitamente, usando model ou categoria se necessário -->
                            <div class="product-brand"><?= htmlspecialchars($prod['model']) ?></div>
                            
                            <h3 class="product-title"><?= htmlspecialchars($prod['NomeProduto']) ?></h3>
                            
                            <div class="product-price" style="<?= !$temEstoque ? 'color: var(--text-light);' : '' ?>">
                                <?php if ($temEstoque): ?>
                                    <small>Por</small> R$ <?= number_format($prod['ValorVenda'], 2, ',', '.') ?>
                                <?php else: ?>
                                    Indisponível
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>

                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; color: #666;">Nenhum produto encontrado.</p>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
<?php
// Configurações do Banco de Dados
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente da classe Connect do sistema
if (file_exists('../../App/Models/connect.php')) {
    require_once '../../App/Models/connect.php';
    if (class_exists('Connect')) {
        $legacy = new Connect();
        if (isset($legacy->SQL) && $legacy->SQL instanceof mysqli) {
            $res = $legacy->SQL->query("SELECT DATABASE()");
            if ($res && $row = $res->fetch_row()) {
                $db_name = $row[0];
            }
        }
    }
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Recupera o ID via GET
$idAnuncio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = null;
$imagens = [];
$erro = null;

if ($idAnuncio) {
    try {
        // 1. Buscar detalhes do anúncio
        // Calcula o estoque (QuantItens - QuantItensVend)
        // Faz JOIN com categoria_produto para pegar o nome da categoria
        $sql = "SELECT 
                    a.*, 
                    (a.QuantItens - COALESCE(a.QuantItensVend, 0)) AS estoqueCalculado,
                    c.NomeCategoria
                FROM anuncio a
                LEFT JOIN categoria_produto c ON a.idCategoria = c.idCategoria
                WHERE a.idAnuncio = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $idAnuncio, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch();

        if ($produto) {
            // 2. Buscar imagens do anúncio
            $sqlImg = "SELECT caminhoImagem FROM anuncio_imagem WHERE anuncio_id = :id ORDER BY ordem ASC";
            $stmtImg = $pdo->prepare($sqlImg);
            $stmtImg->bindValue(':id', $idAnuncio, PDO::PARAM_INT);
            $stmtImg->execute();
            $imagens = $stmtImg->fetchAll();
        } else {
            $erro = "Produto não encontrado ou indisponível.";
        }

    } catch (PDOException $e) {
        $erro = "Erro ao processar a solicitação: " . $e->getMessage();
    }
} else {
    $erro = "Identificador do produto inválido.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $produto ? htmlspecialchars($produto['NomeProduto']) : 'Detalhes do Produto' ?></title>
    <style>
        /* Reset e Variáveis */
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --dark: #343a40;
            --light: #f8f9fa;
            --success: #28a745;
            --danger: #dc3545;
            --radius: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; line-height: 1.6; }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        /* Botão Voltar */
        .btn-back { display: inline-block; margin-bottom: 20px; color: var(--secondary); text-decoration: none; font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        /* Layout Principal */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        /* Galeria de Imagens */
        .gallery { display: flex; flex-direction: column; gap: 15px; }
        
        .main-image-container {
            width: 100%;
            height: 400px;
            border: 1px solid #eee;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #fff;
        }
        .main-image { max-width: 100%; max-height: 100%; object-fit: contain; transition: opacity 0.3s; }
        
        .thumbnails { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
        .thumb {
            width: 80px;
            height: 80px;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            opacity: 0.6;
            transition: all 0.2s;
        }
        .thumb:hover, .thumb.active { border-color: var(--primary); opacity: 1; }

        /* Informações do Produto */
        .info { display: flex; flex-direction: column; }
        
        .product-title { font-size: 2rem; color: var(--dark); margin-bottom: 10px; line-height: 1.2; }
        
        .meta-data { color: #777; font-size: 0.9rem; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; }
        .meta-item strong { color: #555; }

        .price-box { margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .price { font-size: 2.5rem; font-weight: bold; color: var(--primary); display: block; }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 10px;
        }
        .status-available { background-color: #d4edda; color: #155724; }
        .status-soldout { background-color: #f8d7da; color: #721c24; }

        .description { margin-bottom: 30px; }
        .description h3 { font-size: 1.2rem; margin-bottom: 10px; color: var(--dark); }
        .description p { color: #555; white-space: pre-line; }

        /* Mensagens de Erro */
        .alert-box {
            padding: 20px;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            border-radius: var(--radius);
            text-align: center;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .product-detail { grid-template-columns: 1fr; }
            .main-image-container { height: 300px; }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn-back">&larr; Voltar ao Catálogo</a>
    <a href="../index.php" class="btn-back" style="margin-left: 20px;">🏠 Voltar ao Início</a>

    <?php if ($erro): ?>
        <div class="alert-box">
            <h3>Ops!</h3>
            <p><?= htmlspecialchars($erro) ?></p>
            <br>
            <a href="index.php" style="color: inherit; font-weight: bold;">Ir para a lista de produtos</a>
        </div>
    <?php elseif ($produto): ?>
        <?php 
            // Lógica de exibição
            $estoque = $produto['estoqueCalculado'];
            $disponivel = $estoque > 0;
            $statusTexto = $disponivel ? "Disponível ($estoque em estoque)" : "Esgotado";
            $statusClasse = $disponivel ? "status-available" : "status-soldout";
            
            // Imagem Principal Inicial
            $imgPrincipal = !empty($imagens) ? '../../' . $imagens[0]['caminhoImagem'] : null;
        ?>

        <div class="product-detail">
            <!-- Coluna Esquerda: Galeria -->
            <div class="gallery">
                <div class="main-image-container">
                    <?php if ($imgPrincipal): ?>
                        <img src="<?= htmlspecialchars($imgPrincipal) ?>" alt="Imagem Principal" class="main-image" id="mainImage">
                    <?php else: ?>
                        <span style="color: #999;">Sem imagem</span>
                    <?php endif; ?>
                </div>

                <?php if (count($imagens) > 1): ?>
                    <div class="thumbnails">
                        <?php foreach ($imagens as $idx => $img): ?>
                            <?php $thumbUrl = '../../' . $img['caminhoImagem']; ?>
                            <img src="<?= htmlspecialchars($thumbUrl) ?>" 
                                 class="thumb <?= $idx === 0 ? 'active' : '' ?>" 
                                 onclick="changeImage(this, '<?= htmlspecialchars($thumbUrl) ?>')"
                                 alt="Miniatura">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Coluna Direita: Informações -->
            <div class="info">
                <h1 class="product-title"><?= htmlspecialchars($produto['NomeProduto']) ?></h1>
                
                <div class="meta-data">
                    <div class="meta-item">
                        <strong>SKU:</strong> 
                        <a href="edit.php?id=<?= $produto['idAnuncio'] ?>" style="color: inherit; text-decoration: none;" title="Editar Anúncio">
                            <?= htmlspecialchars($produto['skuAnuncio']) ?>
                        </a>
                    </div>
                    <div class="meta-item"><strong>Modelo:</strong> <?= htmlspecialchars($produto['model']) ?></div>
                    <?php if (!empty($produto['NomeCategoria'])): ?>
                        <div class="meta-item"><strong>Categoria:</strong> <?= htmlspecialchars($produto['NomeCategoria']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="price-box">
                    <span class="price">R$ <?= number_format($produto['ValorVenda'], 2, ',', '.') ?></span>
                    <span class="status-badge <?= $statusClasse ?>"><?= $statusTexto ?></span>
                </div>

                <div class="description">
                    <h3>Descrição</h3>
                    <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Script simples para troca de imagem na galeria
    function changeImage(element, src) {
        // Troca o src da imagem principal
        const mainImg = document.getElementById('mainImage');
        if (mainImg) {
            mainImg.style.opacity = 0;
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = 1;
            }, 200);
        }

        // Atualiza classe active nas miniaturas
        document.querySelectorAll('.thumb').forEach(thumb => thumb.classList.remove('active'));
        element.classList.add('active');
    }
</script>

</body>
</html>
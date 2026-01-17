<?php
// 1. Configuração e Autenticação
require_once '../../App/auth.php';

// Configurações do Banco de Dados
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente
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

// Variáveis de controle
$erro = '';
$sucesso = '';
$categorias = [];
$produto = [];
$imagensExistentes = [];

// Recuperar ID do Anúncio
$idAnuncio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idAnuncio) {
    die("ID do anúncio inválido.");
}

// 2. Buscar Dados Iniciais (Categorias e Produto)
try {
    // Categorias
    $stmtCat = $pdo->query("SELECT idCategoria, NomeCategoria FROM categoria_produto WHERE statusCategoria = 1 ORDER BY NomeCategoria ASC");
    $categorias = $stmtCat->fetchAll();

    // Produto
    $stmtProd = $pdo->prepare("SELECT * FROM anuncio WHERE idAnuncio = :id");
    $stmtProd->execute([':id' => $idAnuncio]);
    $produto = $stmtProd->fetch();

    if (!$produto) {
        die("Anúncio não encontrado.");
    }

    // Imagens
    $stmtImg = $pdo->prepare("SELECT * FROM anuncio_imagem WHERE anuncio_id = :id ORDER BY ordem ASC");
    $stmtImg->execute([':id' => $idAnuncio]);
    $imagensExistentes = $stmtImg->fetchAll();

} catch (PDOException $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

// 3. Processar Formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização e Validação
    $sku = filter_input(INPUT_POST, 'skuAnuncio', FILTER_SANITIZE_SPECIAL_CHARS);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_SPECIAL_CHARS);
    $nome = filter_input(INPUT_POST, 'NomeProduto', FILTER_SANITIZE_SPECIAL_CHARS);
    $idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);
    
    $valorCompra = str_replace(',', '.', str_replace('.', '', $_POST['ValorCompra'] ?? '0'));
    $valorVenda = str_replace(',', '.', str_replace('.', '', $_POST['ValorVenda'] ?? '0'));
    
    $qtd = filter_input(INPUT_POST, 'QuantItens', FILTER_VALIDATE_INT);
    $qtdVend = filter_input(INPUT_POST, 'QuantItensVend', FILTER_VALIDATE_INT);
    $descricao = $_POST['descricao'] ?? '';
    $ativo = isset($_POST['Ativo']) ? 1 : 0;
    $public = isset($_POST['public']) ? 1 : 0;
    $usuario_id = $_SESSION['idUsuario'] ?? 0;

    // Validações
    if (!$sku || !$nome || !$idCategoria) {
        $erro = "Preencha os campos obrigatórios.";
    } elseif ($qtdVend > $qtd) {
        $erro = "A quantidade vendida não pode ser maior que a quantidade total.";
    } else {
        try {
            $pdo->beginTransaction();

            // Atualizar Anúncio
            $sqlUpd = "UPDATE anuncio SET 
                skuAnuncio = :sku, model = :model, NomeProduto = :nome, idCategoria = :cat, 
                ValorCompra = :vcompra, ValorVenda = :vvenda, QuantItens = :qtd, 
                QuantItensVend = :qtdVend, descricao = :desc, Ativo = :ativo, public = :public, 
                usuario_id = :uid 
                WHERE idAnuncio = :id";

            $stmt = $pdo->prepare($sqlUpd);
            $stmt->execute([
                ':sku' => $sku, ':model' => $model, ':nome' => $nome, ':cat' => $idCategoria,
                ':vcompra' => $valorCompra, ':vvenda' => $valorVenda, ':qtd' => $qtd,
                ':qtdVend' => $qtdVend, ':desc' => $descricao, ':ativo' => $ativo,
                ':public' => $public, ':uid' => $usuario_id, ':id' => $idAnuncio
            ]);

            // Atualizar Ordem das Imagens Existentes
            if (isset($_POST['ordem_existente']) && is_array($_POST['ordem_existente'])) {
                $sqlOrder = "UPDATE anuncio_imagem SET ordem = :ordem WHERE idImagem = :id AND anuncio_id = :aid";
                $stmtOrder = $pdo->prepare($sqlOrder);
                foreach ($_POST['ordem_existente'] as $idImg => $novaOrdem) {
                    $stmtOrder->execute([
                        ':ordem' => intval($novaOrdem),
                        ':id' => intval($idImg),
                        ':aid' => $idAnuncio
                    ]);
                }
            }

            // Excluir Imagens Selecionadas
            if (isset($_POST['delete_img']) && is_array($_POST['delete_img'])) {
                $idsParaDeletar = $_POST['delete_img'];
                foreach ($idsParaDeletar as $idImg) {
                    // Buscar caminho para deletar arquivo físico
                    $stmtGetPath = $pdo->prepare("SELECT caminhoImagem FROM anuncio_imagem WHERE idImagem = :id AND anuncio_id = :aid");
                    $stmtGetPath->execute([':id' => $idImg, ':aid' => $idAnuncio]);
                    $imgData = $stmtGetPath->fetch();

                    if ($imgData) {
                        // Caminho no banco: uploads/catalogo/ID/arquivo.jpg
                        // Caminho físico relativo a este arquivo: ../../uploads/catalogo/ID/arquivo.jpg
                        $physicalPath = '../../' . $imgData['caminhoImagem'];
                        if (file_exists($physicalPath)) {
                            unlink($physicalPath);
                        }
                        
                        // Deletar do banco
                        $stmtDel = $pdo->prepare("DELETE FROM anuncio_imagem WHERE idImagem = :id");
                        $stmtDel->execute([':id' => $idImg]);
                    }
                }
            }

            // Upload de Novas Imagens
            if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
                // Recalcular quantas imagens existem agora
                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM anuncio_imagem WHERE anuncio_id = :id");
                $stmtCount->execute([':id' => $idAnuncio]);
                $currentCount = $stmtCount->fetchColumn();
                
                // Buscar maior ordem para continuar a sequência corretamente
                $stmtMax = $pdo->prepare("SELECT MAX(ordem) FROM anuncio_imagem WHERE anuncio_id = :id");
                $stmtMax->execute([':id' => $idAnuncio]);
                $maxOrder = $stmtMax->fetchColumn();

                $files = $_FILES['imagens'];
                $newCount = count($files['name']);
                
                if (($currentCount + $newCount) > 10) {
                    throw new Exception("Limite de 10 imagens excedido. Você tem $currentCount e tentou enviar mais $newCount.");
                }

                $baseUploadPath = '../../uploads/catalogo/';
                $targetDir = $baseUploadPath . $idAnuncio . '/';

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $sqlImg = "INSERT INTO anuncio_imagem (anuncio_id, caminhoImagem, ordem, dataCadastro) VALUES (:aid, :path, :ordem, NOW())";
                $stmtImgIns = $pdo->prepare($sqlImg);
                $nextOrder = ($maxOrder !== false) ? $maxOrder + 1 : 1;

                for ($i = 0; $i < $newCount; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $files['tmp_name'][$i];
                        $fileName = basename($files['name'][$i]);
                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $newFileName = uniqid('img_') . '.' . $ext;
                        $destination = $targetDir . $newFileName;
                        $dbPath = 'uploads/catalogo/' . $idAnuncio . '/' . $newFileName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            $stmtImgIns->execute([
                                ':aid' => $idAnuncio,
                                ':path' => $dbPath,
                                ':ordem' => $nextOrder++
                            ]);
                        }
                    }
                }
            }

            $pdo->commit();
            $sucesso = "Anúncio atualizado com sucesso!";
            
            // Recarregar dados
            $stmtProd->execute([':id' => $idAnuncio]);
            $produto = $stmtProd->fetch();
            $stmtImg->execute([':id' => $idAnuncio]);
            $imagensExistentes = $stmtImg->fetchAll();

        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anúncio #<?= $idAnuncio ?></title>
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --danger: #dc3545;
            --radius: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; color: #333; }
        
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .btn-back { text-decoration: none; color: var(--secondary); }
        
        .alert { padding: 15px; border-radius: var(--radius); margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        input[type="text"], input[type="number"], select, textarea {
            padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem;
        }
        
        .checkbox-group { display: flex; gap: 20px; margin-top: 10px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; cursor: pointer; }

        /* Galeria de Edição */
        .existing-images { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; }
        .img-wrapper { position: relative; width: 120px; height: 170px; border: 1px solid #ddd; border-radius: 4px; display: flex; flex-direction: column; background: #fff; }
        .img-wrapper img { width: 100%; height: 100px; object-fit: cover; border-bottom: 1px solid #eee; }
        .img-actions { padding: 8px; display: flex; flex-direction: column; gap: 5px; align-items: center; justify-content: center; flex-grow: 1; }
        .btn-delete-img {
            position: absolute; top: -5px; right: -5px; background: var(--danger); color: white;
            border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .img-wrapper.deleted { opacity: 0.4; border: 2px solid var(--danger); }
        
        .image-upload-area {
            border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: var(--radius); cursor: pointer;
        }
        .image-upload-area:hover { background-color: #f9f9f9; border-color: var(--primary); }

        .btn-submit {
            grid-column: 1 / -1; background-color: var(--primary); color: white; border: none; padding: 15px;
            font-size: 1.1rem; font-weight: bold; border-radius: var(--radius); cursor: pointer; margin-top: 10px;
        }
        .btn-submit:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Editar Anúncio</h1>
            <a href="index.php?view=list" class="btn-back">&larr; Voltar à Lista</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Dados Básicos -->
            <div class="form-group">
                <label for="skuAnuncio">SKU</label>
                <input type="text" name="skuAnuncio" id="skuAnuncio" required value="<?= htmlspecialchars($produto['skuAnuncio']) ?>">
            </div>

            <div class="form-group">
                <label for="model">Modelo</label>
                <input type="text" name="model" id="model" value="<?= htmlspecialchars($produto['model']) ?>">
            </div>

            <div class="form-group full">
                <label for="NomeProduto">Nome do Produto</label>
                <input type="text" name="NomeProduto" id="NomeProduto" required value="<?= htmlspecialchars($produto['NomeProduto']) ?>">
            </div>

            <div class="form-group">
                <label for="idCategoria">Categoria</label>
                <select name="idCategoria" id="idCategoria" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['idCategoria'] ?>" <?= $produto['idCategoria'] == $cat['idCategoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['NomeCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estoque -->
            <div class="form-group">
                <label for="QuantItens">Qtd. Total</label>
                <input type="number" name="QuantItens" id="QuantItens" min="1" required value="<?= htmlspecialchars($produto['QuantItens']) ?>">
            </div>

            <div class="form-group">
                <label for="QuantItensVend">Qtd. Vendida</label>
                <input type="number" name="QuantItensVend" id="QuantItensVend" min="0" required value="<?= htmlspecialchars($produto['QuantItensVend'] ?? 0) ?>">
            </div>

            <!-- Valores -->
            <div class="form-group">
                <label for="ValorCompra">Valor Compra (R$)</label>
                <input type="text" name="ValorCompra" class="money" value="<?= number_format($produto['ValorCompra'], 2, ',', '.') ?>">
            </div>

            <div class="form-group">
                <label for="ValorVenda">Valor Venda (R$)</label>
                <input type="text" name="ValorVenda" class="money" value="<?= number_format($produto['ValorVenda'], 2, ',', '.') ?>">
            </div>

            <!-- Descrição -->
            <div class="form-group full">
                <label for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" rows="4"><?= htmlspecialchars($produto['descricao']) ?></textarea>
            </div>

            <!-- Status -->
            <div class="form-group full checkbox-group">
                <label class="checkbox-item">
                    <input type="checkbox" name="Ativo" <?= $produto['Ativo'] ? 'checked' : '' ?>> Ativo
                </label>
                <label class="checkbox-item">
                    <input type="checkbox" name="public" <?= $produto['public'] ? 'checked' : '' ?>> Público
                </label>
            </div>

            <!-- Gerenciamento de Imagens -->
            <div class="form-group full">
                <label>Imagens Atuais (Marque o X para excluir ao salvar)</label>
                <div class="existing-images">
                    <?php if (empty($imagensExistentes)): ?>
                        <p style="color: #777; font-style: italic;">Nenhuma imagem cadastrada.</p>
                    <?php else: ?>
                        <?php foreach ($imagensExistentes as $img): ?>
                            <?php $idImg = $img['idImagem'] ?? $img['id']; // Ajuste para pegar o ID correto ?>
                            <div class="img-wrapper" id="wrapper-<?= $idImg ?>">
                                <img src="../../<?= htmlspecialchars($img['caminhoImagem']) ?>" alt="Img">
                                <div class="img-actions">
                                    <label style="font-size: 0.8rem; margin:0; color: #555;">Ordem (1=Capa)</label>
                                    <input type="number" name="ordem_existente[<?= $idImg ?>]" value="<?= $img['ordem'] ?>" min="1" style="width: 60px; text-align: center; padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                                </div>
                                <button type="button" class="btn-delete-img" onclick="markForDeletion(<?= $idImg ?>)">X</button>
                                <input type="checkbox" name="delete_img[]" value="<?= $idImg ?>" id="del-<?= $idImg ?>" style="display:none;">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <label>Adicionar Novas Imagens</label>
                <div class="image-upload-area" onclick="document.getElementById('imagens').click()">
                    <p>Clique para adicionar mais imagens</p>
                    <input type="file" name="imagens[]" id="imagens" multiple accept="image/*" style="display: none;" onchange="previewImages(this)">
                </div>
                <div class="existing-images" id="preview" style="margin-top: 15px;"></div>
            </div>

            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>
    </div>
</div>

<script>
    // Marcar imagem para exclusão visualmente
    function markForDeletion(id) {
        const wrapper = document.getElementById('wrapper-' + id);
        const checkbox = document.getElementById('del-' + id);
        
        if (checkbox.checked) {
            // Desmarcar
            checkbox.checked = false;
            wrapper.classList.remove('deleted');
        } else {
            // Marcar
            checkbox.checked = true;
            wrapper.classList.add('deleted');
        }
    }

    // Preview de novas imagens
    function previewImages(input) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'img-wrapper';
                    div.innerHTML = `<img src="${e.target.result}" alt="New">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Máscara Moeda
    document.querySelectorAll('.money').forEach(input => {
        input.addEventListener('keyup', function(e) {
            let v = e.target.value.replace(/\D/g, "");
            v = (v / 100).toFixed(2) + "";
            v = v.replace(".", ",");
            v = v.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            v = v.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = v;
        });
    });
</script>

</body>
</html>
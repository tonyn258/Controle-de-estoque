<?php
// 1. Configuração e Autenticação
require_once '../../App/auth.php';

// Configurações do Banco de Dados
$db_host = 'localhost';
$db_name = 'nome_do_banco'; 
$db_user = 'root';
$db_pass = '';

// Tenta recuperar o nome do banco automaticamente (compatibilidade com sistema legado)
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

// 2. Buscar Categorias para o Select
try {
    // Ajuste na query conforme solicitado, garantindo compatibilidade de case
    $stmtCat = $pdo->query("SELECT idCategoria, NomeCategoria FROM categoria_produto WHERE statusCategoria = 1 ORDER BY NomeCategoria ASC");
    $categorias = $stmtCat->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
}

// 3. Processar Formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização e Validação
    $sku = filter_input(INPUT_POST, 'skuAnuncio', FILTER_SANITIZE_SPECIAL_CHARS);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_SPECIAL_CHARS);
    $nome = filter_input(INPUT_POST, 'NomeProduto', FILTER_SANITIZE_SPECIAL_CHARS);
    $idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);
    
    // Conversão de moeda (R$ 1.000,00 -> 1000.00)
    $valorCompra = str_replace(',', '.', str_replace('.', '', $_POST['ValorCompra'] ?? '0'));
    $valorVenda = str_replace(',', '.', str_replace('.', '', $_POST['ValorVenda'] ?? '0'));
    
    $qtd = filter_input(INPUT_POST, 'QuantItens', FILTER_VALIDATE_INT);
    $dataCompra = $_POST['DataCompra'] ?? date('Y-m-d');
    $descricao = $_POST['descricao'] ?? '';
    $ativo = isset($_POST['Ativo']) ? 1 : 0;
    $public = isset($_POST['public']) ? 1 : 0;
    $usuario_id = $_SESSION['idUsuario'] ?? 0;

    // Validações de Regra de Negócio
    if (!$sku || !$nome || !$idCategoria) {
        $erro = "Preencha os campos obrigatórios (SKU, Nome, Categoria).";
    } elseif ($qtd < 1) {
        $erro = "A quantidade inicial deve ser pelo menos 1.";
    } else {
        try {
            // Início da Transação
            $pdo->beginTransaction();

            // Inserir Anúncio
            $sqlAnuncio = "INSERT INTO anuncio (
                skuAnuncio, model, NomeProduto, idCategoria, ValorCompra, ValorVenda, 
                QuantItens, QuantItensVend, descricao, Ativo, public, usuario_id, DataCompra
            ) VALUES (
                :sku, :model, :nome, :cat, :vcompra, :vvenda, 
                :qtd, 0, :desc, :ativo, :public, :uid, :datacompra
            )";

            $stmt = $pdo->prepare($sqlAnuncio);
            $stmt->execute([
                ':sku' => $sku,
                ':model' => $model,
                ':nome' => $nome,
                ':cat' => $idCategoria,
                ':vcompra' => $valorCompra,
                ':vvenda' => $valorVenda,
                ':qtd' => $qtd,
                ':desc' => $descricao,
                ':ativo' => $ativo,
                ':public' => $public,
                ':uid' => $usuario_id,
                ':datacompra' => $dataCompra
            ]);

            $idAnuncio = $pdo->lastInsertId();

            // Processar Upload de Imagens
            if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
                // Caminho relativo para salvar no banco e físico
                // Estrutura: www/projetos/website/uploads/catalogo/{id}/
                $baseUploadPath = '../../uploads/catalogo/';
                $targetDir = $baseUploadPath . $idAnuncio . '/';

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $files = $_FILES['imagens'];
                $count = count($files['name']);
                $maxFiles = 10;
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if ($count > $maxFiles) {
                    throw new Exception("O limite é de 10 imagens.");
                }

                $sqlImg = "INSERT INTO anuncio_imagem (anuncio_id, caminhoImagem, ordem, dataCadastro) VALUES (:aid, :path, :ordem, NOW())";
                $stmtImg = $pdo->prepare($sqlImg);

                for ($i = 0; $i < $count; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $files['tmp_name'][$i];
                        $fileName = basename($files['name'][$i]);
                        $fileType = $files['type'][$i];
                        $fileSize = $files['size'][$i];

                        // Validações de Arquivo
                        if (!in_array($fileType, $allowedTypes)) {
                            throw new Exception("Tipo de arquivo não permitido: $fileName");
                        }
                        if ($fileSize > 2 * 1024 * 1024) { // 2MB
                            throw new Exception("Arquivo muito grande (Max 2MB): $fileName");
                        }

                        // Gerar nome único
                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $newFileName = uniqid('img_') . '.' . $ext;
                        $destination = $targetDir . $newFileName;
                        
                        // Caminho para salvar no banco (relativo à raiz do site ou views, conforme padrão do sistema)
                        // Salvando: uploads/catalogo/{id}/{arquivo}
                        $dbPath = 'uploads/catalogo/' . $idAnuncio . '/' . $newFileName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            $stmtImg->execute([
                                ':aid' => $idAnuncio,
                                ':path' => $dbPath,
                                ':ordem' => $i + 1
                            ]);
                        } else {
                            throw new Exception("Falha ao salvar o arquivo: $fileName");
                        }
                    }
                }
            }

            $pdo->commit();
            $sucesso = "Anúncio cadastrado com sucesso! ID: #$idAnuncio";
            // Limpar POST
            $_POST = [];

        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao cadastrar: " . $e->getMessage();
            // Nota: Em um ambiente de produção ideal, deveríamos remover a pasta criada se o rollback ocorrer.
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Anúncio - Catálogo</title>
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --radius: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; }
        
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 30px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { font-size: 1.8rem; color: var(--dark); }
        .btn-back { text-decoration: none; color: var(--secondary); font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        .alert { padding: 15px; border-radius: var(--radius); margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        input[type="text"], input[type="number"], select, textarea {
            padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; }
        
        .checkbox-group { display: flex; gap: 20px; align-items: center; margin-top: 10px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        
        .image-upload-area {
            border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: var(--radius); cursor: pointer; transition: background 0.2s;
        }
        .image-upload-area:hover { background-color: #f9f9f9; border-color: var(--primary); }
        
        .preview-container { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px; }
        .preview-box { position: relative; width: 100px; height: 100px; border-radius: 4px; overflow: hidden; border: 1px solid #ddd; }
        .preview-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-submit {
            grid-column: 1 / -1; background-color: var(--primary); color: white; border: none; padding: 15px;
            font-size: 1.1rem; font-weight: bold; border-radius: var(--radius); cursor: pointer; margin-top: 10px;
        }
        .btn-submit:hover { background-color: #0056b3; }

        @media (max-width: 768px) {
            form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Novo Anúncio</h1>
            <a href="index.php" class="btn-back">&larr; Voltar ao Catálogo</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Dados do Produto -->
            <div class="form-group">
                <label for="skuAnuncio">SKU *</label>
                <input type="text" name="skuAnuncio" id="skuAnuncio" required value="<?= htmlspecialchars($_POST['skuAnuncio'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="model">Modelo</label>
                <input type="text" name="model" id="model" value="<?= htmlspecialchars($_POST['model'] ?? '') ?>">
            </div>

            <div class="form-group full">
                <label for="NomeProduto">Nome do Produto *</label>
                <input type="text" name="NomeProduto" id="NomeProduto" required value="<?= htmlspecialchars($_POST['NomeProduto'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="idCategoria">Categoria *</label>
                <select name="idCategoria" id="idCategoria" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['idCategoria'] ?>" <?= (isset($_POST['idCategoria']) && $_POST['idCategoria'] == $cat['idCategoria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['NomeCategoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="QuantItens">Quantidade em Estoque *</label>
                <input type="number" name="QuantItens" id="QuantItens" min="1" required value="<?= htmlspecialchars($_POST['QuantItens'] ?? '1') ?>">
            </div>

            <!-- Valores -->
            <div class="form-group">
                <label for="ValorCompra">Valor de Compra (R$)</label>
                <input type="text" name="ValorCompra" id="ValorCompra" class="money" placeholder="0,00" value="<?= htmlspecialchars($_POST['ValorCompra'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="ValorVenda">Valor de Venda (R$)</label>
                <input type="text" name="ValorVenda" id="ValorVenda" class="money" placeholder="0,00" value="<?= htmlspecialchars($_POST['ValorVenda'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="DataCompra">Data da Compra</label>
                <input type="date" name="DataCompra" id="DataCompra" value="<?= htmlspecialchars($_POST['DataCompra'] ?? date('Y-m-d')) ?>">
            </div>

            <!-- Descrição -->
            <div class="form-group full">
                <label for="descricao">Descrição Detalhada</label>
                <textarea name="descricao" id="descricao" rows="4"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
            </div>

            <!-- Opções -->
            <div class="form-group full checkbox-group">
                <label class="checkbox-item">
                    <input type="checkbox" name="Ativo" checked> Ativo
                </label>
                <label class="checkbox-item">
                    <input type="checkbox" name="public" checked> Público (Visível no site)
                </label>
            </div>

            <!-- Upload de Imagens -->
            <div class="form-group full">
                <label>Imagens do Produto (Máx 10)</label>
                <div class="image-upload-area" onclick="document.getElementById('imagens').click()">
                    <p>Clique para selecionar ou arraste as imagens aqui</p>
                    <input type="file" name="imagens[]" id="imagens" multiple accept="image/png, image/jpeg, image/webp" style="display: none;" onchange="previewImages(this)">
                </div>
                <div class="preview-container" id="preview"></div>
            </div>

            <button type="submit" class="btn-submit">Cadastrar Anúncio</button>
        </form>
    </div>
</div>

<script>
    // Preview de Imagens
    function previewImages(input) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        
        if (input.files) {
            if (input.files.length > 10) {
                alert("Máximo de 10 imagens permitidas.");
                input.value = '';
                return;
            }

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-box';
                    div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Máscara simples para moeda
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
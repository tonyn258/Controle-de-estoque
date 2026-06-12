<?php

class UsuarioModel {
    private $pdo;
    private $uploadDir = '../../uploads/usuarios/';

    public function __construct() {
        // Database configuration (Adjust if necessary)
        $host = 'localhost';
        $dbname = 'controlestoque'; // Assumed based on context
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM usuario ORDER BY idUser DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE idUser = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data, $file) {
        $filename = $this->handleUpload($file);
        
        // Hash password
        $passwordHash = password_hash($data['Password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (Username, Email, Password, Permissão, arquivo, DataRegistro) 
                VALUES (:user, :email, :pass, :perm, :img, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user' => $data['Username'],
            ':email' => $data['Email'],
            ':pass' => $passwordHash,
            ':perm' => $data['Permissão'],
            ':img' => $filename
        ]);
    }

    public function update($id, $data, $file) {
        $currentUser = $this->getById($id);
        if (!$currentUser) return false;

        // Handle Image
        $filename = $currentUser['arquivo'];
        if (isset($file['name']) && !empty($file['name'])) {
            // Delete old image if exists and is not default
            if ($filename && file_exists($this->uploadDir . $filename)) {
                unlink($this->uploadDir . $filename);
            }
            $filename = $this->handleUpload($file);
        }

        // Handle Password
        $passwordSql = "";
        $params = [
            ':user' => $data['Username'],
            ':email' => $data['Email'],
            ':perm' => $data['Permissão'],
            ':img' => $filename,
            ':id' => $id
        ];

        if (!empty($data['Password'])) {
            $passwordSql = ", Password = :pass";
            $params[':pass'] = password_hash($data['Password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE usuario SET Username = :user, Email = :email, Permissão = :perm, arquivo = :img $passwordSql WHERE idUser = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $user = $this->getById($id);
        if ($user) {
            // Delete image
            if ($user['arquivo'] && file_exists($this->uploadDir . $user['arquivo'])) {
                unlink($this->uploadDir . $user['arquivo']);
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE idUser = :id");
            return $stmt->execute([':id' => $id]);
        }
        return false;
    }

    private function handleUpload($file) {
        if (!isset($file['name']) || empty($file['name'])) {
            return null;
        }

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Tipo de arquivo inválido. Apenas JPG, PNG e WEBP.");
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            throw new Exception("Arquivo muito grande. Máximo 5MB.");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('user_') . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $this->uploadDir . $newFilename)) {
            return $newFilename;
        }

        throw new Exception("Falha ao salvar a imagem.");
    }
}
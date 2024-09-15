<?php
require_once 'connect.php';

class Usuario extends Connect
{
    //Inicio -- index
    public function index($perm)
    {
        if ($perm == 1) {
            $stmt = $this->SQL->prepare("SELECT * FROM `usuario`");
            $stmt->execute();
            $this->result = $stmt->get_result();

            while ($row[] = mysqli_fetch_array($this->result));
            return json_encode($row);
        } else {
            echo "Você não tem Permissao de acesso a este conteúdo!";
        }
    } //Fim -- index

    public function insertUser($username, $email, $password, $pt_file, $perm)
    {
        $stmt = $this->SQL->prepare("INSERT INTO `usuario`(`idUser`,`Username`,`Email`,`Password`,`arquivo`,`DataRegistro`,`permissão`) VALUES (NULL,?,?,?,?,CURRENT_TIMESTAMP,?)");
        $stmt->bind_param("ssssi", $username, $email, $password, $pt_file, $perm);
        $result = $stmt->execute();
        $last_id = $stmt->insert_id;
        $stmt->close();
        
        if ($result) {
            header('Location: ../../views/usuarios/index.php?alert=1');
        } else {
            header('Location: ../../views/usuarios/index.php?alert=0');
        }
    }
}

$usuario = new Usuario;

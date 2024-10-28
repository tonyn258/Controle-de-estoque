<?php
require_once 'connect.php';

class MinhaClasse extends Connect {
  function index() {
    // Verifica se a conexão com o banco de dados está funcionando
    if (!$this->SQL) {
      die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
    }

    $this->query = "SELECT `ValorCompra`, `DataCompra` FROM `compras` ORDER BY `DataCompra` ASC";
    $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));
    $row = array();
    while ($r = mysqli_fetch_assoc($this->result)) {
      $row[] = $r;
    }

    // Verifica se houve algum resultado
    if (count($row) > 0) {
      return json_encode($row);
    } else {
      return json_encode(array("message" => "Nenhum resultado encontrado."));
    }
  }
}

$obj = new MinhaClasse();
echo $obj->index();

?>
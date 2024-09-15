<?php
require_once 'connect.php';

class Vendas extends Connect
{
  // Função que busca e retorna todas as vendas do banco de dados
  function indexView($order_by = "")
  {
    if (!$this->SQL) {
      die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
    }

    // Query que retorna todas as vendas, juntando as tabelas vendas, compras e cliente
    $query = "SELECT v.idVendas, v.valor, v.DataVenda, v.CodRastreioV, 
                     c.skuProduto, c.model, c.NomeProduto, 
                     cl.NomeCliente, cl.cpfCliente, cl.idCliente,v.Vd_Tax
              FROM vendas v
              LEFT JOIN compras c ON v.Id_Compra = c.IdCompra
              LEFT JOIN cliente cl ON v.cliente_idCliente = cl.idCliente
              $order_by";

    $this->result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    $row = array();
    while ($r = mysqli_fetch_assoc($this->result)) {
      $row[] = $r;
    }

    if (count($row) > 0) {
      return json_encode($row);
    } else {
      return json_encode(array("message" => "Nenhum resultado encontrado."));
    }
  }

  // Função para inserir uma nova venda no banco de dados
  function insertVenda($Itensquant, $valor, $Id_Compra, $cliente_idCliente, $DataVenda, $CodRastreioV,$Vd_Tax)
  {
    // Escapa caracteres especiais para evitar SQL injection
    $Itensquant        = mysqli_real_escape_string($this->SQL, $Itensquant);
    $valor             = mysqli_real_escape_string($this->SQL, $valor);
    $Id_Compra         = mysqli_real_escape_string($this->SQL, $Id_Compra);
    $cliente_idCliente = mysqli_real_escape_string($this->SQL, $cliente_idCliente);
    $DataVenda         = mysqli_real_escape_string($this->SQL, $DataVenda);
    $CodRastreioV      = mysqli_real_escape_string($this->SQL, $CodRastreioV);
    $Vd_Tax      = mysqli_real_escape_string($this->SQL, $Vd_Tax);

    // Query de inserção
    $query = "INSERT INTO `vendas`(`Itensquant`, `valor`, `Id_Compra`, `cliente_idCliente`, `DataVenda`, `CodRastreioV`,`Vd_Tax`) 
              VALUES ('$Itensquant', '$valor', '$Id_Compra', '$cliente_idCliente', '$DataVenda', '$CodRastreioV', '$Vd_Tax')";
    
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    
    if ($result) {
      return 1;
    } else {
      return 0;
    }
    mysqli_close($this->SQL);
  }
}
?>

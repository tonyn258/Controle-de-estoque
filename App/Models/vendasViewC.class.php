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

    // Nova query conforme solicitado, juntando as tabelas vendas, cliente e compras
    $query = "SELECT v.CodRastreioV, 
                     c.NomeCliente, 
                     c.CepCliente,                      
                     cp.NomeProduto,
                     v.Itensquant,
                     v.Vd_Tax,
                     v.Diferenca_Quantidade,
                     v.Venda_Total
              FROM vendas v
              JOIN cliente c ON v.cliente_idCliente = c.idCliente
              JOIN anuncio cp ON v.anuncio_id = cp.IdCompra
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
  function insertVenda($Itensquant, $valor, $anuncio_id, $cliente_idCliente, $DataVenda, $CodRastreioV, $Vd_Tax, $Venda_Total, $Diferenca_Quantidade)
  {
    // Escapa caracteres especiais para evitar SQL injection
    $Itensquant        = mysqli_real_escape_string($this->SQL, $Itensquant);
    $valor             = mysqli_real_escape_string($this->SQL, $valor);
    $anuncio_id        = mysqli_real_escape_string($this->SQL, $anuncio_id);
    $cliente_idCliente = mysqli_real_escape_string($this->SQL, $cliente_idCliente);
    $DataVenda         = mysqli_real_escape_string($this->SQL, $DataVenda);
    $CodRastreioV      = mysqli_real_escape_string($this->SQL, $CodRastreioV);
    $Vd_Tax            = mysqli_real_escape_string($this->SQL, $Vd_Tax);
    $Venda_Total       = mysqli_real_escape_string($this->SQL, $Venda_Total);
    $Diferenca_Quantidade       = mysqli_real_escape_string($this->SQL, $Diferenca_Quantidade);

    // Query de inserção
    $query = "INSERT INTO `vendas`(`Itensquant`, `valor`, `anuncio_id`, `cliente_idCliente`, `DataVenda`, `CodRastreioV`, `Vd_Tax` , `Venda_Total` , `Diferenca_Quantidade`) 
              VALUES ('$Itensquant', '$valor', '$anuncio_id', '$cliente_idCliente', '$DataVenda', '$CodRastreioV', '$Vd_Tax' , '$Venda_Total','$Diferenca_Quantidade')";

    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {
      return 1;
    } else {
      return 0;
    }
    mysqli_close($this->SQL);
  }
}

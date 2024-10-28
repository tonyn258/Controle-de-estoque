
<?php
require_once 'connect.php';

class Compras extends Connect
{
  // Função que busca e retorna todas as compras do banco de dados
  function index($value)
  {
    // Verifica se a conexão com o banco de dados está funcionando
    if (!$this->SQL) {
      die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
    }
    $this->query = "SELECT * FROM `compras` ORDER BY `IdCompra` DESC";
    

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
  } //fim -- index

  // Função que insere uma nova compra no banco de dados
  function insertCompras($skuProduto, $model, $NomeProduto, $CodRastreio, $ValorCompra, $DataCompra, $QuantItens)
  {
    // Escapa caracteres especiais para evitar SQL injection
    $skuProduto   = mysqli_real_escape_string($this->SQL, $skuProduto);
    $model        = mysqli_real_escape_string($this->SQL, $model);
    $NomeProduto  = mysqli_real_escape_string($this->SQL, $NomeProduto);
    $CodRastreio  = mysqli_real_escape_string($this->SQL, $CodRastreio);
    $ValorCompra  = mysqli_real_escape_string($this->SQL, $ValorCompra);
    $DataCompra   = mysqli_real_escape_string($this->SQL, $DataCompra);
    $QuantItens   = mysqli_real_escape_string($this->SQL, $QuantItens);
    // Monta a query de inserção
    $query = "INSERT INTO `compras`(`skuProduto`, `model`,`NomeProduto`, `CodRastreio`, `ValorCompra`, `DataCompra`,`QuantItens`) 
              VALUES ('$skuProduto', '$model','$NomeProduto', '$CodRastreio', '$ValorCompra', '$DataCompra','$QuantItens')";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    // Verifica se a inserção foi realizada com sucesso
    if ($result) {
      return 1;
    } else {
      return 0;
    }
    mysqli_close($this->SQL);
  } // Fim Insert Compras      
  // Função que busca e retorna os dados de uma compra com base no seu IdCompra  
  public function EditCompras($IdCompra)
  {
    // Executa a query e verifica se houve resultados
    $this->query = "SELECT * FROM `compras` WHERE `IdCompra` = '$IdCompra'";
    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {

      if ($row = mysqli_fetch_array($this->result)) {
        // Preenche um array com os dados da compra
        $skuProduto  = $row['skuProduto'];
        $model       = $row['model'];
        $NomeProduto = $row['NomeProduto'];
        $CodRastreio = $row['CodRastreio'];
        $ValorCompra = $row['ValorCompra'];
        $DataCompra  = $row['DataCompra'];
        $DataEntrega = $row['DataEntrega'];
        $QuantItens  = $row['QuantItens'];
        

        // Declare a variável $array fora do bloco condicional
        $array = array('compras' => [
          'SKU'             => $skuProduto,
          'Modelo'          => $model,
          'Nome'            => $NomeProduto,
          'Rastreio'        => $CodRastreio,
          'Valor'           => $ValorCompra,
          'Data'            => $DataCompra,
          'Entrega'         => $DataEntrega,
          'Saldo'           => $QuantItens,
          
        ]);

        return $array; // feche a chave da função
      }
    }
    return 0; // retorne um valor padrão para o caso em que a query não é executada
  }

  public function UpdateCompras($IdCompra, $skuProduto, $model, $NomeProduto, $CodRastreio, $ValorCompra, $DataCompra, $DataEntrega, $QuantItens)
  {
    // Altera os valores do produto com base no seu IdCompra
    $this->query = "UPDATE `compras` SET 
                    `skuProduto`  = '$skuProduto', 
                    `model`       = '$model',
                    `NomeProduto` = '$NomeProduto', 
                    `CodRastreio` = '$CodRastreio', 
                    `ValorCompra` = '$ValorCompra', 
                    `DataCompra`  = '$DataCompra', 
                    `DataEntrega` = '$DataEntrega', 
                    `QuantItens`  = '$QuantItens'

                    
              WHERE `IdCompra`    = '$IdCompra'";

    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
      
      header('Location: ../../views/compras/index.php?alert=1');
    } else {
      header('Location: ../../views/compras/index.php?alert=0');
    }
  }
} //fim -- classe Compras
$compras = new Compras;

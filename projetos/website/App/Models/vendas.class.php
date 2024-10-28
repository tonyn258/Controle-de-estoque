<?php
require_once 'connect.php';
class Vendas extends Connect
{

    function index($value)
    {
      // Verifica se a conexão com o banco de dados está funcionando
      if (!$this->SQL) {
        die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
      }
      $this->query = "SELECT * FROM `vendas`";  
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

    public function itensVerify($Id_Compra, $quant){     
        $this->query = "SELECT * FROM `compras` WHERE `IdCompra` = '$Id_Compra'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));
        $total = mysqli_num_rows($this->result);
      
        if ($total > 0) {
          if ($row = mysqli_fetch_array($this->result)) {
            $q = $row['QuantItens'];
            $v = $row['QuantItensVend'];
            $quantotal = $v + $quant;
      
            if ($q >= $quantotal) {
              return array('status' => '1', 'NomeProduto' => $row['NomeProduto']);
            } else {
              $estoque = $q - $v;
              return array('status' => '0', 'NomeProduto' => $row['NomeProduto'], 'estoque' => $estoque);
            }
          }
        } else {
          $_SESSION['msg'] = '<div class="alert alert-warning"><strong>Ops!</strong> Compra ('.$Id_Compra.') não encontrada!</div>';
          header('Location: ../../views/vendas/index.php');
          exit();
        }
    } 

    

        
    public function itensVendido($Id_Compra, $quant,$NomeCliente,$cpfCliente,$FoneCliente,$Cidade,$UF,$idUsuario,$DataVenda, $CodRastreioV,$TxMl,$TxFret,$Vd_Tax){
        // Verificar se o item de compra existe
        $this->query = "SELECT * FROM `compras` WHERE `IdCompra` = '$Id_Compra'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        if ($this->result) {

            if ($row = mysqli_fetch_array($this->result)) {
                $q = $row['QuantItens'];
                $v = $row['QuantItensVend'];
                $quantotal = $v + $quant;

                if ($q >= $quantotal) {

                    $valor = ($row['ValorCompra'] * $quant);
                    $Compra_id = $row['ValorCompra'];
                    $compra_idData = $row['DataEntrega'];

                    $id = Vendas::idCliente($cpfCliente); // Verifica se o cliente existe no DB.
                    if ($id > 0) { // Se o cliente existir, Retorne o ID do cliente
                        $idCliente = $id; // ID do cliente                    
                    } else {

                        // Caso o cliente não exista, adicionar um novo cliente
                        $this->NovoClient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `Cidade`, `UF`, `FoneCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) 
                    VALUES (NULL,'$NomeCliente','$Cidade','$UF','$FoneCliente','$cpfCliente',1,'$idUsuario')";
                        if (mysqli_query($this->SQL, $this->NovoClient) or die(mysqli_error($this->SQL))) {
                            $idCliente = mysqli_insert_id($this->SQL);
                        }
                    }
                    // Registrar a venda
                    $this->query = "INSERT INTO `vendas`(`Itensquant`,`Compra_id`, `valor`, `Id_Compra`,`cliente_idCliente`,`compra_idData`, `DataVenda`, `CodRastreioV`, `taxa`, `Frete`, `Venda`) 
                                                VALUES ('$quant','$Compra_id','$valor','$Id_Compra','$idCliente','$compra_idData','$DataVenda','$CodRastreioV','$TxMl','$TxFret','$Vd_Tax')";
                    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                        // Atualizar a quantidade de itens vendidos na tabela de compras
                        $this->query = "UPDATE `compras` SET `QuantItensVend` = '$quantotal' WHERE `IdCompra`= '$Id_Compra '";
                        if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {

                            $_SESSION['msg'] = 'Venda efetuada';
                            header('Location: ../../views/vendas/');
                        }
                    } else {
                        $_SESSION['msg'] =  'Não foi possível efetuar a venda!';
                        header('Location: ../../views/vendas/');
                    }
                } else {
                    // Quantidade de itens maior do que o estoque disponível
                    $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                    echo 'Quantidade maior do que em estoque </br> Quantidade em estoque disponivel: ' . $estoque;
                    $_SESSION['msg'] = $estoque;
                    header('Location: ../../views/vendas/');
                }
            } else {
                $_SESSION['msg'] =  'Produto não encontrado';
                header('Location: ../../views/vendas/');
            }
        } else {
            header('Location: ../../views/compras/index.php?alert=0');
        }
    } //intesVendidos

    //Consultar se exite o CPF para não repetir
    public function idcliente($cpfCliente)
    {
        $this->client = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$cpfCliente'";
        if ($this->resultcliente = mysqli_query($this->SQL, $this->client) or die(mysqli_error($this->SQL))) {
            $row = mysqli_fetch_array($this->resultcliente);
            return $idCliente = $row['idCliente'];
        }
    } // Fim Consultar se exite o CPF para não repetir

    //----------itemNome
    public function itemNome($IdCompra)
    {
        $query = "SELECT `NomeProduto` FROM `compras` WHERE `IdCompra` = '$IdCompra'"; //
        $result = mysqli_query($this->SQL, $query)  or die(mysqli_error($this->SQL));

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $resp = $row['NomeProduto'];
        } else {
            $resp = NULL;
        }
        return $resp;
    } //--itemNome
}//Class

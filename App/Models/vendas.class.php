<?php
require_once __DIR__ . '/connect.php';



class Vendas extends Connect
{
    public function itensVerify($Id_Compra, $quant)
    {
        $this->query = "SELECT * FROM `anuncio` WHERE `idAnuncio` = '$Id_Compra'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        if (mysqli_num_rows($this->result) > 0) {
            $row = mysqli_fetch_array($this->result);
            $q = $row['QuantItens'];
            $v = $row['QuantItensVend'];
            $quantotal = $v + $quant;

            if ($q >= $quantotal) {
                return array('status' => '1', 'NomeProduto' => $row['NomeProduto']);
            } else {
                $estoque = $q - $v;
                return array('status' => '0', 'NomeProduto' => $row['NomeProduto'], 'estoque' => $estoque);
            }
        } else {
            return array('status' => '0', 'NomeProduto' => 'Produto não encontrado', 'estoque' => 0);
        }
    }

    public function itensVendido($idItem, $quant, $NomeCliente, $cpfCliente, $FoneCliente, $Cidade, $UF, $idUsuario, $DataVenda, $CodRastreioV, $TxMl, $TxFret, $Vd_Tax, $Diferenca_Venda_Compra, $Diferenca_Quantidade, $Venda_Total)
    {
        // Verificar se o item de compra existe
        $this->query = "SELECT * FROM `anuncio` WHERE `idAnuncio` = '$idItem'";
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

                    $id = $this->idcliente($cpfCliente); // Verifica se o cliente existe no DB.
                    if ($id > 0) { // Se o cliente existir, Retorne o ID do cliente
                        $idCliente = $id; // ID do cliente                    
                    } else {

                        // Caso o cliente não exista, adicionar um novo cliente
                        $this->NovoClient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `CepCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`, `Cidade`, `UF`) 
                    VALUES (NULL,'$NomeCliente','$FoneCliente','$cpfCliente',1,'$idUsuario', '$Cidade', '$UF')";
                        if (mysqli_query($this->SQL, $this->NovoClient) or die(mysqli_error($this->SQL))) {
                            $idCliente = mysqli_insert_id($this->SQL);
                        }
                    }
                    // Registrar a venda
                    $this->query = "INSERT INTO `vendas`(`Itensquant`,`anuncio_id`, `valor`, `IdItem`,`cliente_idCliente`,`anuncio_data`, `DataVenda`, `CodRastreioV`,`TxMl`,`TxFret`,`Vd_Tax`, `Diferenca_Venda_Compra`, `Diferenca_Quantidade`, `Venda_Total`) 
                                                VALUES ('$quant','$Compra_id','$valor','$idItem','$idCliente','$compra_idData','$DataVenda','$CodRastreioV','$TxMl','$TxFret','$Vd_Tax', '$Diferenca_Venda_Compra', '$Diferenca_Quantidade', '$Venda_Total')";
                    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                        // Atualizar a quantidade de itens vendidos na tabela de compras
                        $this->query = "UPDATE `anuncio` SET `QuantItensVend` = '$quantotal' WHERE `idAnuncio`= '$idItem '";
                        if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {

                            $_SESSION['msg'] = 'Venda efetuada';
                            // header('Location: ../../views/vendas/');
                        }
                    } else {
                        $_SESSION['msg'] =  'Não foi possível efetuar a venda!';
                        // header('Location: ../../views/vendas/');
                    }
                } else {
                     // Quantidade de itens maior do que o estoque disponível
                    $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                    // echo 'Quantidade maior do que em estoque </br> Quantidade em estoque disponivel: ' . $estoque;
                    $_SESSION['msg'] = $estoque;
                    // header('Location: ../../views/vendas/');
                }
            } else {
                $_SESSION['msg'] =  'Produto não encontrado';
                // header('Location: ../../views/vendas/');
            }
        } else {
            // header('Location: ../../views/compras/index.php?alert=0');
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
    }// Fim Consultar se exite o CPF para não repetir

    public function itemNome($idAnuncio)
    {
        $query = "SELECT `NomeProduto` FROM `anuncio` WHERE `idAnuncio` = '$idAnuncio'";
        $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            return $row['NomeProduto'];
        }
        return null;
    }
}//Class
       

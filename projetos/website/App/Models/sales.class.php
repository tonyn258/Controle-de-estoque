<?php
require_once 'connect.php';



class Vendas extends Connect
{
    public function itensVendidos($Id_Compra, $quant,$NomeCliente,$cpfCliente,$FoneCliente,$Cidade,$UF,$idUsuario,$DataVenda, $CodRastreioV,$TxMl,$TxFret,$Vd_Tax)
    {
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
                    $this->query = "INSERT INTO `vendas`(`Itensquant`,`Compra_id`, `valor`, `Id_Compra`,`cliente_idCliente`,`compra_idData`, `DataVenda`, `CodRastreioV`,`TxMl`,`TxFret`,`Vd_Tax`) 
                                                VALUES ('$quant','$Compra_id','$valor','$Id_Compra','$idCliente','$compra_idData','$DataVenda','$CodRastreioV','$TxMl','$TxFret','$Vd_Tax')";//,'$TxMl','$TxFret','$Vd_Tax'
                    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                        // Atualizar a quantidade de itens vendidos na tabela de compras
                        $this->query = "UPDATE `compras` SET `QuantItensVend` = '$quantotal' WHERE `IdCompra`= '$Id_Compra '";
                        if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {

                            $_SESSION['msg'] = 'Venda efetuada';
                            header('Location: ../../views/sales/');
                        }
                    } else {
                        $_SESSION['msg'] =  'Não foi possível efetuar a venda!';
                        header('Location: ../../views/sales/');
                    }
                } else {
                     // Quantidade de itens maior do que o estoque disponível
                    $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                    echo 'Quantidade maior do que em estoque </br> Quantidade em estoque disponivel: ' . $estoque;
                    $_SESSION['msg'] = $estoque;
                    header('Location: ../../views/sales/');
                }
            } else {
                $_SESSION['msg'] =  'Produto não encontrado';
                header('Location: ../../views/sales/');
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
    }// Fim Consultar se exite o CPF para não repetir
}//Class

       


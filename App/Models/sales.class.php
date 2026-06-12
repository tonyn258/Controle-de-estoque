<?php
require_once __DIR__ . '/connect.php';



class Vendas extends Connect
{
    public function itensVendidos($anuncio_id, $quant, $NomeCliente, $cpfCliente, $CepCliente, $idUsuario, $DataVenda, $CodRastreioV, $valorUnitario)
    {
        // Verificar se o item de compra existe
        $this->query = "SELECT * FROM `anuncio` WHERE `idAnuncio` = '$anuncio_id'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        if ($this->result) {


            if ($row = mysqli_fetch_array($this->result)) {
                $q = $row['QuantItens'];
                $v = $row['QuantItensVend'];
                $quantotal = $v + $quant;

                if ($q >= $quantotal) {

                    $valor = ($row['ValorCompra'] * $quant); // Custo Total
                    $Venda_Total = $valorUnitario * $quant; // Venda Total
                    $Lucro = $Venda_Total - $valor; // Diferença Venda - Compra
                    $EstoqueRestante = $q - $quantotal; // Diferença Quantidade
                    $Compra_id = $row['ValorCompra'];
                    $anuncio_data = $row['DataEntrega'];

                    $id = $this->idcliente($cpfCliente); // Verifica se o cliente existe no DB.
                    if ($id > 0) { // Se o cliente existir, Retorne o ID do cliente
                        $idCliente = $id; // ID do cliente                    
                    } else {

                        // Caso o cliente não exista, adicionar um novo cliente
                        $this->NovoClient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `CepCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) 
                    VALUES (NULL,'$NomeCliente','$CepCliente','$cpfCliente',1,'$idUsuario')";
                        if (mysqli_query($this->SQL, $this->NovoClient) or die(mysqli_error($this->SQL))) {
                            $idCliente = mysqli_insert_id($this->SQL);
                        }
                    }
                    // Registrar a venda
                    $this->query = "INSERT INTO `vendas`(`Itensquant`,`Compra_id`, `valor`, `anuncio_id`,`cliente_idCliente`,`anuncio_data`, `DataVenda`, `CodRastreioV`,`Venda_Total`,`Diferenca_Venda_Compra`,`Diferenca_Quantidade`,`usuario_id`, `Vd_Tax`) 
                                                VALUES ('$quant','$Compra_id','$valor','$anuncio_id','$idCliente','$anuncio_data','$DataVenda','$CodRastreioV','$Venda_Total','$Lucro','$EstoqueRestante','$idUsuario', '$valorUnitario')";
                    if ($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                        // Atualizar a quantidade de itens vendidos na tabela de compras
                        $this->query = "UPDATE `anuncio` SET `QuantItensVend` = '$quantotal' WHERE `idAnuncio`= '$anuncio_id '";
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

       

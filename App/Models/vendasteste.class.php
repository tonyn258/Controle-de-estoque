<?php
require_once 'connect.php';

class VendasTeste extends Connect
{
    public function index($value)
    {
        if (!$this->SQL) {
            die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
        }

        $this->query = "SELECT * FROM `vendas`";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        $row = array();
        while ($r = mysqli_fetch_assoc($this->result)) {
            $row[] = $r;
        }

        if (count($row) > 0) {
            return json_encode($row);
        } else {
            return json_encode([]);
        }
    }

    public function itensVerify($Id_Compra, $quant)
    {
        $this->query = "SELECT * FROM `anuncio` WHERE `IdCompra` = '$Id_Compra'";
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
            $_SESSION['msg'] = '<div class="alert alert-warning"><strong>Ops!</strong> Compra (' . $Id_Compra . ') não encontrada!</div>';
            header('Location: ../../views/vendas/index.php');
            exit();
        }
    }

    public function itensVendido($Id_Compra, $quant, $NomeCliente, $cpfCliente, $FoneCliente, $Cidade, $UF, $idUsuario, $DataVenda, $CodRastreioV, $TxMl, $TxFret, $Vd_Tax)
    {
        $this->query = "SELECT * FROM `anuncio` WHERE `IdCompra` = '$Id_Compra'";
        $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));

        if ($row = mysqli_fetch_array($this->result)) {
            $q = $row['QuantItens'];
            $v = $row['QuantItensVend'];
            $quantotal = $v + $quant;

            if ($q >= $quantotal) {
                $valor = ($row['ValorCompra'] * $quant);
                $Compra_id = $row['ValorCompra'];
                $compra_idData = $row['DataEntrega'];

                $id = $this->idCliente($cpfCliente);
                if ($id > 0) {
                    $idCliente = $id;
                } else {
                    $this->NovoClient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `Cidade`, `UF`, `FoneCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) 
                        VALUES (NULL,'$NomeCliente','$Cidade','$UF','$FoneCliente','$cpfCliente',1,'$idUsuario')";

                    if (mysqli_query($this->SQL, $this->NovoClient) or die(mysqli_error($this->SQL))) {
                        $idCliente = mysqli_insert_id($this->SQL);
                    }
                }

                $this->query = "INSERT INTO `vendas`(`Itensquant`, `Compra_id`, `valor`, `Id_Compra`, `cliente_idCliente`, `compra_idData`, `DataVenda`, `CodRastreioV`, `taxa`, `Frete`, `Venda`) 
                    VALUES ('$quant','$Compra_id','$valor','$Id_Compra','$idCliente','$compra_idData','$DataVenda','$CodRastreioV','$TxMl','$TxFret','$Vd_Tax')";

                if (mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                    $this->query = "UPDATE `anuncio` SET `QuantItensVend` = '$quantotal' WHERE `IdCompra`= '$Id_Compra'";
                    if (mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))) {
                        $_SESSION['msg'] = 'Venda efetuada';
                        header('Location: ../../views/vendas/');
                    }
                } else {
                    $_SESSION['msg'] = 'Não foi possível efetuar a venda!';
                    header('Location: ../../views/vendas/');
                }
            } else {
                $estoque = $row['QuantItens'] - $row['QuantItensVend'];
                $_SESSION['msg'] = 'Estoque insuficiente. Disponível: ' . $estoque;
                header('Location: ../../views/vendas/');
            }
        } else {
            $_SESSION['msg'] = 'Produto não encontrado';
            header('Location: ../../views/compras/index.php?alert=0');
        }
    }

    public function idCliente($cpfCliente)
    {
        $this->client = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$cpfCliente'";
        $this->resultcliente = mysqli_query($this->SQL, $this->client) or die(mysqli_error($this->SQL));

        if ($row = mysqli_fetch_array($this->resultcliente)) {
            return $row['idCliente'];
        }

        return 0;
    }

    public function itemNome($IdCompra)
    {
        $query = "SELECT `NomeProduto` FROM `anuncio` WHERE `IdCompra` = '$IdCompra'";
        $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            return $row['NomeProduto'];
        }

        return null;
    }
}

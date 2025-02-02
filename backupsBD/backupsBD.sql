-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: controlestoque
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente` (
  `idCliente` int(11) NOT NULL AUTO_INCREMENT,
  `NomeCliente` varchar(45) NOT NULL,
  `Cidade` varchar(45) NOT NULL,
  `UF` varchar(45) NOT NULL,
  `FoneCliente` varchar(50) NOT NULL,
  `cpfCliente` text NOT NULL,
  `dataRegCliente` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `statusCliente` int(1) NOT NULL,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  KEY `fk_Cliente_Usuario1_idx` (`Usuario_idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compras` (
  `IdCompra` int(11) NOT NULL AUTO_INCREMENT,
  `skuProduto` text NOT NULL,
  `model` text NOT NULL,
  `NomeProduto` text NOT NULL,
  `CodRastreio` varchar(20) DEFAULT NULL,
  `ValorCompra` decimal(18,2) NOT NULL,
  `DataCompra` date NOT NULL,
  `DataEntrega` date DEFAULT NULL,
  `QuantItens` decimal(10,0) NOT NULL,
  `QuantItensVend` decimal(10,0) NOT NULL,
  `Ativo` varchar(2) NOT NULL,
  `public` int(1) NOT NULL,
  PRIMARY KEY (`IdCompra`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itens`
--

DROP TABLE IF EXISTS `itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itens` (
  `idItens` int(1) NOT NULL AUTO_INCREMENT,
  `QuantItens` decimal(10,0) NOT NULL,
  `QuantItensVend` decimal(10,0) NOT NULL,
  `ItensAtivo` tinyint(4) NOT NULL,
  `ItensPublic` tinyint(1) NOT NULL,
  PRIMARY KEY (`idItens`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `idProduto` int(11) NOT NULL AUTO_INCREMENT,
  `skuProduto` text NOT NULL,
  `model` text NOT NULL,
  `NomeProduto` varchar(60) NOT NULL,
  `Quantidade` int(14) NOT NULL,
  `Conexao` varchar(100) NOT NULL,
  `Marca` varchar(100) NOT NULL,
  `statusProduto` varchar(15) NOT NULL,
  PRIMARY KEY (`idProduto`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL,
  `Email` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  `arquivo` varchar(100) NOT NULL,
  `DataRegistro` date DEFAULT NULL,
  `Permissão` tinyint(4) NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendas`
--

DROP TABLE IF EXISTS `vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendas` (
  `idVendas` int(11) NOT NULL AUTO_INCREMENT,
  `Itensquant` int(11) NOT NULL,
  `Compra_id` decimal(10,2) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `Id_Compra` int(5) NOT NULL,
  `cliente_idCliente` int(11) NOT NULL,
  `compra_idData` date NOT NULL DEFAULT current_timestamp(),
  `DataVenda` date NOT NULL,
  `CodRastreioV` varchar(20) NOT NULL,
  `TxMl` decimal(10,2) DEFAULT NULL,
  `TxFret` decimal(10,2) DEFAULT NULL,
  `Vd_Tax` decimal(10,2) DEFAULT NULL,
  `Diferenca_Venda_Compra` decimal(10,2) DEFAULT NULL,
  `Diferenca_Quantidade` decimal(10,2) DEFAULT NULL,
  `Venda_Total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idVendas`)
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER Atualiza_Valores_Vendas
BEFORE INSERT ON vendas
FOR EACH ROW
BEGIN
    SET NEW.Diferenca_Venda_Compra = NEW.Vd_Tax - NEW.Compra_id;
    SET NEW.Diferenca_Quantidade = (NEW.Vd_Tax - NEW.Compra_id) * NEW.Itensquant;
    SET NEW.Venda_Total = NEW.Vd_Tax * NEW.Itensquant;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER Atualiza_Valores_Vendas_Update
BEFORE UPDATE ON vendas
FOR EACH ROW
BEGIN
    SET NEW.Diferenca_Venda_Compra = NEW.Vd_Tax - NEW.Compra_id;
    SET NEW.Diferenca_Quantidade = (NEW.Vd_Tax - NEW.Compra_id) * NEW.Itensquant;
    SET NEW.Venda_Total = NEW.Vd_Tax * NEW.Itensquant;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-02 17:00:02

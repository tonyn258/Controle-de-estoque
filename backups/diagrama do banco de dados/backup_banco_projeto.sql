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
-- Table structure for table `anuncio`
--

DROP TABLE IF EXISTS `anuncio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anuncio` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `skuAnuncio` text NOT NULL,
  `model` text NOT NULL,
  `NomeProduto` text NOT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `ValorCompra` decimal(18,2) NOT NULL,
  `ValorVenda` decimal(10,2) DEFAULT NULL,
  `DataCompra` date NOT NULL,
  `QuantItens` decimal(10,0) NOT NULL,
  `QuantItensVend` decimal(10,0) NOT NULL,
  `Ativo` varchar(2) NOT NULL,
  `public` int(1) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `descricao` text NOT NULL,
  PRIMARY KEY (`idAnuncio`),
  KEY `fk_compras_categoria` (`idCategoria`),
  KEY `fk_anuncio_usuario` (`usuario_id`),
  CONSTRAINT `fk_anuncio_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compras_categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categoria_produto` (`idCategoria`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anuncio_imagem`
--

DROP TABLE IF EXISTS `anuncio_imagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anuncio_imagem` (
  `idImagem` int(11) NOT NULL AUTO_INCREMENT,
  `anuncio_id` int(11) NOT NULL,
  `caminhoImagem` varchar(255) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `dataCadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idImagem`),
  KEY `fk_anuncio_imagem` (`anuncio_id`),
  CONSTRAINT `fk_anuncio_imagem` FOREIGN KEY (`anuncio_id`) REFERENCES `anuncio` (`idAnuncio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categoria_produto`
--

DROP TABLE IF EXISTS `categoria_produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria_produto` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nomeCategoria` varchar(100) NOT NULL,
  `statusCategoria` enum('Ativo','Inativo') DEFAULT 'Ativo',
  `dataCadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente` (
  `idCliente` int(11) NOT NULL AUTO_INCREMENT,
  `NomeCliente` varchar(45) NOT NULL,
  `CepCliente` varchar(50) NOT NULL,
  `cpfCliente` text NOT NULL,
  `dataRegCliente` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `statusCliente` int(1) NOT NULL,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  KEY `fk_Cliente_Usuario1_idx` (`Usuario_idUsuario`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=422 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `idProduto` int(11) NOT NULL,
  `skuProduto` varchar(50) DEFAULT NULL,
  `nomeProduto` varchar(255) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `Conexao` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `especificacao` text DEFAULT NULL,
  `statusProduto` enum('ativo','inativo') DEFAULT NULL,
  `public` tinyint(1) DEFAULT NULL,
  `dataCadastro` datetime DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `DataCompra` date DEFAULT NULL,
  `QuantItens` int(11) DEFAULT 0,
  `ValorCompra` decimal(10,2) DEFAULT 0.00,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  KEY `fk_produto_usuario` (`usuario_id`),
  CONSTRAINT `fk_produto_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  `anuncio_id` int(11) NOT NULL,
  `cliente_idCliente` int(11) NOT NULL,
  `anuncio_data` date NOT NULL,
  `DataVenda` date NOT NULL,
  `CodRastreioV` varchar(20) NOT NULL,
  `Vd_Tax` decimal(10,2) DEFAULT NULL,
  `Diferenca_Venda_Compra` decimal(10,2) DEFAULT NULL,
  `Diferenca_Quantidade` decimal(10,2) DEFAULT NULL,
  `Venda_Total` decimal(10,2) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `fk_vendas_anuncio` (`anuncio_id`),
  KEY `fk_vendas_usuario` (`usuario_id`),
  CONSTRAINT `fk_vendas_anuncio` FOREIGN KEY (`anuncio_id`) REFERENCES `anuncio` (`idAnuncio`) ON UPDATE CASCADE,
  CONSTRAINT `fk_vendas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=618 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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

    -- Volta a usar Compra_id aqui

    SET NEW.Diferenca_Venda_Compra = NEW.Vd_Tax - NEW.Compra_id;

    

    -- E volta a usar Compra_id aqui também

    SET NEW.Diferenca_Quantidade = (NEW.Vd_Tax - NEW.Compra_id) * NEW.Itensquant;

    

    -- O total da venda permanece igual

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

    -- Volta a usar Compra_id aqui

    SET NEW.Diferenca_Venda_Compra = NEW.Vd_Tax - NEW.Compra_id;

    

    -- E volta a usar Compra_id aqui também

    SET NEW.Diferenca_Quantidade = (NEW.Vd_Tax - NEW.Compra_id) * NEW.Itensquant;

    

    -- O total da venda permanece igual

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

-- Dump completed on 2026-01-18 12:50:04

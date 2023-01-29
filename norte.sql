-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Jan-2023 às 09:58
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sfms`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `contrato`
--

CREATE TABLE `contrato` (
  `idContrato` int(11) NOT NULL,
  `numeroContrato` int(15) NOT NULL,
  `descricao` varchar(1000) DEFAULT NULL,
  `idEmpresa` int(100) NOT NULL,
  `idFornecedor` int(100) NOT NULL,
  `idTipoContrato` int(100) NOT NULL,
  `idResponsavel` int(100) NOT NULL,
  `idSetorResponsavel` int(100) NOT NULL,
  `dataInicioVigencia` date DEFAULT NULL,
  `dataTerminoVigencia` date DEFAULT NULL,
  `dataInicioExecucao` date DEFAULT NULL,
  `dataTerminoExecucao` date DEFAULT NULL,
  `dataAssinatura` date DEFAULT NULL,
  `valorGlobal` int(100) DEFAULT NULL,
  `qtdParcelas` int(100) DEFAULT NULL,
  `valorParcela` int(100) DEFAULT NULL,
  `telefone` int(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `notificacaoFimVigencia` int(100) DEFAULT NULL,
  `statusContrato` varchar(100) NOT NULL,
  `objetivoContrato` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `contrato`
--

INSERT INTO `contrato` (`idContrato`, `numeroContrato`, `descricao`, `idEmpresa`, `idFornecedor`, `idTipoContrato`, `idResponsavel`, `idSetorResponsavel`, `dataInicioVigencia`, `dataTerminoVigencia`, `dataInicioExecucao`, `dataTerminoExecucao`, `dataAssinatura`, `valorGlobal`, `qtdParcelas`, `valorParcela`, `telefone`, `email`, `notificacaoFimVigencia`, `statusContrato`, `objetivoContrato`) VALUES
(4, 432432, 'desc', 2, 3, 12, 2, 5, '2023-01-01', '2023-01-02', '2023-01-03', '2023-01-04', '2023-01-05', 6, 7, 8, 9, '10', 11, 'Suspenso', '13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `idEmpresa` int(11) NOT NULL,
  `nomeEmpresa` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`idEmpresa`, `nomeEmpresa`, `telefone`, `email`) VALUES
(2, 'teste', '123', '123');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `idFornecedor` int(11) NOT NULL,
  `nomeFornecedor` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `fornecedor`
--

INSERT INTO `fornecedor` (`idFornecedor`, `nomeFornecedor`, `telefone`, `email`) VALUES
(3, 'Fornecedor Rico', '123324543', 'fornecedor@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `responsavel`
--

CREATE TABLE `responsavel` (
  `idResponsavel` int(11) NOT NULL,
  `nomeResponsavel` varchar(100) NOT NULL,
  `telefone` int(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `responsavel`
--

INSERT INTO `responsavel` (`idResponsavel`, `nomeResponsavel`, `telefone`, `email`) VALUES
(2, 'RESPONSAVEL 1', 123, '123');

-- --------------------------------------------------------

--
-- Estrutura da tabela `setorresponsavel`
--

CREATE TABLE `setorresponsavel` (
  `idSetorResponsavel` int(11) NOT NULL,
  `setorResponsavel` varchar(250) NOT NULL,
  `telefone` int(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `setorresponsavel`
--

INSERT INTO `setorresponsavel` (`idSetorResponsavel`, `setorResponsavel`, `telefone`, `email`) VALUES
(5, 'SETOR 1', 123, '123');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipocontrato`
--

CREATE TABLE `tipocontrato` (
  `idTipoContrato` int(11) NOT NULL,
  `tipoContrato` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipocontrato`
--

INSERT INTO `tipocontrato` (`idTipoContrato`, `tipoContrato`) VALUES
(12, 'Premium');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `contrato`
--
ALTER TABLE `contrato`
  ADD PRIMARY KEY (`idContrato`);

--
-- Índices para tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`idEmpresa`);

--
-- Índices para tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`idFornecedor`);

--
-- Índices para tabela `responsavel`
--
ALTER TABLE `responsavel`
  ADD PRIMARY KEY (`idResponsavel`);

--
-- Índices para tabela `setorresponsavel`
--
ALTER TABLE `setorresponsavel`
  ADD PRIMARY KEY (`idSetorResponsavel`);

--
-- Índices para tabela `tipocontrato`
--
ALTER TABLE `tipocontrato`
  ADD PRIMARY KEY (`idTipoContrato`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contrato`
--
ALTER TABLE `contrato`
  MODIFY `idContrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `idEmpresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `idFornecedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `responsavel`
--
ALTER TABLE `responsavel`
  MODIFY `idResponsavel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `setorresponsavel`
--
ALTER TABLE `setorresponsavel`
  MODIFY `idSetorResponsavel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tipocontrato`
--
ALTER TABLE `tipocontrato`
  MODIFY `idTipoContrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

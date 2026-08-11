-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 06, 2026 at 12:52 AM
-- Server version: 12.3.2-MariaDB
-- PHP Version: 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisseg_sst`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrador`
--

CREATE TABLE `administrador` (
  `id_adm` int(11) NOT NULL,
  `nome_adm` varchar(255) NOT NULL,
  `cpf_adm` varchar(255) NOT NULL,
  `setor_adm` varchar(255) NOT NULL,
  `cargo_adm` varchar(255) NOT NULL,
  `turno_adm` enum('Matutino','Verspertino','Noturno','Integral') NOT NULL,
  `senha_adm` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atividade`
--

CREATE TABLE `atividade` (
  `id_atividade` int(11) NOT NULL,
  `nome_atividade` varchar(255) NOT NULL,
  `icone_atividade` varchar(255) NOT NULL,
  `id_nr_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atividade_epi`
--

CREATE TABLE `atividade_epi` (
  `id_atividade_epi` int(11) NOT NULL,
  `id_atividade_fk` int(11) NOT NULL,
  `id_epi_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificado`
--

CREATE TABLE `certificado` (
  `id_certificado` int(11) NOT NULL,
  `data_certificado` date NOT NULL,
  `pontos_certificado` decimal(3,1) NOT NULL,
  `id_prova_fk` int(11) NOT NULL,
  `id_funcionario_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checklist`
--

CREATE TABLE `checklist` (
  `id_checklist` int(11) NOT NULL,
  `data_checklist` datetime NOT NULL,
  `turno_checklist` enum('Matutino','Vespertino','Noturno','Integral') NOT NULL,
  `progresso_checklist` int(11) NOT NULL,
  `status_checklist` varchar(255) NOT NULL,
  `observacao_checklist` text NOT NULL,
  `id_adm_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_checkbox`
--

CREATE TABLE `checklist_checkbox` (
  `id_checklist_checkbox` int(11) NOT NULL,
  `nome_checklist_checkbox` varchar(255) NOT NULL,
  `opcoes_valor_checklist_checkbox` text NOT NULL,
  `id_checklist_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contato`
--

CREATE TABLE `contato` (
  `id_contato` int(11) NOT NULL,
  `nome_contato` varchar(255) NOT NULL,
  `numero_contato` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `epi`
--

CREATE TABLE `epi` (
  `id_epi` int(11) NOT NULL,
  `nome_epi` varchar(255) NOT NULL,
  `descricao_epi` text NOT NULL,
  `funcao_epi` text NOT NULL,
  `ca_epi` text NOT NULL,
  `qtd_minima_epi` int(11) NOT NULL,
  `qtd_epi` int(11) NOT NULL,
  `status_epi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `epi_funcao`
--

CREATE TABLE `epi_funcao` (
  `id_epi_funcao` int(11) NOT NULL,
  `id_epi_fk` int(11) NOT NULL,
  `id_funcao_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ficha`
--

CREATE TABLE `ficha` (
  `id_ficha` int(11) NOT NULL,
  `procedimentos_obrigatorios_ficha` text NOT NULL,
  `medidas_protecao_ficha` text NOT NULL,
  `id_atividade_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ficha_risco`
--

CREATE TABLE `ficha_risco` (
  `id_ficha_risco` int(11) NOT NULL,
  `id_ficha_fk` int(11) NOT NULL,
  `id_risco_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funcao`
--

CREATE TABLE `funcao` (
  `id_funcao` int(11) NOT NULL,
  `nome_funcao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funcionario`
--

CREATE TABLE `funcionario` (
  `id_funcionario` int(11) NOT NULL,
  `nome_funcionario` varchar(255) NOT NULL,
  `cpf_funcionario` varchar(255) NOT NULL,
  `setor_funcionario` varchar(255) NOT NULL,
  `cargo_funcionario` varchar(255) NOT NULL,
  `turno_funcionario` enum('Matutino','Verspertino','Noturno','Integral') NOT NULL,
  `senha_funcionario` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funcionario_treinamento`
--

CREATE TABLE `funcionario_treinamento` (
  `id_funcionario_treinamento` int(11) NOT NULL,
  `data_funcionario_treinamento` date NOT NULL,
  `id_funcionario_fk` int(11) NOT NULL,
  `id_treinamento_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incidente`
--

CREATE TABLE `incidente` (
  `id_incidente` int(11) NOT NULL,
  `data_incidente` date NOT NULL,
  `descricao_incidente` text NOT NULL,
  `local_incidente` varchar(255) NOT NULL,
  `acao_imediata_incidente` text NOT NULL,
  `gravidade_incidente` varchar(255) NOT NULL,
  `tipo_incidente` varchar(255) NOT NULL,
  `testemunhas_incidente` varchar(255) NOT NULL,
  `treinamento_reciclagem_incidente` varchar(255) NOT NULL,
  `fotos_incidente` mediumblob NOT NULL,
  `id_atividade_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indicadores`
--

CREATE TABLE `indicadores` (
  `id_indicadores` int(11) NOT NULL,
  `nome_equipe_indicadores` varchar(255) NOT NULL,
  `treinamento_percentual_indicadores` int(11) NOT NULL,
  `dias_sem_acidentes_indicadores` int(11) NOT NULL,
  `epi_percentual_indicadores` int(11) NOT NULL,
  `pontos_indicadores` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspecao`
--

CREATE TABLE `inspecao` (
  `id_inspecao` int(11) NOT NULL,
  `data_hora_inspecao` datetime NOT NULL,
  `epis_verificados_inspecao` int(11) NOT NULL,
  `status_inspecao` varchar(255) NOT NULL,
  `id_funcionario_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nr`
--

CREATE TABLE `nr` (
  `id_nr` int(11) NOT NULL,
  `nome_nr` varchar(255) NOT NULL,
  `descricao_nr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pontuacao_setor`
--

CREATE TABLE `pontuacao_setor` (
  `id_pontuacao_setor` int(11) NOT NULL,
  `nome_setor` varchar(255) NOT NULL,
  `pontuacao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prova`
--

CREATE TABLE `prova` (
  `id_prova` int(11) NOT NULL,
  `nome_prova` varchar(255) NOT NULL,
  `conteudo_prova` text NOT NULL,
  `id_treinamento_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questao`
--

CREATE TABLE `questao` (
  `id_questao` int(11) NOT NULL,
  `enunciado_questao` text NOT NULL,
  `alternativa_questao` enum('a','b','c','d','e') NOT NULL,
  `alt_a_questão` text NOT NULL,
  `alt_b_questão` text NOT NULL,
  `alt_c_questão` text NOT NULL,
  `alt_d_questão` text NOT NULL,
  `alt_e_questão` text NOT NULL,
  `id_prova_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risco`
--

CREATE TABLE `risco` (
  `id_risco` int(11) NOT NULL,
  `descricao_risco` text NOT NULL,
  `tipo_risco` varchar(255) NOT NULL,
  `nivel_risco` varchar(255) NOT NULL,
  `probabilidade_risco` int(11) NOT NULL,
  `severidade_risco` int(11) NOT NULL,
  `medidas_controle_risco` text NOT NULL,
  `epis_relacionados_risco` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treinamento`
--

CREATE TABLE `treinamento` (
  `id_treinamento` int(11) NOT NULL,
  `nome_treinamento` varchar(255) NOT NULL,
  `imagem_treinamento` mediumblob DEFAULT NULL,
  `subtitulo_treinamento` text DEFAULT NULL,
  `nr_treinamento` varchar(255) NOT NULL,
  `carga_horaria_treinamento` int(11) NOT NULL,
  `link_aulas_treinamento` text NOT NULL,
  `data_limite_treinamento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_adm`),
  ADD UNIQUE KEY `cpf_adm` (`cpf_adm`);

--
-- Indexes for table `atividade`
--
ALTER TABLE `atividade`
  ADD PRIMARY KEY (`id_atividade`),
  ADD KEY `id_nr_fk` (`id_nr_fk`);

--
-- Indexes for table `atividade_epi`
--
ALTER TABLE `atividade_epi`
  ADD PRIMARY KEY (`id_atividade_epi`),
  ADD KEY `id_atividade_fk` (`id_atividade_fk`),
  ADD KEY `id_epi_fk` (`id_epi_fk`);

--
-- Indexes for table `certificado`
--
ALTER TABLE `certificado`
  ADD PRIMARY KEY (`id_certificado`),
  ADD KEY `id_prova_fk1` (`id_prova_fk`),
  ADD KEY `id_funcionario_fk2` (`id_funcionario_fk`);

--
-- Indexes for table `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id_checklist`),
  ADD KEY `id_adm_fk` (`id_adm_fk`);

--
-- Indexes for table `checklist_checkbox`
--
ALTER TABLE `checklist_checkbox`
  ADD PRIMARY KEY (`id_checklist_checkbox`),
  ADD KEY `id_checklist_fk` (`id_checklist_fk`);

--
-- Indexes for table `contato`
--
ALTER TABLE `contato`
  ADD PRIMARY KEY (`id_contato`);

--
-- Indexes for table `epi`
--
ALTER TABLE `epi`
  ADD PRIMARY KEY (`id_epi`);

--
-- Indexes for table `epi_funcao`
--
ALTER TABLE `epi_funcao`
  ADD PRIMARY KEY (`id_epi_funcao`),
  ADD KEY `id_epi_fk1` (`id_epi_fk`),
  ADD KEY `id_funcao_fk` (`id_funcao_fk`);

--
-- Indexes for table `ficha`
--
ALTER TABLE `ficha`
  ADD PRIMARY KEY (`id_ficha`),
  ADD KEY `id_atividade_fk` (`id_atividade_fk`);

--
-- Indexes for table `ficha_risco`
--
ALTER TABLE `ficha_risco`
  ADD PRIMARY KEY (`id_ficha_risco`),
  ADD KEY `id_ficha_fk` (`id_ficha_fk`),
  ADD KEY `id_risco_fk` (`id_risco_fk`);

--
-- Indexes for table `funcao`
--
ALTER TABLE `funcao`
  ADD PRIMARY KEY (`id_funcao`);

--
-- Indexes for table `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD UNIQUE KEY `cpf_funcionario` (`cpf_funcionario`);

--
-- Indexes for table `funcionario_treinamento`
--
ALTER TABLE `funcionario_treinamento`
  ADD PRIMARY KEY (`id_funcionario_treinamento`),
  ADD KEY `id_treinamento_fk1` (`id_treinamento_fk`),
  ADD KEY `id_funcionario_fk3` (`id_funcionario_fk`);

--
-- Indexes for table `incidente`
--
ALTER TABLE `incidente`
  ADD PRIMARY KEY (`id_incidente`),
  ADD KEY `id_atividade_fk2` (`id_atividade_fk`);

--
-- Indexes for table `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id_indicadores`);

--
-- Indexes for table `inspecao`
--
ALTER TABLE `inspecao`
  ADD PRIMARY KEY (`id_inspecao`),
  ADD KEY `id_funcionario_fk1` (`id_funcionario_fk`);

--
-- Indexes for table `nr`
--
ALTER TABLE `nr`
  ADD PRIMARY KEY (`id_nr`);

--
-- Indexes for table `pontuacao_setor`
--
ALTER TABLE `pontuacao_setor`
  ADD PRIMARY KEY (`id_pontuacao_setor`);

--
-- Indexes for table `prova`
--
ALTER TABLE `prova`
  ADD PRIMARY KEY (`id_prova`),
  ADD KEY `id_treinamento_fk` (`id_treinamento_fk`);

--
-- Indexes for table `questao`
--
ALTER TABLE `questao`
  ADD PRIMARY KEY (`id_questao`),
  ADD KEY `id_prova_fk` (`id_prova_fk`);

--
-- Indexes for table `risco`
--
ALTER TABLE `risco`
  ADD PRIMARY KEY (`id_risco`);

--
-- Indexes for table `treinamento`
--
ALTER TABLE `treinamento`
  ADD PRIMARY KEY (`id_treinamento`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_adm` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atividade`
--
ALTER TABLE `atividade`
  MODIFY `id_atividade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atividade_epi`
--
ALTER TABLE `atividade_epi`
  MODIFY `id_atividade_epi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificado`
--
ALTER TABLE `certificado`
  MODIFY `id_certificado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checklist_checkbox`
--
ALTER TABLE `checklist_checkbox`
  MODIFY `id_checklist_checkbox` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contato`
--
ALTER TABLE `contato`
  MODIFY `id_contato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `epi`
--
ALTER TABLE `epi`
  MODIFY `id_epi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `epi_funcao`
--
ALTER TABLE `epi_funcao`
  MODIFY `id_epi_funcao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ficha`
--
ALTER TABLE `ficha`
  MODIFY `id_ficha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ficha_risco`
--
ALTER TABLE `ficha_risco`
  MODIFY `id_ficha_risco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funcao`
--
ALTER TABLE `funcao`
  MODIFY `id_funcao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funcionario_treinamento`
--
ALTER TABLE `funcionario_treinamento`
  MODIFY `id_funcionario_treinamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incidente`
--
ALTER TABLE `incidente`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id_indicadores` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspecao`
--
ALTER TABLE `inspecao`
  MODIFY `id_inspecao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nr`
--
ALTER TABLE `nr`
  MODIFY `id_nr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pontuacao_setor`
--
ALTER TABLE `pontuacao_setor`
  MODIFY `id_pontuacao_setor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prova`
--
ALTER TABLE `prova`
  MODIFY `id_prova` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questao`
--
ALTER TABLE `questao`
  MODIFY `id_questao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risco`
--
ALTER TABLE `risco`
  MODIFY `id_risco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treinamento`
--
ALTER TABLE `treinamento`
  MODIFY `id_treinamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atividade`
--
ALTER TABLE `atividade`
  ADD CONSTRAINT `id_nr_fk` FOREIGN KEY (`id_nr_fk`) REFERENCES `nr` (`id_nr`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `atividade_epi`
--
ALTER TABLE `atividade_epi`
  ADD CONSTRAINT `id_atividade_fk` FOREIGN KEY (`id_atividade_fk`) REFERENCES `atividade` (`id_atividade`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_epi_fk` FOREIGN KEY (`id_epi_fk`) REFERENCES `epi` (`id_epi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `certificado`
--
ALTER TABLE `certificado`
  ADD CONSTRAINT `id_funcionario_fk2` FOREIGN KEY (`id_funcionario_fk`) REFERENCES `funcionario` (`id_funcionario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_prova_fk1` FOREIGN KEY (`id_prova_fk`) REFERENCES `prova` (`id_prova`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `checklist`
--
ALTER TABLE `checklist`
  ADD CONSTRAINT `id_adm_fk` FOREIGN KEY (`id_adm_fk`) REFERENCES `administrador` (`id_adm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `checklist_checkbox`
--
ALTER TABLE `checklist_checkbox`
  ADD CONSTRAINT `id_checklist_fk` FOREIGN KEY (`id_checklist_fk`) REFERENCES `checklist` (`id_checklist`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `epi_funcao`
--
ALTER TABLE `epi_funcao`
  ADD CONSTRAINT `id_epi_fk1` FOREIGN KEY (`id_epi_fk`) REFERENCES `epi` (`id_epi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_funcao_fk` FOREIGN KEY (`id_funcao_fk`) REFERENCES `funcao` (`id_funcao`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ficha`
--
ALTER TABLE `ficha`
  ADD CONSTRAINT `id_atividade_fk` FOREIGN KEY (`id_atividade_fk`) REFERENCES `atividade` (`id_atividade`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ficha_risco`
--
ALTER TABLE `ficha_risco`
  ADD CONSTRAINT `id_ficha_fk` FOREIGN KEY (`id_ficha_fk`) REFERENCES `ficha` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_risco_fk` FOREIGN KEY (`id_risco_fk`) REFERENCES `risco` (`id_risco`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `funcionario_treinamento`
--
ALTER TABLE `funcionario_treinamento`
  ADD CONSTRAINT `id_funcionario_fk3` FOREIGN KEY (`id_funcionario_fk`) REFERENCES `funcionario` (`id_funcionario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_treinamento_fk1` FOREIGN KEY (`id_treinamento_fk`) REFERENCES `treinamento` (`id_treinamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `incidente`
--
ALTER TABLE `incidente`
  ADD CONSTRAINT `id_atividade_fk2` FOREIGN KEY (`id_atividade_fk`) REFERENCES `atividade` (`id_atividade`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inspecao`
--
ALTER TABLE `inspecao`
  ADD CONSTRAINT `id_funcionario_fk1` FOREIGN KEY (`id_funcionario_fk`) REFERENCES `funcionario` (`id_funcionario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prova`
--
ALTER TABLE `prova`
  ADD CONSTRAINT `id_treinamento_fk` FOREIGN KEY (`id_treinamento_fk`) REFERENCES `treinamento` (`id_treinamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questao`
--
ALTER TABLE `questao`
  ADD CONSTRAINT `id_prova_fk` FOREIGN KEY (`id_prova_fk`) REFERENCES `prova` (`id_prova`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Anfitrião:                    vsgate-s1.dei.isep.ipp.pt
-- Versão do servidor:           8.0.45 - MySQL Community Server - GPL
-- SO do servidor:               Linux
-- HeidiSQL Versão:              12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- A despejar estrutura para tabela db1241375.acessorios
CREATE TABLE IF NOT EXISTS `acessorios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `quantidade` int DEFAULT NULL,
  `id_estado` int DEFAULT NULL,
  `id_equipamento` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_estado` (`id_estado`),
  CONSTRAINT `acessorios_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `acessorios_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.acessorios: ~10 rows (aproximadamente)
INSERT INTO `acessorios` (`id`, `nome`, `quantidade`, `id_estado`, `id_equipamento`) VALUES
	(5, 'Circuito Ventilatório Adulto', 4, 1, 7),
	(6, 'Máscara Facial Tamanho M', 5, 1, 7),
	(7, 'Pás de Desfibrilhação Adulto', 1, 1, 4),
	(8, 'Cabo de Ligação ECG', 2, 1, 4),
	(9, 'Probe Convex 3.5MHz', 1, 1, 9),
	(10, 'Probe Linear 7.5MHz', 1, 2, 9),
	(14, 'Sensor de Fluxo', 3, 1, 3),
	(17, 'Sensor de SpO2 Adulto', 3, 1, 1),
	(20, 'Manga de Pressão Adulto', 2, 1, 2),
	(21, 'Sensor de Temperatura', 1, 1, 2),
	(22, 'Sensor', 1, 1, 30);

-- A despejar estrutura para tabela db1241375.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.categorias: ~7 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nome_categoria`) VALUES
	(1, 'Monitorização'),
	(2, 'Suporte de Vida'),
	(3, 'Diagnóstico'),
	(4, 'Terapia'),
	(5, 'Laboratório'),
	(6, 'Esterilização'),
	(7, 'Reabilitação');

-- A despejar estrutura para tabela db1241375.consumiveis
CREATE TABLE IF NOT EXISTS `consumiveis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `quantidade` int DEFAULT NULL,
  `id_equipamento` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipamento` (`id_equipamento`),
  CONSTRAINT `consumiveis_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.consumiveis: ~10 rows (aproximadamente)
INSERT INTO `consumiveis` (`id`, `nome`, `quantidade`, `id_equipamento`) VALUES
	(2, 'Circuito Respiratório Descartável', 10, 7),
	(3, 'Elétrodos ECG Descartáveis', 100, 5),
	(4, 'Gel para Ecografia 250ml', 15, 9),
	(5, 'Papel Térmico ECG 80mm', 8, 5),
	(6, 'Seringa 50ml para Perfusor', 50, 15),
	(7, 'Kit de Nebulização Descartável', 30, 20),
	(8, 'Reagentes ABL90 Pack Mensal', 2, 23),
	(9, 'Fita de Impressão Monitor MX40', 5, 13),
	(10, 'Solução de Limpeza Autoclave 5L', 3, 12),
	(13, 'Filtro Bacteriano HME', 20, 1),
	(14, 'Filtro', 3, 30);

-- A despejar estrutura para tabela db1241375.contratos
CREATE TABLE IF NOT EXISTS `contratos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_contrato` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `periodicidade` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `entidade_responsavel` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `id_fornecedor` int DEFAULT NULL,
  `id_documento` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_documento` (`id_documento`),
  KEY `id_fornecedor` (`id_fornecedor`),
  CONSTRAINT `contratos_ibfk_1` FOREIGN KEY (`id_documento`) REFERENCES `documentacao` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contratos_ibfk_2` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.contratos: ~12 rows (aproximadamente)
INSERT INTO `contratos` (`id`, `tipo_contrato`, `periodicidade`, `entidade_responsavel`, `observacoes`, `id_fornecedor`, `id_documento`) VALUES
	(3, 'Manutenção Preventiva e Corretiva', 'Semestral', NULL, 'Inclui calibração semestral', 5, 9),
	(4, 'Manutenção Preventiva', 'Anual', NULL, 'Contrato de 3 anos com Philips', 1, 11),
	(5, 'Manutenção Preventiva e Corretiva', 'Trimestral', NULL, 'Manutenção intensiva para bloco', 3, 13),
	(6, 'Manutenção Preventiva', 'Anual', NULL, 'Garantia alargada 3 anos', 5, 14),
	(7, 'Manutenção Preventiva', 'Anual', NULL, 'Inclui verificação de pressão', 5, 15),
	(8, 'Manutenção Preventiva', 'Anual', NULL, 'Garantia alargada 3 anos', 1, 17),
	(9, 'Manutenção Preventiva', 'Anual', NULL, 'Contrato de 3 anos B. Braun', 4, 25),
	(10, 'Manutenção Preventiva e Corretiva', 'Semestral', NULL, 'Monitor UCI com contrato total', 3, 24),
	(11, 'Manutenção Preventiva', 'Anual', NULL, 'Inclui calibração e reagentes', 3, 23),
	(12, 'Manutenção Preventiva', 'Anual', NULL, 'Garantia alargada GE', 8, 19),
	(17, 'Manutenção Preventiva e Corretiva', 'Semestral', NULL, NULL, NULL, 315),
	(19, 'Manutenção Preventiva', 'Anual', NULL, NULL, NULL, 329),
	(20, 'Manutenção Preventiva', 'Trimestral', 'sonic', NULL, NULL, 337);

-- A despejar estrutura para tabela db1241375.documentacao
CREATE TABLE IF NOT EXISTS `documentacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_documento` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `data_documento` date DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `caminho_ficheiro` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `id_tipo_documento` int NOT NULL,
  `id_equipamento` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_tipo_documento` (`id_tipo_documento`),
  CONSTRAINT `documentacao_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentacao_ibfk_2` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=340 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.documentacao: ~159 rows (aproximadamente)
INSERT INTO `documentacao` (`id`, `nome_documento`, `data_documento`, `data_validade`, `estado`, `caminho_ficheiro`, `observacoes`, `id_tipo_documento`, `id_equipamento`) VALUES
	(7, 'Manual de Serviço Desfibrilhador Zoll', '2021-07-05', NULL, 'Ativo', 'docs/servico_desfibrilhador.pdf', 'Manual técnico para assistência', 11, 4),
	(8, 'Certificado de Calibração Desfibrilhador', '2023-06-15', '2024-06-15', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', 'Calibração semestral', 7, 4),
	(9, 'Contrato de Manutenção Desfibrilhador Zoll', '2021-08-01', '2024-07-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Contrato semestral HospitalCare', 6, 4),
	(10, 'Relatório Técnico Eletrocardiógrafo', '2023-05-20', NULL, 'Ativo', 'docs/relatorio_ecg.pdf', 'Relatório de avaliação técnica', 10, 5),
	(11, 'Contrato de Manutenção Monitor MP5', '2022-10-01', '2025-09-30', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Contrato 3 anos Philips', 6, 6),
	(12, 'Manual de Utilizador Evita V500', '2021-05-18', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', 'Manual em português e inglês', 1, 7),
	(13, 'Contrato de Manutenção Evita V500', '2021-06-01', '2024-05-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Manutenção trimestral SaúdeTec', 6, 7),
	(14, 'Fatura de Aquisição Ecógrafo Logiq E10', '2023-01-15', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', 'Fatura de compra original', 3, 9),
	(15, 'Manual de Utilizador Autoclave 3870EA', '2022-03-07', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', 'Inclui instruções de segurança', 1, 12),
	(16, 'Certificado de Calibração Monitor MX40', '2023-09-01', '2024-09-01', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', 'Calibração anual', 7, 13),
	(17, 'Contrato de Manutenção Monitor MX40', '2023-04-01', '2026-03-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Garantia alargada 3 anos Philips', 6, 13),
	(18, 'Manual de Utilizador Ecógrafo Vscan', '2023-08-14', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', 'Manual de operação portátil', 1, 24),
	(19, 'Contrato de Manutenção Ecógrafo Vscan', '2023-09-01', '2026-08-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Garantia alargada GE Healthcare', 6, 24),
	(20, 'Declaração de Conformidade AED Plus', '2022-07-30', NULL, 'Ativo', 'docs/conformidade_aed.pdf', 'Certificação CE e normas IEC', 9, 22),
	(21, 'Manual de Serviço Analisador ABL90', '2021-12-01', NULL, 'Ativo', 'docs/servico_abl90.pdf', 'Manual técnico para laboratório', 11, 23),
	(22, 'Certificado de Calibração Analisador Gases', '2023-11-15', '2024-11-15', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', 'Calibração anual obrigatória', 7, 23),
	(23, 'Contrato de Manutenção Analisador ABL90', '2022-01-01', '2024-11-30', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Inclui calibração e reagentes', 6, 23),
	(24, 'Contrato de Manutenção Monitor BeneVision N17', '2023-06-01', '2026-05-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Contrato semestral SaúdeTec', 6, 21),
	(25, 'Contrato de Manutenção Bomba Perfusor Space', '2023-01-01', '2025-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', 'Contrato B. Braun 3 anos', 6, 15),
	(49, 'Relatório de Calibração Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 4),
	(50, 'Relatório de Calibração Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 5),
	(51, 'Relatório de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 6),
	(52, 'Relatório de Calibração Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 7),
	(53, 'Relatório de Calibração Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 8),
	(54, 'Relatório de Calibração Ecógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 9),
	(55, 'Relatório de Calibração Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 10),
	(56, 'Relatório de Calibração Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 11),
	(57, 'Relatório de Calibração Autoclave', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 12),
	(58, 'Relatório de Calibração Monitor de Sinais Vitais', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 13),
	(59, 'Relatório de Calibração Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 14),
	(60, 'Relatório de Calibração Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 15),
	(61, 'Relatório de Calibração Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 16),
	(62, 'Relatório de Calibração Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 17),
	(63, 'Relatório de Calibração Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 18),
	(64, 'Relatório de Calibração Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 19),
	(65, 'Relatório de Calibração Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 20),
	(66, 'Relatório de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 21),
	(67, 'Relatório de Calibração Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 22),
	(68, 'Relatório de Calibração Analisador de Gases', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 23),
	(69, 'Relatório de Calibração Ecógrafo Portátil', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 24),
	(78, 'Manual de Utilização Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 4),
	(79, 'Manual de Utilização Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 5),
	(80, 'Manual de Utilização Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 6),
	(81, 'Manual de Utilização Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 8),
	(82, 'Manual de Utilização Ecógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 9),
	(83, 'Manual de Utilização Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 10),
	(84, 'Manual de Utilização Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 11),
	(85, 'Manual de Utilização Monitor de Sinais Vitais', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 13),
	(86, 'Manual de Utilização Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 14),
	(87, 'Manual de Utilização Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 15),
	(88, 'Manual de Utilização Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 16),
	(89, 'Manual de Utilização Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 17),
	(90, 'Manual de Utilização Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 18),
	(91, 'Manual de Utilização Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 19),
	(92, 'Manual de Utilização Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 20),
	(93, 'Manual de Utilização Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 21),
	(94, 'Manual de Utilização Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 22),
	(95, 'Manual de Utilização Analisador de Gases', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 23),
	(111, 'Manual Técnico Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 4),
	(112, 'Manual Técnico Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 5),
	(113, 'Manual Técnico Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 6),
	(114, 'Manual Técnico Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 7),
	(115, 'Manual Técnico Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 8),
	(116, 'Manual Técnico Ecógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 9),
	(117, 'Manual Técnico Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 10),
	(118, 'Manual Técnico Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 11),
	(119, 'Manual Técnico Autoclave', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 12),
	(120, 'Manual Técnico Monitor de Sinais Vitais', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 13),
	(121, 'Manual Técnico Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 14),
	(122, 'Manual Técnico Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 15),
	(123, 'Manual Técnico Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 16),
	(124, 'Manual Técnico Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 17),
	(125, 'Manual Técnico Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 18),
	(126, 'Manual Técnico Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 19),
	(127, 'Manual Técnico Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 20),
	(128, 'Manual Técnico Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 21),
	(129, 'Manual Técnico Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 22),
	(130, 'Manual Técnico Analisador de Gases', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 23),
	(131, 'Manual Técnico Ecógrafo Portátil', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 24),
	(142, 'Fatura de Aquisição Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 4),
	(143, 'Fatura de Aquisição Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 5),
	(144, 'Fatura de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 6),
	(145, 'Fatura de Aquisição Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 7),
	(146, 'Fatura de Aquisição Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 8),
	(147, 'Fatura de Aquisição Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 10),
	(148, 'Fatura de Aquisição Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 11),
	(149, 'Fatura de Aquisição Autoclave', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 12),
	(150, 'Fatura de Aquisição Monitor de Sinais Vitais', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 13),
	(151, 'Fatura de Aquisição Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 14),
	(152, 'Fatura de Aquisição Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 15),
	(153, 'Fatura de Aquisição Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 16),
	(154, 'Fatura de Aquisição Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 17),
	(155, 'Fatura de Aquisição Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 18),
	(156, 'Fatura de Aquisição Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 19),
	(157, 'Fatura de Aquisição Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 20),
	(158, 'Fatura de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 21),
	(159, 'Fatura de Aquisição Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 22),
	(160, 'Fatura de Aquisição Analisador de Gases', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 23),
	(161, 'Fatura de Aquisição Ecógrafo Portátil', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 24),
	(173, 'Contrato de Aquisição Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 4),
	(174, 'Contrato de Aquisição Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 5),
	(175, 'Contrato de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 6),
	(176, 'Contrato de Aquisição Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 7),
	(177, 'Contrato de Aquisição Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 8),
	(178, 'Contrato de Aquisição Ecógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 9),
	(179, 'Contrato de Aquisição Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 10),
	(180, 'Contrato de Aquisição Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 11),
	(181, 'Contrato de Aquisição Autoclave', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 12),
	(182, 'Contrato de Aquisição Monitor de Sinais Vitais', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 13),
	(183, 'Contrato de Aquisição Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 14),
	(184, 'Contrato de Aquisição Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 15),
	(185, 'Contrato de Aquisição Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 16),
	(186, 'Contrato de Aquisição Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 17),
	(187, 'Contrato de Aquisição Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 18),
	(188, 'Contrato de Aquisição Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 19),
	(189, 'Contrato de Aquisição Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 20),
	(190, 'Contrato de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 21),
	(191, 'Contrato de Aquisição Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 22),
	(192, 'Contrato de Aquisição Analisador de Gases', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 23),
	(193, 'Contrato de Aquisição Ecógrafo Portátil', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 24),
	(203, 'Certificado de Calibração Eletrocardiógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 5),
	(204, 'Certificado de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 6),
	(205, 'Certificado de Calibração Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 7),
	(206, 'Certificado de Calibração Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 8),
	(207, 'Certificado de Calibração Ecógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 9),
	(208, 'Certificado de Calibração Desfibrilhador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 10),
	(209, 'Certificado de Calibração Analisador Bioquímico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 11),
	(210, 'Certificado de Calibração Autoclave', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 12),
	(211, 'Certificado de Calibração Ventilador de Transporte', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 14),
	(212, 'Certificado de Calibração Bomba de Seringa', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 15),
	(213, 'Certificado de Calibração Oxímetro de Pulso', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 16),
	(214, 'Certificado de Calibração Electroencefalógrafo', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 17),
	(215, 'Certificado de Calibração Cama Articulada Elétrica', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 18),
	(216, 'Certificado de Calibração Equipamento de Fisioterapia', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 19),
	(217, 'Certificado de Calibração Nebulizador', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 20),
	(218, 'Certificado de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 21),
	(219, 'Certificado de Calibração Desfibrilhador Automático', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 22),
	(220, 'Certificado de Calibração Ecógrafo Portátil', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 24),
	(288, 'Manual de Utilização Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 3),
	(289, 'Manual Técnico Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 3),
	(290, 'Fatura de Aquisição Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 3),
	(291, 'Contrato de Aquisição Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 3),
	(292, 'Certificado de Calibração Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 3),
	(293, 'Relatório de Calibração Bomba de Infusão', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 3),
	(294, 'Declaração de Conformidade Infusomat', '2020-11-10', NULL, 'Ativo', 'docs/conformidade_infusomat.pdf', NULL, 12, 3),
	(311, 'Manual de Utilizador VX-200', '2021-03-15', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/doc_6a308705ecb092.70137270.pdf', NULL, 1, 1),
	(312, 'Manual Técnico Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 1),
	(313, 'Fatura de Aquisição Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 1),
	(314, 'Contrato de Aquisição Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 1),
	(315, 'Contrato de Manutenção VX-200', '2021-04-01', '2031-03-12', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 6, 1),
	(316, 'Certificado de Calibração VX-200', '2023-03-10', '2029-03-07', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 1),
	(317, 'Relatório de Calibração Ventilador Pulmonar', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 1),
	(325, 'Manual de Utilizador BeneVision N12', '2022-06-20', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_utilizacao.pdf', NULL, 1, 2),
	(326, 'Manual Técnico Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_manual_tecnico.pdf', NULL, 2, 2),
	(327, 'Fatura de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_fatura_aquisicao.pdf', NULL, 3, 2),
	(328, 'Contrato de Aquisição Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 4, 2),
	(329, 'Contrato de Manutenção BeneVision N12', '2022-07-01', '2027-06-17', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_contrato_aquisicao.pdf', NULL, 6, 2),
	(330, 'Certificado de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_certificado_calibracao.pdf', NULL, 7, 2),
	(331, 'Relatório de Calibração Monitor Multiparamétrico', '2023-01-01', '2027-12-31', 'Ativo', '/techmedsolutions/assets/uploads/documentos/placeholder_relatorio_calibracao.pdf', NULL, 8, 2),
	(332, 'Manual', '2026-06-15', '2026-07-11', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a396fee9744e6.75841292.pdf', NULL, 1, 30),
	(333, 'manual', '2026-06-15', '2026-07-11', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a396fee9796e8.12156159.pdf', NULL, 2, 30),
	(334, 'fatura', '2026-06-21', '2026-07-09', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a3970338e05a4.28239870.pdf', NULL, 3, 30),
	(335, 'Contrato', '2026-06-08', '2026-06-30', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a3970338e5ca5.44585918.pdf', NULL, 4, 30),
	(336, 'certificado', '2026-06-08', '2026-06-30', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a39754bb2f285.03696571.pdf', NULL, 5, 30),
	(337, 'contrato', '2026-06-07', '2026-06-30', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a39754bb325e7.34668754.pdf', NULL, 6, 30),
	(338, 'certificado', '2026-06-15', '2026-06-30', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a39754bb364b9.07190214.pdf', NULL, 7, 30),
	(339, 'relatorio', '2026-06-09', '2026-07-11', 'Ativo', '/sibdas/1241375/techmedsolutions/assets/uploads/documentos/doc_6a39754bb39948.94031699.pdf', NULL, 8, 30);

-- A despejar estrutura para tabela db1241375.equipamentos
CREATE TABLE IF NOT EXISTS `equipamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_interno` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `designacao` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `fabricante` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `marca` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `num_serie` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `ano_fabrico` year DEFAULT NULL,
  `data_aquisicao` date DEFAULT NULL,
  `custo_aquisicao` decimal(10,2) DEFAULT NULL,
  `tipo_entrada` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `criticidade` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `id_localizacao` int DEFAULT NULL,
  `id_categoria` int DEFAULT NULL,
  `id_estado` int DEFAULT NULL,
  `tem_garantia` tinyint(1) NOT NULL DEFAULT '0',
  `tem_contrato` tinyint(1) NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_interno` (`codigo_interno`),
  KEY `id_localizacao` (`id_localizacao`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_estado` (`id_estado`),
  CONSTRAINT `equipamentos_ibfk_1` FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipamentos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipamentos_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.equipamentos: ~24 rows (aproximadamente)
INSERT INTO `equipamentos` (`id`, `codigo_interno`, `designacao`, `fabricante`, `marca`, `modelo`, `num_serie`, `ano_fabrico`, `data_aquisicao`, `custo_aquisicao`, `tipo_entrada`, `criticidade`, `observacoes`, `id_localizacao`, `id_categoria`, `id_estado`, `tem_garantia`, `tem_contrato`, `ativo`) VALUES
	(1, 'EQ-0001', 'Ventilador Pulmonar', 'Philips', 'Philips', 'VX-200', 'PH-VX200-2021-001', '2021', '2021-03-15', 28500.00, 'Empréstimo', 'Suporte de Vida', 'Equipamento de suporte ventilatório UCI', 6, 2, 1, 0, 1, 0),
	(2, 'EQ-0002', 'Monitor Multiparamétrico', 'Mindray', 'Mindray', 'BeneVision N12', 'MR-BVN12-2022-045', '2022', '2022-06-20', 12300.00, 'Compra', 'Suporte de Vida', 'Monitor com SpO2, ECG, NIBP e temperatura', 13, 1, 2, 0, 1, 0),
	(3, 'EQ-0003', 'Bomba de Infusão', 'B. Braun', 'B. Braun', 'Infusomat Space', 'BB-INF-2020-321', '2020', '2020-11-10', 3800.00, 'Compra', 'Alta', 'Bomba volumétrica para administração IV', 6, 4, 4, 0, 0, 1),
	(4, 'EQ-0004', 'Desfibrilhador', 'Zoll', 'Zoll', 'R Series', 'ZR-2021-7712', '2021', '2021-07-05', 18900.00, 'Compra', 'Suporte de Vida', 'Desfibrilhador com monitorização ECG integrada', 4, 2, 1, 0, 1, 1),
	(5, 'EQ-0005', 'Eletrocardiógrafo', 'GE Healthcare', 'GE', 'MAC 2000', 'GE-MAC2-2019-088', '2019', '2019-04-22', 6500.00, 'Compra', 'Média', 'ECG de 12 derivações para diagnóstico', 4, 3, 3, 0, 0, 0),
	(6, 'EQ-0006', 'Monitor Multiparamétrico', 'Philips', 'Philips', 'IntelliVue MP5', 'PH-MP5-2022-073', '2022', '2022-09-01', 14200.00, 'Compra', 'Suporte de Vida', 'Monitor para UCI com alarmes configuráveis', 4, 1, 1, 0, 1, 1),
	(7, 'EQ-0007', 'Ventilador Pulmonar', 'Dräger', 'Dräger', 'Evita V500', 'DR-EV500-2021-009', '2021', '2021-05-18', 32000.00, 'Compra', 'Suporte de Vida', 'Ventilador de alta performance para bloco operatório', 5, 2, 1, 0, 1, 1),
	(8, 'EQ-0008', 'Bomba de Infusão', 'B. Braun', 'B. Braun', 'Infusomat Space', 'BB-INF-2021-456', '2021', '2021-08-30', 3800.00, 'Compra', 'Alta', 'Bomba para serviço de medicina', 6, 4, 1, 0, 0, 1),
	(9, 'EQ-0009', 'Ecógrafo', 'GE Healthcare', 'GE', 'Logiq E10', 'GE-LOG-2023-012', '2023', '2023-01-15', 45000.00, 'Compra', 'Alta', 'Ecógrafo de diagnóstico por imagem', 3, 3, 1, 0, 0, 1),
	(10, 'EQ-0010', 'Desfibrilhador', 'Philips', 'Philips', 'HeartStart MRx', 'PH-HSMRX-2020-034', '2020', '2020-02-28', 17500.00, 'Compra', 'Suporte de Vida', 'Desfibrilhador portátil para urgência', 4, 2, 1, 0, 0, 1),
	(11, 'EQ-0011', 'Analisador Bioquímico', 'Roche', 'Roche', 'Cobas c311', 'RC-COB-2019-005', '2019', '2019-09-10', 22000.00, 'Compra', 'Média', 'Analisador para laboratório de análises', 7, 5, 3, 0, 0, 1),
	(12, 'EQ-0012', 'Autoclave', 'Tuttnauer', 'Tuttnauer', '3870EA', 'TT-3870-2022-001', '2022', '2022-03-07', 9800.00, 'Compra', 'Baixa', 'Esterilizador a vapor para bloco operatório', 5, 6, 1, 0, 0, 1),
	(13, 'EQ-0013', 'Monitor de Sinais Vitais', 'Philips', 'Philips', 'IntelliVue MX40', 'PH-MX40-2023-015', '2023', '2023-03-20', 8900.00, 'Compra', 'Alta', 'Monitor portátil para transporte de doentes', 4, 1, 1, 0, 1, 1),
	(14, 'EQ-0014', 'Ventilador de Transporte', 'Dräger', 'Dräger', 'Oxylog 3000', 'DR-OXY-2020-022', '2020', '2020-06-14', 15600.00, 'Compra', 'Suporte de Vida', 'Ventilador para transporte intra-hospitalar', 4, 2, 2, 0, 0, 1),
	(15, 'EQ-0015', 'Bomba de Seringa', 'B. Braun', 'B. Braun', 'Perfusor Space', 'BB-PERF-2022-089', '2022', '2022-11-25', 2900.00, 'Compra', 'Alta', 'Bomba de seringa para UCI', 2, 4, 1, 0, 1, 1),
	(16, 'EQ-0016', 'Oxímetro de Pulso', 'Nonin', 'Nonin', '9600', 'NN-9600-2021-034', '2021', '2021-02-10', 850.00, 'Compra', 'Média', 'Oxímetro portátil para triagem', 2, 1, 1, 0, 0, 1),
	(17, 'EQ-0017', 'Electroencefalógrafo', 'Natus', 'Natus', 'Xltek 32', 'NT-XLT-2020-007', '2020', '2020-08-19', 18700.00, 'Compra', 'Alta', 'EEG de 32 canais para neurologia', 11, 3, 1, 0, 0, 1),
	(18, 'EQ-0018', 'Cama Articulada Elétrica', 'Linet', 'Linet', 'Eleganza 5', 'LN-ELG5-2021-012', '2021', '2021-10-05', 3200.00, 'Compra', 'Baixa', 'Cama hospitalar para pediatria', 9, 4, 1, 0, 0, 1),
	(19, 'EQ-0019', 'Equipamento de Fisioterapia', 'BTL', 'BTL', '6000', 'BTL-6000-2022-003', '2022', '2022-04-18', 5400.00, 'Compra', 'Baixa', 'Equipamento de eletroterapia para reabilitação', 8, 7, 1, 0, 0, 1),
	(20, 'EQ-0020', 'Nebulizador', 'PARI', 'PARI', 'BOY S', 'PR-BOYS-2020-045', '2020', '2020-03-22', 420.00, 'Compra', 'Média', 'Nebulizador para tratamento respiratório', 9, 4, 3, 0, 0, 1),
	(21, 'EQ-0021', 'Monitor Multiparamétrico', 'Mindray', 'Mindray', 'BeneVision N17', 'MR-BVN17-2023-008', '2023', '2023-05-10', 16800.00, 'Compra', 'Suporte de Vida', 'Monitor central para UCI', 2, 1, 1, 0, 1, 1),
	(22, 'EQ-0022', 'Desfibrilhador Automático', 'Zoll', 'Zoll', 'AED Plus', 'ZR-AEDP-2022-019', '2022', '2022-07-30', 2100.00, 'Compra', 'Suporte de Vida', 'DAE para corredores e zonas comuns', 10, 2, 1, 0, 0, 1),
	(23, 'EQ-0023', 'Analisador de Gases', 'Radiometer', 'Radiometer', 'ABL90 FLEX', 'RM-ABL90-2021-006', '2021', '2021-12-01', 28000.00, 'Compra', 'Alta', 'Analisador de gases no sangue para UCI', 2, 5, 1, 0, 1, 1),
	(24, 'EQ-0024', 'Ecógrafo Portátil', 'GE Healthcare', 'GE', 'Vscan Air', 'GE-VSCA-2023-022', '2023', '2023-08-14', 9500.00, 'Compra', 'Alta', 'Ecógrafo portátil para urgência', 2, 3, 1, 0, 1, 1),
	(30, 'EQ-0025', 'Bomba De Infusão', 'Mindray', 'Mindray', 'BeneVision N12', '', '1909', '2026-06-09', NULL, 'Doação', 'Média', '', 10, 5, 4, 1, 1, 1);

-- A despejar estrutura para tabela db1241375.equipamentos_fornecedores
CREATE TABLE IF NOT EXISTS `equipamentos_fornecedores` (
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `tipo_relacao` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_equipamento`,`id_fornecedor`),
  KEY `id_fornecedor` (`id_fornecedor`),
  CONSTRAINT `equipamentos_fornecedores_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipamentos_fornecedores_ibfk_2` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.equipamentos_fornecedores: ~53 rows (aproximadamente)
INSERT INTO `equipamentos_fornecedores` (`id_equipamento`, `id_fornecedor`, `tipo_relacao`) VALUES
	(1, 1, 'Assistência Técnica'),
	(1, 2, 'Distribuidor'),
	(1, 3, 'Assistência Técnica'),
	(2, 1, 'Assistência Técnica'),
	(2, 2, 'Distribuidor'),
	(2, 3, 'Assistência Técnica'),
	(2, 9, 'Fabricante'),
	(3, 4, 'Fabricante'),
	(3, 6, 'Consumíveis / Acessórios'),
	(4, 1, 'Assistência Técnica'),
	(4, 3, 'Assistência Técnica'),
	(4, 5, 'Distribuidor'),
	(4, 11, 'Fabricante'),
	(5, 5, 'Distribuidor'),
	(5, 8, 'Fabricante'),
	(6, 1, 'Assistência Técnica'),
	(6, 3, 'Assistência Técnica'),
	(7, 1, 'Assistência Técnica'),
	(7, 2, 'Distribuidor'),
	(7, 3, 'Assistência Técnica'),
	(7, 7, 'Fabricante'),
	(8, 4, 'Fabricante'),
	(8, 6, 'Consumíveis / Acessórios'),
	(9, 5, 'Distribuidor'),
	(9, 8, 'Fabricante'),
	(10, 1, 'Assistência Técnica'),
	(10, 3, 'Assistência Técnica'),
	(11, 5, 'Distribuidor'),
	(12, 5, 'Distribuidor'),
	(13, 1, 'Fabricante'),
	(13, 2, 'Distribuidor'),
	(13, 3, 'Fabricante'),
	(14, 1, 'Assistência Técnica'),
	(14, 3, 'Assistência Técnica'),
	(14, 7, 'Fabricante'),
	(15, 4, 'Fabricante'),
	(15, 6, 'Consumíveis / Acessórios'),
	(16, 2, 'Distribuidor'),
	(17, 1, 'Assistência Técnica'),
	(17, 3, 'Assistência Técnica'),
	(17, 5, 'Distribuidor'),
	(18, 5, 'Distribuidor'),
	(19, 5, 'Distribuidor'),
	(21, 1, 'Assistência Técnica'),
	(21, 3, 'Assistência Técnica'),
	(21, 9, 'Fabricante'),
	(22, 5, 'Distribuidor'),
	(22, 11, 'Fabricante'),
	(23, 1, 'Assistência Técnica'),
	(23, 3, 'Assistência Técnica'),
	(23, 5, 'Distribuidor'),
	(24, 5, 'Distribuidor'),
	(24, 8, 'Fabricante'),
	(30, 8, 'Fabricante');

-- A despejar estrutura para tabela db1241375.estados
CREATE TABLE IF NOT EXISTS `estados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_estado` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_estado` (`nome_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.estados: ~4 rows (aproximadamente)
INSERT INTO `estados` (`id`, `nome_estado`) VALUES
	(1, 'Ativo'),
	(2, 'Em manutenção'),
	(3, 'Inativo'),
	(4, 'Em calibração');

-- A despejar estrutura para tabela db1241375.fornecedores
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_fornecedor` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `nome_empresa` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `tipo_fornecedor` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `nif` varchar(9) COLLATE utf8mb4_bin NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `morada` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `pessoa_contacto` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `telefone_contacto` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_fornecedor` (`codigo_fornecedor`),
  UNIQUE KEY `nif` (`nif`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.fornecedores: ~13 rows (aproximadamente)
INSERT INTO `fornecedores` (`id`, `codigo_fornecedor`, `nome_empresa`, `tipo_fornecedor`, `nif`, `website`, `email`, `telefone`, `morada`, `pessoa_contacto`, `telefone_contacto`, `observacoes`, `ativo`) VALUES
	(1, 'FORN-001', 'Philips Healthcare Portugal', 'Fabricante', '500112233', 'https://www.philips.pt/', 'geral@philips.pt', '+351 210 111 000', 'Av. Eng. Duarte Pacheco, 19, Lisboa', 'Ana Ferreira', '+351 910 111 222', 'Fabricante oficial de equipamentos de monitorização', 1),
	(2, 'FORN-002', 'MedEquip Portugal Lda.', 'Distribuidor', '500223344', 'www.medequip.pt', 'comercial@medequip.pt', '+351 220 222 111', 'Rua do Heroísmo, 45, Porto', 'João Sousa', '+351 920 222 333', 'Distribuidor nacional de equipamentos médicos', 1),
	(3, 'FORN-003', 'SaúdeTec Lda.', 'Assistência Técnica', '500334455', 'www.saudetec.pt', 'suporte@saudetec.pt', '+351 230 333 222', 'Rua de Santa Catarina, 120, Porto', 'Maria Costa', '+351 930 333 444', 'Empresa especializada em manutenção de equipamentos', 1),
	(4, 'FORN-004', 'B. Braun Medical Lda.', 'Fabricante', '500445566', 'www.bbraun.pt', 'info@bbraun.pt', '+351 210 444 333', 'Estrada Nacional 10, Queluz', 'Rui Oliveira', '+351 940 444 555', 'Fabricante de bombas de infusão e consumíveis', 1),
	(5, 'FORN-005', 'HospitalCare Solutions', 'Distribuidor', '500556677', 'www.hospitalcare.pt', 'vendas@hospitalcare.pt', '+351 210 555 444', 'Av. da Liberdade, 110, Lisboa', 'Carla Mendes', '+351 950 555 666', 'Distribuidor de equipamentos cirúrgicos e de diagnóstico', 1),
	(6, 'FORN-006', 'TechServ Médica', 'Consumíveis / Acessórios', '500667788', 'www.techserv.pt', 'geral@techserv.pt', '+351 220 666 555', 'Rua Álvares Cabral, 30, Braga', 'Pedro Lima', '+351 960 666 777', 'Fornecedor de consumíveis e acessórios médicos', 1),
	(7, 'FORN-007', 'Dräger Portugal Lda.', 'Fabricante', '500778899', 'www.draeger.com/pt', 'portugal@draeger.com', '+351 210 777 666', 'Rua Mário Castelhano, 40, Queluz', 'Sofia Nunes', '+351 970 777 888', 'Fabricante de ventiladores e equipamentos de anestesia', 1),
	(8, 'FORN-008', 'GE Healthcare Portugal', 'Fabricante', '500889900', 'www.gehealthcare.com', 'pt@gehealthcare.com', '+351 210 888 777', 'Av. José Malhoa, 16, Lisboa', 'Tiago Rodrigues', '+351 980 888 999', 'Fabricante de equipamentos de diagnóstico e imagiologia', 1),
	(9, 'FORN-009', 'Mindray Portugal', 'Fabricante', '500990011', 'www.mindray.com/pt', 'portugal@mindray.com', '+351 210 999 888', 'Rua Tomás da Fonseca, 55, Lisboa', 'Inês Carvalho', '+351 910 999 000', 'Fabricante de monitores multiparamétricos e ecógrafos', 1),
	(10, 'FORN-010', 'Siemens Healthineers Portugal', 'Fabricante', '501001122', 'www.siemens-healthineers.com/pt', 'pt@siemens-healthineers.com', '+351 210 001 100', 'Av. Dr. Mário Soares, 12, Amadora', 'Bruno Pinto', '+351 920 001 100', 'Fabricante de equipamentos de imagiologia e diagnóstico', 0),
	(11, 'FORN-011', 'Zoll Medical Portugal', 'Fabricante', '501112233', 'www.zoll.com', 'portugal@zoll.com', '+351 210 111 200', 'Rua Eng. Ferreira Dias, 80, Lisboa', 'Catarina Lemos', '+351 930 111 200', 'Fabricante de desfibrilhadores e equipamentos de reanimação', 1),
	(12, 'FORN-012', 'MedLine Portugal', 'Consumíveis / Acessórios', '501223344', 'www.medline.pt', 'geral@medline.pt', '+351 220 222 300', 'Rua da Boavista, 200, Porto', 'André Moreira', '+351 940 222 300', 'Fornecedor de consumíveis descartáveis e acessórios hospitalares', 1),
	(13, 'FORN-013', 'Teste', 'Distribuidor / Fornecedor Comercial', '123456789', NULL, 'teste@gmail.com', '+351915678678', 'teste', 'joao', '+351937654321', NULL, 1);

-- A despejar estrutura para tabela db1241375.gestao_conteudos
CREATE TABLE IF NOT EXISTS `gestao_conteudos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo_principal` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `descricao` text COLLATE utf8mb4_bin,
  `texto_botao` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `link_botao` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `imagem_principal` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos: ~0 rows (aproximadamente)
INSERT INTO `gestao_conteudos` (`id`, `titulo_principal`, `descricao`, `texto_botao`, `link_botao`, `imagem_principal`) VALUES
	(1, 'Gestão eficiente de equipamentos médicos', 'A TechMed Solutions ajuda hospitais a gerir o inventário de equipamentos médicos de forma centralizada, segura e eficiente.', 'Conhecer Serviços', '#servicos', '../assets/images/imagem_inicio.png');

-- A despejar estrutura para tabela db1241375.gestao_conteudos_contactos
CREATE TABLE IF NOT EXISTS `gestao_conteudos_contactos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo_secao` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `texto_introdutorio` text COLLATE utf8mb4_bin,
  `titulo_formulario` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `texto_botao` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos_contactos: ~0 rows (aproximadamente)
INSERT INTO `gestao_conteudos_contactos` (`id`, `titulo_secao`, `texto_introdutorio`, `titulo_formulario`, `texto_botao`, `estado`) VALUES
	(1, 'Contactos', 'Entre em contacto connosco para obter mais informações sobre os nossos serviços.', 'Envie-nos uma mensagem', 'Enviar Mensagem', 'Ativo');

-- A despejar estrutura para tabela db1241375.gestao_conteudos_faq
CREATE TABLE IF NOT EXISTS `gestao_conteudos_faq` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pergunta` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `resposta` text COLLATE utf8mb4_bin NOT NULL,
  `ordem_apresentacao` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos_faq: ~4 rows (aproximadamente)
INSERT INTO `gestao_conteudos_faq` (`id`, `pergunta`, `resposta`, `ordem_apresentacao`) VALUES
	(1, 'A quem se destina a plataforma TechMed Solutions?', 'A plataforma destina-se a instituições de saúde que pretendem organizar e gerir equipamentos médicos de forma centralizada, digital e mais eficiente.', 2),
	(2, 'Que tipo de informação pode ser registada?', 'Podem ser registadas informações como identificação do equipamento, categoria, estado, localização, criticidade, documentação associada e histórico relevante.', 1),
	(3, 'A plataforma permite consultar documentos dos equipamentos?', 'Sim. A plataforma prevê a associação de documentos aos equipamentos, como manuais, certificados, contratos, relatórios técnicos e outros ficheiros importantes.', 3),
	(4, 'Quem pode aceder à área reservada?', 'A área reservada destina-se apenas a utilizadores autorizados, como profissionais responsáveis pela gestão e consulta da informação dos equipamentos médicos.', 4);

-- A despejar estrutura para tabela db1241375.gestao_conteudos_rodape
CREATE TABLE IF NOT EXISTS `gestao_conteudos_rodape` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `texto_descritivo` text COLLATE utf8mb4_bin,
  `localizacao` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `horario` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos_rodape: ~0 rows (aproximadamente)
INSERT INTO `gestao_conteudos_rodape` (`id`, `logo`, `texto_descritivo`, `localizacao`, `horario`, `telefone`, `email`) VALUES
	(1, '../assets/images/imagem_logo2.png', 'Plataforma digital para gestão eficiente de equipamentos médicos.', 'Rua Dr. António Bernardino de Almeida, 4249-015 Porto, Portugal', '2ª a 6ª Feira - 09h00 às 18h00', '+351 912 345 678', 'geral@techmedsolutions.pt');

-- A despejar estrutura para tabela db1241375.gestao_conteudos_servicos
CREATE TABLE IF NOT EXISTS `gestao_conteudos_servicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `descricao` text COLLATE utf8mb4_bin NOT NULL,
  `icone` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `ordem_apresentacao` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos_servicos: ~4 rows (aproximadamente)
INSERT INTO `gestao_conteudos_servicos` (`id`, `titulo`, `descricao`, `icone`, `ordem_apresentacao`) VALUES
	(1, 'Gestão de Inventário', 'Registo e organização de equipamentos médicos por categoria, estado, localização e criticidade.', 'fa-solid fa-chart-simple', 1),
	(2, 'Gestão Documental', 'Associação de manuais, certificados, contratos, relatórios técnicos e outros documentos aos equipamentos.', 'fa-solid fa-folder-open', 2),
	(3, 'Dashboard e Consulta', 'Visualização de indicadores, pesquisa e filtragem de equipamentos para apoiar a gestão hospitalar.', 'fa-solid fa-chart-simple', 3);

-- A despejar estrutura para tabela db1241375.gestao_conteudos_sobre
CREATE TABLE IF NOT EXISTS `gestao_conteudos_sobre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo_secao` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `texto_principal` text COLLATE utf8mb4_bin,
  `bloco1_titulo` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `bloco1_texto` text COLLATE utf8mb4_bin,
  `bloco2_titulo` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `bloco2_texto` text COLLATE utf8mb4_bin,
  `bloco3_titulo` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `bloco3_texto` text COLLATE utf8mb4_bin,
  `estado` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.gestao_conteudos_sobre: ~0 rows (aproximadamente)
INSERT INTO `gestao_conteudos_sobre` (`id`, `titulo_secao`, `texto_principal`, `bloco1_titulo`, `bloco1_texto`, `bloco2_titulo`, `bloco2_texto`, `bloco3_titulo`, `bloco3_texto`, `estado`) VALUES
	(1, 'Sobre Nós', 'A TechMed Solutions nasceu com o objetivo de apoiar instituições de saúde na organização e digitalização dos seus processos internos.', 'Missão', 'Apoiar hospitais na transição para processos digitais mais organizados, seguros e eficientes.', 'Inovação', 'Desenvolver soluções tecnológicas simples, intuitivas e adaptadas às necessidades do contexto hospitalar.', 'Impacto', 'Contribuir para uma gestão hospitalar mais eficaz e para melhores condições de apoio aos profissionais de saúde.', 'Ativo');

-- A despejar estrutura para tabela db1241375.historico_equipamentos
CREATE TABLE IF NOT EXISTS `historico_equipamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `acao` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `descricao` text COLLATE utf8mb4_bin,
  `data_acao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utilizador` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `id_localizacao_anterior` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_localizacao_anterior` (`id_localizacao_anterior`),
  CONSTRAINT `historico_equipamentos_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`),
  CONSTRAINT `historico_equipamentos_ibfk_2` FOREIGN KEY (`id_localizacao_anterior`) REFERENCES `localizacoes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.historico_equipamentos: ~59 rows (aproximadamente)
INSERT INTO `historico_equipamentos` (`id`, `id_equipamento`, `acao`, `descricao`, `data_acao`, `utilizador`, `id_localizacao_anterior`) VALUES
	(1, 2, 'Equipamento editado', 'Dados do equipamento atualizados pelo utilizador.', '2026-06-17 17:22:54', 'mariamiguelferreira16@gmail.com', NULL),
	(2, 3, 'Equipamento desativado', 'O equipamento foi desativado pelo utilizador.', '2026-06-17 17:23:12', 'mariamiguelferreira16@gmail.com', NULL),
	(3, 3, 'Equipamento reativado', 'O equipamento foi reativado pelo utilizador.', '2026-06-17 17:25:30', 'mariamiguelferreira16@gmail.com', NULL),
	(4, 9, 'Localização alterada', 'Localização alterada de LOC-003 para LOC-004 devido à desativação da localização original.', '2026-06-17 17:33:15', 'mariamiguelferreira16@gmail.com', 3),
	(5, 9, 'Localização restaurada', 'Equipamento reposto na localização LOC-003 após reativação.', '2026-06-17 17:33:24', 'mariamiguelferreira16@gmail.com', NULL),
	(6, 4, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(7, 6, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(8, 10, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(9, 13, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(10, 14, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(11, 15, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(12, 16, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(13, 21, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(14, 23, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(15, 24, 'Localização alterada', 'Localização alterada de LOC-002 para LOC-001 devido à desativação da localização original.', '2026-06-17 17:33:38', 'mariamiguelferreira16@gmail.com', 2),
	(16, 15, 'Localização restaurada', 'Equipamento reposto na localização LOC-002 após reativação.', '2026-06-17 17:33:50', 'mariamiguelferreira16@gmail.com', NULL),
	(17, 16, 'Localização restaurada', 'Equipamento reposto na localização LOC-002 após reativação.', '2026-06-17 17:33:50', 'mariamiguelferreira16@gmail.com', NULL),
	(18, 21, 'Localização restaurada', 'Equipamento reposto na localização LOC-002 após reativação.', '2026-06-17 17:33:50', 'mariamiguelferreira16@gmail.com', NULL),
	(19, 23, 'Localização restaurada', 'Equipamento reposto na localização LOC-002 após reativação.', '2026-06-17 17:33:50', 'mariamiguelferreira16@gmail.com', NULL),
	(20, 24, 'Localização restaurada', 'Equipamento reposto na localização LOC-002 após reativação.', '2026-06-17 17:33:51', 'mariamiguelferreira16@gmail.com', NULL),
	(21, 2, 'Equipamento editado', 'Dados do equipamento atualizados pelo utilizador.', '2026-06-17 18:30:40', 'mariamiguelferreira16@gmail.com', NULL),
	(22, 1, 'Fornecedor alterado', 'Fornecedor alterado de FORN-001 devido à desativação do fornecedor original.', '2026-06-17 20:27:51', 'mariamiguelferreira16@gmail.com', NULL),
	(23, 6, 'Fornecedor alterado', 'Fornecedor alterado de FORN-001 devido à desativação do fornecedor original.', '2026-06-17 20:27:51', 'mariamiguelferreira16@gmail.com', NULL),
	(24, 10, 'Fornecedor alterado', 'Fornecedor alterado de FORN-001 devido à desativação do fornecedor original.', '2026-06-17 20:27:52', 'mariamiguelferreira16@gmail.com', NULL),
	(25, 13, 'Fornecedor alterado', 'Fornecedor alterado de FORN-001 devido à desativação do fornecedor original.', '2026-06-17 20:27:52', 'mariamiguelferreira16@gmail.com', NULL),
	(26, 1, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(27, 2, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(28, 4, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(29, 6, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(30, 7, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(31, 10, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(32, 13, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(33, 14, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(34, 17, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(35, 21, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:47', 'mariamiguelferreira16@gmail.com', NULL),
	(36, 23, 'Fornecedor alterado', 'Fornecedor alterado de FORN-003 devido à desativação do fornecedor original.', '2026-06-17 20:31:48', 'mariamiguelferreira16@gmail.com', NULL),
	(37, 1, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:01', 'mariamiguelferreira16@gmail.com', NULL),
	(38, 1, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:01', 'mariamiguelferreira16@gmail.com', NULL),
	(39, 2, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:01', 'mariamiguelferreira16@gmail.com', NULL),
	(40, 2, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(41, 2, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(42, 4, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(43, 4, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(44, 4, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(45, 6, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(46, 7, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(47, 7, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(48, 7, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(49, 10, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(50, 13, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(51, 13, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(52, 14, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:02', 'mariamiguelferreira16@gmail.com', NULL),
	(53, 14, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(54, 17, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(55, 17, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(56, 21, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(57, 21, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(58, 23, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(59, 23, 'Fornecedor restaurado', 'Equipamento reposto no fornecedor FORN-003 após reativação.', '2026-06-17 20:32:03', 'mariamiguelferreira16@gmail.com', NULL),
	(60, 4, 'Localização alterada', 'Localização alterada de LOC-001 para LOC-004 devido à desativação da localização original.', '2026-06-20 10:18:08', 'mariamiguelferreira16@gmail.com', 1),
	(61, 6, 'Localização alterada', 'Localização alterada de LOC-001 para LOC-004 devido à desativação da localização original.', '2026-06-20 10:18:08', 'mariamiguelferreira16@gmail.com', 1),
	(62, 10, 'Localização alterada', 'Localização alterada de LOC-001 para LOC-004 devido à desativação da localização original.', '2026-06-20 10:18:08', 'mariamiguelferreira16@gmail.com', 1),
	(63, 13, 'Localização alterada', 'Localização alterada de LOC-001 para LOC-004 devido à desativação da localização original.', '2026-06-20 10:18:08', 'mariamiguelferreira16@gmail.com', 1),
	(64, 14, 'Localização alterada', 'Localização alterada de LOC-001 para LOC-004 devido à desativação da localização original.', '2026-06-20 10:18:08', 'mariamiguelferreira16@gmail.com', 1),
	(65, 5, 'Equipamento desativado', 'O equipamento foi desativado pelo utilizador.', '2026-06-21 20:50:13', 'miguel.ferreira@techmedsolutions.pt', NULL),
	(66, 2, 'Equipamento desativado', 'O equipamento foi desativado pelo utilizador.', '2026-06-21 20:50:27', 'miguel.ferreira@techmedsolutions.pt', NULL),
	(67, 30, 'Equipamento criado', 'Equipamento registado no sistema com o código EQ-0025.', '2026-06-22 17:55:52', 'miguel.ferreira@techmedsolutions.pt', NULL);

-- A despejar estrutura para tabela db1241375.localizacoes
CREATE TABLE IF NOT EXISTS `localizacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_localizacao` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `edificio` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `piso` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `servico_departamento` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `sala_gabinete` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_localizacao` (`codigo_localizacao`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.localizacoes: ~14 rows (aproximadamente)
INSERT INTO `localizacoes` (`id`, `codigo_localizacao`, `edificio`, `piso`, `servico_departamento`, `sala_gabinete`, `ativo`) VALUES
	(1, 'LOC-001', 'Hospital Central', 'Piso 2', 'UCI', 'Sala UCI-A', 0),
	(2, 'LOC-002', 'Hospital Central', 'Piso 1', 'Urgência', 'Sala de Trauma', 1),
	(3, 'LOC-003', 'Hospital Central', 'Piso 1', 'Cardiologia', 'Gabinete C1', 1),
	(4, 'LOC-004', 'Centro de Reabilitação', 'Piso -1', 'Imagiologia', 'Sala de Raio-X', 1),
	(5, 'LOC-005', 'Hospital Central', 'Piso 0', 'Neuropediatria', 'Sala BO-1', 1),
	(6, 'LOC-006', 'Clínica Norte', 'Piso 0', 'Serviço de Medicina', 'Enfermaria M1', 1),
	(7, 'LOC-007', 'Hospital Central', 'Piso 3', 'Laboratório', 'Lab. Análises Clínicas', 1),
	(8, 'LOC-008', 'Centro de Reabilitação', 'Piso 2', 'Fisioterapia', 'Ginásio de Reabilitação', 1),
	(9, 'LOC-009', 'Clínica Norte', 'Piso 1', 'Pediatria', 'Enfermaria P1', 1),
	(10, 'LOC-010', 'Centro de Reabilitação', 'Piso 0', 'Armazém', 'Armazém de Equipamentos', 1),
	(11, 'LOC-011', 'Hospital Central', 'Piso 2', 'Neurologia', 'Gabinete N2', 1),
	(12, 'LOC-012', 'Clínica Norte', 'Piso 2', 'Ortopedia', 'Sala de Tratamentos O1', 1),
	(13, 'LOC-013', 'Clínica Norte', 'Piso 2', 'Ortopedia', 'Sala de Tratamento O1', 1),
	(14, 'LOC-014', 'Clínica Norte', 'Piso 4', 'Ortopedia', 'Sala de Tratamento O1', 1);

-- A despejar estrutura para tabela db1241375.log_acessos
CREATE TABLE IF NOT EXISTS `log_acessos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `tipo` enum('sucesso','falha') COLLATE utf8mb4_bin NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `data_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.log_acessos: ~0 rows (aproximadamente)
INSERT INTO `log_acessos` (`id`, `email`, `tipo`, `descricao`, `ip`, `data_hora`) VALUES
	(1, 'miguel.ferreira@techmedsolutions.pt', 'falha', 'Tentativa de login falhada — email ou password incorretos', '127.0.0.1', '2026-06-21 18:37:35'),
	(2, 'miguel.ferreira@techmedsolutions.pt', 'sucesso', 'Login efetuado com sucesso', '127.0.0.1', '2026-06-21 18:37:51'),
	(3, 'miguel.ferreira@techmedsolutions.pt', 'sucesso', 'Login efetuado com sucesso', '127.0.0.1', '2026-06-21 22:15:34'),
	(4, 'miguel.ferreira@techmedsolutions.pt', 'sucesso', 'Login efetuado com sucesso', '127.0.0.1', '2026-06-22 16:52:08');

-- A despejar estrutura para tabela db1241375.mensagens_contacto
CREATE TABLE IF NOT EXISTS `mensagens_contacto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `assunto` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `mensagem` text COLLATE utf8mb4_bin NOT NULL,
  `data_envio` datetime DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.mensagens_contacto: ~2 rows (aproximadamente)
INSERT INTO `mensagens_contacto` (`id`, `nome`, `email`, `assunto`, `mensagem`, `data_envio`, `lida`) VALUES
	(1, 'teste', 'teste@gmail.com', 'teste', 'teste', '2026-06-20 21:12:35', 1),
	(2, 'teste 2', 'teste2@gmail.com', 'rsre', 'gsgs', '2026-06-20 21:26:09', 1);

-- A despejar estrutura para tabela db1241375.tipos_documento
CREATE TABLE IF NOT EXISTS `tipos_documento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_tipo` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.tipos_documento: ~12 rows (aproximadamente)
INSERT INTO `tipos_documento` (`id`, `nome_tipo`) VALUES
	(1, 'Manual de Utilização'),
	(2, 'Manual Técnico'),
	(3, 'Fatura de Aquisição'),
	(4, 'Contrato de Aquisição'),
	(5, 'Certificado de Garantia'),
	(6, 'Contrato de Manutenção'),
	(7, 'Certificado de Calibração'),
	(8, 'Relatório de Calibração'),
	(9, 'Declaração de Conformidade'),
	(10, 'Relatório Técnico'),
	(11, 'Manual de Serviço'),
	(12, 'Outro');

-- A despejar estrutura para tabela db1241375.utilizadores
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `email` blob,
  `password` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `perfil` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT 'Profissional de Saúde',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241375.utilizadores: ~3 rows (aproximadamente)
INSERT INTO `utilizadores` (`id`, `nome`, `email`, `password`, `perfil`) VALUES
	(1, 'Miguel Ferreira', _binary 0xa28fc773d119085fdb9a3ed92db82d1de01f8b726e94d9de4f98dbb179f80911ff49d2e0d5eddbd63a722f5fe3a1b5e1, '$2y$10$lQqowB1dMT/3yDQ6lGk5XuFfkW5.n3j5LPBIVDmlwICtftXXf1R3u', 'Administrador'),
	(2, 'João Silva', _binary 0x62325095121ccbfc03d3ce3d750fc8a9476ba99b1a78524928253bbe647f78ee, '$2y$10$D1eG5fFCPGJqMlKOzjhqsONbZ2Qz3PTdltu1PywfhKC1epNpgCn/a', 'Técnico'),
	(3, 'Ana Costa', _binary 0xfab1aafc36ec963e454e09814c5a17b3ca8bcf3d2d1f1bde823f6fed6685b7ef, '$2y$10$ytPT0Rc2P9uLjkqxq0WlcecW0TFvh1EJlsqPkzYQdUmNrIxQZqPdG', 'Profissional de Saúde');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

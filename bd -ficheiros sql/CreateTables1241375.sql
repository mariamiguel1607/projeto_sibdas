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

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.estados
CREATE TABLE IF NOT EXISTS `estados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_estado` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_estado` (`nome_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.gestao_conteudos_faq
CREATE TABLE IF NOT EXISTS `gestao_conteudos_faq` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pergunta` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `resposta` text COLLATE utf8mb4_bin NOT NULL,
  `ordem_apresentacao` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.gestao_conteudos_servicos
CREATE TABLE IF NOT EXISTS `gestao_conteudos_servicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `descricao` text COLLATE utf8mb4_bin NOT NULL,
  `icone` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `ordem_apresentacao` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

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

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.tipos_documento
CREATE TABLE IF NOT EXISTS `tipos_documento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_tipo` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1241375.utilizadores
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `email` blob,
  `password` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `perfil` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT 'Profissional de Saúde',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

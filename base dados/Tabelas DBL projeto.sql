CREATE DATABASE IF NOT EXISTS `projeto_sibdas`;
USE `projeto_sibdas`;

CREATE TABLE `utilizadores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL
);

CREATE TABLE `localizacoes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_localizacao` varchar(50) UNIQUE NOT NULL,
  `edificio` varchar(50) NOT NULL,
  `piso` varchar(20) NOT NULL,
  `servico_departamento` varchar(100) NOT NULL,
  `sala_gabinete` varchar(50)
);

CREATE TABLE `categorias` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_categoria` varchar(100) NOT NULL
);

CREATE TABLE `estados` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_estado` varchar(50) UNIQUE NOT NULL
);

CREATE TABLE `tipos_documento` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_tipo` varchar(100) NOT NULL
);

CREATE TABLE `fornecedores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_fornecedor` varchar(50) UNIQUE NOT NULL,
  `nome_empresa` varchar(150) NOT NULL,
  `tipo_fornecedor` varchar(50) NOT NULL,
  `estado_atividade` varchar(20) NOT NULL,
  `nif` varchar(9) UNIQUE NOT NULL,
  `website` varchar(255),
  `email` varchar(100),
  `telefone` varchar(20),
  `morada` varchar(255),
  `pessoa_contacto` varchar(100),
  `telefone_contacto` varchar(20),
  `observacoes` text
);

CREATE TABLE `equipamentos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_interno` varchar(50) UNIQUE NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `fabricante` varchar(100),
  `marca` varchar(100),
  `modelo` varchar(100),
  `num_serie` varchar(100),
  `ano_fabrico` year,
  `data_aquisicao` date,
  `custo_aquisicao` decimal(10,2),
  `tipo_entrada` varchar(50),
  `criticidade` varchar(50) NOT NULL,
  `observacoes` text,
  `id_localizacao` int,
  `id_categoria` int,
  `id_estado` int
);

CREATE TABLE `equipamentos_fornecedores` (
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `tipo_relacao` varchar(50) NOT NULL,
  PRIMARY KEY (`id_equipamento`, `id_fornecedor`)
);

CREATE TABLE `acessorios` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `quantidade` int,
  `id_estado` int,
  `id_equipamento` int NOT NULL
);

CREATE TABLE `consumiveis` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `quantidade` int,
  `stock_minimo` int,
  `id_equipamento` int NOT NULL
);

CREATE TABLE `documentacao` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_documento` varchar(50) UNIQUE NOT NULL,
  `nome_documento` varchar(150) NOT NULL,
  `data_documento` date,
  `data_validade` date,
  `estado` varchar(20),
  `caminho_ficheiro` varchar(255),
  `observacoes` text,
  `id_tipo_documento` int NOT NULL,
  `id_equipamento` int NOT NULL
);

CREATE TABLE `contratos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tipo_contrato` varchar(100) NOT NULL,
  `periodicidade` varchar(50),
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `observacoes` text,
  `id_fornecedor` int,
  `id_documento` int NOT NULL
);

CREATE TABLE `gestao_conteudos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `titulo_principal` varchar(255) NOT NULL,
  `descricao` text,
  `texto_botao` varchar(100),
  `link_botao` varchar(255),
  `imagem_principal` varchar(255)
);

CREATE TABLE `gestao_conteudos_servicos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `icone` varchar(100),
  `estado` varchar(20) NOT NULL,
  `ordem_apresentacao` int
);

CREATE TABLE `gestao_conteudos_sobre` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `titulo_secao` varchar(255),
  `texto_principal` text,
  `bloco1_titulo` varchar(100),
  `bloco1_texto` text,
  `bloco2_titulo` varchar(100),
  `bloco2_texto` text,
  `bloco3_titulo` varchar(100),
  `bloco3_texto` text,
  `estado` varchar(20)
);

CREATE TABLE `gestao_conteudos_contactos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `titulo_secao` varchar(255) NOT NULL,
  `texto_introdutorio` text,
  `titulo_formulario` varchar(255),
  `texto_botao` varchar(100),
  `estado` varchar(20) NOT NULL
);

CREATE TABLE `gestao_conteudos_faq` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `pergunta` varchar(255) NOT NULL,
  `resposta` text NOT NULL,
  `ordem_apresentacao` int
);

CREATE TABLE `gestao_conteudos_rodape` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `logo` varchar(255),
  `texto_descritivo` text,
  `localizacao` varchar(255),
  `horario` varchar(255),
  `telefone` varchar(20),
  `email` varchar(100)
);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id`) ON DELETE SET NULL;

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id`);

ALTER TABLE `equipamentos_fornecedores` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE;

ALTER TABLE `equipamentos_fornecedores` ADD FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id`) ON DELETE CASCADE;

ALTER TABLE `acessorios` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE;

ALTER TABLE `acessorios` ADD FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id`) ON DELETE SET NULL;

ALTER TABLE `consumiveis` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE;

ALTER TABLE `documentacao` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE;

ALTER TABLE `documentacao` ADD FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id`);

ALTER TABLE `contratos` ADD FOREIGN KEY (`id_documento`) REFERENCES `documentacao` (`id`) ON DELETE CASCADE;

ALTER TABLE `contratos` ADD FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL;

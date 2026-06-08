-- Categorias
INSERT INTO `categorias` (`nome_categoria`) VALUES ('Monitorização');
INSERT INTO `categorias` (`nome_categoria`) VALUES ('Diagnóstico');
INSERT INTO `categorias` (`nome_categoria`) VALUES ('Terapia');
INSERT INTO `categorias` (`nome_categoria`) VALUES ('Suporte de Vida');

-- Estados
INSERT INTO `estados` (`nome_estado`) VALUES ('Ativo');
INSERT INTO `estados` (`nome_estado`) VALUES ('Inativo');
INSERT INTO `estados` (`nome_estado`) VALUES ('Em manutenção');
INSERT INTO `estados` (`nome_estado`) VALUES ('Em calibração');

-- Tipos de Documento
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Manual de Utilização');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Manual Técnico');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Fatura de Aquisição');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Contrato de Aquisição');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Certificado de Garantia');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Contrato de Manutenção');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Certificado de Calibração');
INSERT INTO `tipos_documento` (`nome_tipo`) VALUES ('Relatório de Calibração');
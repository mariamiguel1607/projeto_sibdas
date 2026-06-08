ALTER TABLE `fornecedores` 
MODIFY `pessoa_contacto` varchar(100) NOT NULL,
MODIFY `telefone_contacto` varchar(20) NOT NULL,
MODIFY `email` varchar(100) NOT NULL;

ALTER TABLE `equipamentos`
MODIFY `fabricante` varchar(100) NOT NULL,
MODIFY `marca` varchar(100) NOT NULL,
MODIFY `modelo` varchar(100) NOT NULL,
MODIFY `data_aquisicao` date NOT NULL,
MODIFY `tipo_entrada` varchar(50) NOT NULL,
MODIFY `id_categoria` int NOT NULL,
MODIFY `id_estado` int NOT NULL;

ALTER TABLE `contratos`
MODIFY `periodicidade` varchar(50) NOT NULL;

ALTER TABLE `consumiveis`
MODIFY `quantidade` int NOT NULL;

ALTER TABLE `acessorios`
MODIFY `quantidade` int NOT NULL,
MODIFY `id_estado` int NOT NULL;

ALTER TABLE `consumiveis`
DROP COLUMN `stock_minimo`;

ALTER TABLE `gestao_conteudos_servicos`
DROP COLUMN `estado`;
CREATE DATABASE `vendas`;
USE `vendas`;
CREATE TABLE `Produto` (
  `Codigo_Produto` int PRIMARY KEY,
  `Descricao_Produto` varchar(100),
  `Preco` decimal(10,2)
);
CREATE TABLE `Nota_fiscal` (
  `Numero_NF` int PRIMARY KEY,
  `Data_NF` date,
  `Valor_NF` decimal(10,2)
);
CREATE TABLE `Itens` (
  `Codigo_Produto` int,
  `Numero_NF` int,
  `Quantidade` int,
  PRIMARY KEY (`Codigo_Produto`,`Numero_NF`),
  FOREIGN KEY (`Codigo_Produto`) REFERENCES `Produto` (`Codigo_Produto`),
  FOREIGN KEY (`Numero_NF`) REFERENCES `Nota_fiscal` (`Numero_NF`)
);
ALTER TABLE `Produto`
  MODIFY COLUMN `Descricao_Produto` varchar(50);
ALTER TABLE `Nota_fiscal`
  ADD `CMS` float AFTER `Numero_NF`;
ALTER TABLE `Produto`
  ADD `Peso` float;
ALTER TABLE `Itens`
  DROP PRIMARY KEY;
ALTER TABLE `Itens`
  ADD `Num_item` int PRIMARY KEY;
DESCRIBE `Produto`;
DESCRIBE `Nota_fiscal`;
ALTER TABLE `Nota_fiscal`
  CHANGE `Valor_NF` `ValorTotal_NF` decimal(10,2);
ALTER TABLE `Nota_fiscal`
  DROP COLUMN `Data_NF`;
DROP TABLE `Itens`;
ALTER TABLE `Nota_fiscal`
  RENAME TO `Venda`;
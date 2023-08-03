--
-- Table structure for table products
--
DROP TABLE IF EXISTS `products`;
CREATE TABLE products (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `baseCost` int(11) NOT NULL,
  `includedKwh` int(11),
  `additionalKwhCost` int(11) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE products
  ADD PRIMARY KEY (`id`);


ALTER TABLE products
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
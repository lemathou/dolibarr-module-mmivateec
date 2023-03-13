CREATE TABLE IF NOT EXISTS `llx_societe_vat_number_check` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ctime` timestamp NOT NULL DEFAULT current_timestamp(),
  `fk_soc` int(11) DEFAULT NULL,
  `object_type` enum('propal','commande','facture') DEFAULT NULL,
  `fk_object` int(11) DEFAULT NULL,
  `country_code` varchar(2) NOT NULL,
  `vat_number` varchar(16) NOT NULL,
  `valid` tinyint(1) NOT NULL,
  `response_name` varchar(64) NOT NULL,
  `response_address` varchar(255) NOT NULL,
  `request_id` varchar(32) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `fk_soc` (`fk_soc`),
  KEY `fk_object` (`object_type`,`fk_soc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

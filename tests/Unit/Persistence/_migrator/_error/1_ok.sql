CREATE TABLE `migrator_test_error` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `migrator_test_error` (`title`) VALUES ('test1');
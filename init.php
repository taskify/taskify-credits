<?php
// * Copyright 2012,2013 Melvin Carvalho and other contributors; Licensed LGPLv3

require_once 'db/db.php';

class ExternalConnection extends ConnectionSettings {
    public function __construct() {
        $this->setHost( 'localhost' );
        $this->setUser( 'root' );
        $this->setPassword( '' );
        $this->setDatabase( 'taskify' );
    }
}

Database::getInstance(new ExternalConnection());

$webcredits_sql = 'CREATE TABLE IF NOT EXISTS `webcredits` (
  `root` varchar(255) DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `referrer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

Database::getInstance()->query($webcredits_sql);


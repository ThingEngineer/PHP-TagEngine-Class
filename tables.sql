#
# Encoding: Unicode (UTF-8)
#


CREATE TABLE `sysTagMaps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relId` int(11) NOT NULL DEFAULT '0',
  `typeId` int(11) NOT NULL DEFAULT '0',
  `tagId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `relId` (`relId`),
  KEY `tagId` (`tagId`),
  KEY `typeId` (`typeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `sysTags` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `sysTagTypes` (
  `typeId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(3) NOT NULL DEFAULT '',
  `longName` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`typeId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS = 0;


LOCK TABLES `sysTagTypes` WRITE;
INSERT INTO `sysTagTypes` (`typeId`, `name`, `longName`) VALUES (1, 'pst', 'Post');
INSERT INTO `sysTagTypes` (`typeId`, `name`, `longName`) VALUES (2, 'img', 'Image');
INSERT INTO `sysTagTypes` (`typeId`, `name`, `longName`) VALUES (3, 'doc', 'Document');
UNLOCK TABLES;


SET FOREIGN_KEY_CHECKS = 1;

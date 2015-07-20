SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` char(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `currentIP` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NULL,
  `currentToken` char(23) CHARACTER SET utf8 COLLATE utf8_bin NULL,
  `currentHeroID` int(10) unsigned NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `login` (`login`),
  KEY `current` (`currentToken`, `currentIP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `hero`;
CREATE TABLE `hero` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL,
  `name` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `modules` VARCHAR(255),
  `lastNode` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `character`;
CREATE TABLE `character` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `name` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `family` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `fromNode` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `toNode` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `quest`;
CREATE TABLE `quest` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `task` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `state` tinyint(3) NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `item` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `number` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `squad`;
CREATE TABLE `squad` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `characterID` int(10) unsigned NOT NULL,
  `troop` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `number` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `node_objects`;
CREATE TABLE `node_objects` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heroID` int(10) unsigned NOT NULL,
  `node` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `object` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `heroID` (`heroID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

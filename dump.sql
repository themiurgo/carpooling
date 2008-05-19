-- MySQL dump 10.11
--
-- Host: localhost    Database: Carpooling
-- ------------------------------------------------------
-- Server version	5.0.45-community-nt

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auto`
--

DROP TABLE IF EXISTS `auto`;
CREATE TABLE `auto` (
  `ID` int(11) NOT NULL auto_increment,
  `targa` char(15) NOT NULL,
  `marca` varchar(15) NOT NULL,
  `modello` varchar(15) NOT NULL,
  `cilindrata` int(11) NOT NULL,
  `annoImmatr` int(4) NOT NULL,
  `condizioni` int(1) NOT NULL,
  `note` varchar(200) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `targa` (`targa`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auto`
--

LOCK TABLES `auto` WRITE;
/*!40000 ALTER TABLE `auto` DISABLE KEYS */;
INSERT INTO `auto` VALUES (1,'WF345PY','Audi','A3',1990,2000,3,NULL),(2,'AB321FI','Alfa Romeo','159',1990,2000,4,NULL),(3,'ID458TV','Mercedes','A 180 Classic',1990,2000,5,NULL),(4,'MN432HC','Bmw','118',1990,2000,4,NULL),(5,'RD341OX','Citroen','C3',1990,2000,3,NULL),(6,'ER456YU','Fiat','500',2000,1951,1,'Gomme nuove');
/*!40000 ALTER TABLE `auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autoutenti`
--

DROP TABLE IF EXISTS `autoutenti`;
CREATE TABLE `autoutenti` (
  `idAuto` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `valido` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idAuto`,`idUtente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `autoutenti`
--

LOCK TABLES `autoutenti` WRITE;
/*!40000 ALTER TABLE `autoutenti` DISABLE KEYS */;
INSERT INTO `autoutenti` VALUES (1,1,1),(2,2,1),(3,3,1),(4,4,1),(5,5,1),(6,8,1);
/*!40000 ALTER TABLE `autoutenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `autore` int(11) NOT NULL,
  `tragittoAut` int(11) NOT NULL,
  `valutato` int(11) NOT NULL,
  `tragittoVal` int(11) NOT NULL,
  `valutazione` int(1) NOT NULL,
  `data` datetime NOT NULL,
  `note` varchar(50) default NULL,
  PRIMARY KEY  (`autore`,`tragittoAut`,`valutato`,`tragittoVal`),
  KEY `valutato` (`valutato`,`tragittoVal`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`autore`, `tragittoAut`) REFERENCES `utentitragitto` (`idUtente`, `idTragitto`),
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`valutato`, `tragittoVal`) REFERENCES `utentitragitto` (`idUtente`, `idTragitto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,1,6,1,2,'2008-03-28 11:00:00','Molesto'),(2,2,7,2,4,'2008-03-28 11:00:00','Signorile'),(3,3,8,3,1,'2008-03-28 11:00:00','Pessimo soggetto'),(3,3,9,3,2,'2008-05-19 20:48:52','Ha dormito tutto il tempo'),(6,1,1,1,3,'2008-03-28 11:00:00','Simpatico personaggio'),(7,2,2,2,5,'2008-03-28 11:00:00','Ottima e simpatica persona'),(8,3,3,3,4,'2008-05-19 20:47:40','Simpatico'),(8,3,9,3,3,'2008-05-19 20:47:52','Discreto'),(8,3,10,3,1,'2008-05-19 20:48:04','Fastidioso'),(10,3,3,3,4,'2008-05-19 20:50:08','Ottimo autista'),(10,3,8,3,2,'2008-05-19 20:51:21','Stupido');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `feedbackpossibili`
--

DROP TABLE IF EXISTS `feedbackpossibili`;
/*!50001 DROP VIEW IF EXISTS `feedbackpossibili`*/;
/*!50001 CREATE TABLE `feedbackpossibili` (
  `autore` int(11),
  `tragittoAut` int(11),
  `valutato` int(11),
  `tragittoVal` int(11)
) */;

--
-- Table structure for table `tragitto`
--

DROP TABLE IF EXISTS `tragitto`;
CREATE TABLE `tragitto` (
  `ID` int(11) NOT NULL auto_increment,
  `idPropr` int(11) NOT NULL,
  `idAuto` int(11) NOT NULL,
  `partenza` varchar(20) NOT NULL,
  `destinaz` varchar(20) NOT NULL,
  `dataPart` date NOT NULL,
  `oraPart` time NOT NULL,
  `durata` time NOT NULL,
  `fumo` tinyint(1) NOT NULL,
  `musica` tinyint(1) NOT NULL,
  `spese` decimal(6,2) unsigned default '0.00',
  `postiDisp` int(1) NOT NULL,
  `note` varchar(200) default NULL,
  `bloccato` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tragitto`
--

LOCK TABLES `tragitto` WRITE;
/*!40000 ALTER TABLE `tragitto` DISABLE KEYS */;
INSERT INTO `tragitto` VALUES (1,1,1,'Catania','Palermo','2008-05-18','08:00:00','01:30:00',0,0,'0.00',3,NULL,0),(2,2,2,'Catania','Palermo','2008-05-18','09:00:00','01:30:00',0,0,'0.00',4,NULL,0),(3,3,3,'Catania','Palermo','2008-05-18','07:00:00','02:00:00',0,0,'0.00',5,NULL,0),(4,4,4,'Catania','Palermo','2008-05-20','08:30:00','01:30:00',0,0,'0.00',4,NULL,0),(5,5,5,'Catania','Palermo','2008-05-20','08:00:00','02:00:00',0,0,'0.00',4,NULL,0),(6,1,1,'Catania','Messina','2008-05-20','20:00:00','02:00:00',0,0,'0.00',4,NULL,1),(7,2,2,'Catania','Messina','2008-05-19','12:00:00','03:00:00',0,0,'0.00',3,NULL,0),(8,3,3,'Catania','Messina','2008-05-19','15:00:00','03:00:00',0,0,'0.00',3,NULL,0),(9,4,4,'Catania','Messina','2008-05-19','12:00:00','02:00:00',0,0,'0.00',3,NULL,0);
/*!40000 ALTER TABLE `tragitto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utenti`
--

DROP TABLE IF EXISTS `utenti`;
CREATE TABLE `utenti` (
  `ID` int(11) NOT NULL auto_increment,
  `userName` varchar(20) default NULL,
  `psw` varchar(32) NOT NULL,
  `nome` varchar(20) NOT NULL,
  `cognome` varchar(20) NOT NULL,
  `sesso` enum('f','m') NOT NULL,
  `dataNascita` date NOT NULL,
  `email` varchar(40) NOT NULL,
  `dataPatente` date NOT NULL,
  `fumatore` tinyint(1) NOT NULL,
  `dataIscriz` date NOT NULL,
  `localita` varchar(20) NOT NULL,
  `idAutoPref` int(11) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `userName` (`userName`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `utenti`
--

LOCK TABLES `utenti` WRITE;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;
INSERT INTO `utenti` VALUES (1,'ari','c7cc6a1fd6d6b5f4817025cb532b52fa','Alfio','Rinaldi','m','1977-12-15','a.rinaldi@tin.it','1990-12-15',0,'2006-11-15','Catania',NULL),(2,'gica','f2293aa6431ff49aa481e7acaea71116','Giuseppa','Cantone','f','1980-10-10','g.canto@tin.it','1998-12-15',0,'2006-11-16','Catania',NULL),(3,'caccio','3cd56ca40c0be39daad5c1398df013f9','Filippo','Cacciola','m','1975-07-02','filcacciola@libero.it','1985-12-01',1,'2006-11-17','Catania',NULL),(4,'frano','244fe8bdf0f58277b7b55e5b555deefe','Francesco','Nocita','m','1960-02-02','fr.nocita@gmail.com','1990-03-04',0,'2006-11-18','Catania',NULL),(5,'ape','efa04884995f5b73b9f64e8688ff3e42','Angela','Perna','f','1962-05-06','aperna@hotmail.it','1990-07-12',0,'2006-11-19','Catania',NULL),(6,'rosi','f4bf8ba00cb902edaa901b006dfec4aa','Rosalia','Fichera','f','1973-01-06','firosa@hotmail.com','1995-03-05',1,'2006-11-19','Messina',NULL),(7,'coca','79f84228831211f0fd89ab9101bf05bd','Concetto','Calabro','m','1970-05-06','cocala@tin.it','1990-11-10',1,'2006-11-19','Messina',NULL),(8,'seby','d5b6cc3c2cf280ac3b95a8b3876d1c18','Sebastiano','Accetta','m','1971-05-06','sebya@yahoo.it','1992-05-08',1,'2006-11-19','Messina',NULL),(9,'giuta','f1e58f2b2576943808d59acaa24eb935','Giuseppa','Taccetta','f','1972-05-06','gtaccetta@hotmail.it','1993-09-08',1,'2006-11-19','Messina',NULL),(10,'vica','0ed93e154eff79aee1cba0a2476284a8','Vincenzo','Castorina','m','1975-05-06','castorov@gmail.com','1994-01-09',0,'2006-11-19','Messina',NULL);
/*!40000 ALTER TABLE `utenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utentitragitto`
--

DROP TABLE IF EXISTS `utentitragitto`;
CREATE TABLE `utentitragitto` (
  `idUtente` int(11) NOT NULL,
  `idTragitto` int(11) NOT NULL,
  PRIMARY KEY  (`idUtente`,`idTragitto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `utentitragitto`
--

LOCK TABLES `utentitragitto` WRITE;
/*!40000 ALTER TABLE `utentitragitto` DISABLE KEYS */;
INSERT INTO `utentitragitto` VALUES (1,1),(1,6),(2,2),(2,7),(3,3),(3,8),(4,4),(4,9),(5,5),(6,1),(7,2),(8,3),(8,5),(9,3),(10,3);
/*!40000 ALTER TABLE `utentitragitto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `feedbackpossibili`
--

/*!50001 DROP TABLE IF EXISTS `feedbackpossibili`*/;
/*!50001 DROP VIEW IF EXISTS `feedbackpossibili`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `feedbackpossibili` AS select `ut1`.`idUtente` AS `autore`,`ut1`.`idTragitto` AS `tragittoAut`,`ut2`.`idUtente` AS `valutato`,`ut2`.`idTragitto` AS `tragittoVal` from (`utentitragitto` `ut1` join `utentitragitto` `ut2`) where ((`ut1`.`idTragitto` = `ut2`.`idTragitto`) and (`ut1`.`idUtente` <> `ut2`.`idUtente`)) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-05-19 18:55:48

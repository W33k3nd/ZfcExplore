-- MySQL dump 10.16  Distrib 10.1.20-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: localhost
-- ------------------------------------------------------
-- Server version	10.1.20-MariaDB

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
-- Table structure for table `ansprechpartner`
--

DROP TABLE IF EXISTS `ansprechpartner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ansprechpartner` (
  `knr_id` bigint(20) unsigned NOT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `nachname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `idgfi` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`knr_id`),
  KEY `IDX_130315FA5A844226` (`geschlecht`),
  CONSTRAINT `FK_130315FA5A844226` FOREIGN KEY (`geschlecht`) REFERENCES `geschlecht` (`gen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `benutzer`
--

DROP TABLE IF EXISTS `benutzer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benutzer` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `knr` int(11) DEFAULT NULL,
  `role` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` int(11) DEFAULT '0',
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `UNIQ_36144FC7E7927C74` (`email`),
  KEY `IDX_36144FC7FEB6ABEA` (`knr`),
  KEY `IDX_36144FC757698A6A` (`role`),
  CONSTRAINT `FK_36144FC757698A6A` FOREIGN KEY (`role`) REFERENCES `role` (`roleid`),
  CONSTRAINT `FK_36144FC7FEB6ABEA` FOREIGN KEY (`knr`) REFERENCES `kammer` (`knr`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debitor`
--

DROP TABLE IF EXISTS `debitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firma` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bemerkung` longtext COLLATE utf8_unicode_ci,
  `strasse` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hausnummer` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plz` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ort` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6492 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dozent`
--

DROP TABLE IF EXISTS `dozent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dozent` (
  `knr_id` bigint(20) unsigned NOT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `nachname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`knr_id`),
  KEY `IDX_DF4DB64C5A844226` (`geschlecht`),
  CONSTRAINT `FK_DF4DB64C5A844226` FOREIGN KEY (`geschlecht`) REFERENCES `geschlecht` (`gen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geschlecht`
--

DROP TABLE IF EXISTS `geschlecht`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geschlecht` (
  `gen` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `bez` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `anrede` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`gen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kammer`
--

DROP TABLE IF EXISTS `kammer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kammer` (
  `knr` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL,
  `titel` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `subdomain` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `web` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `strasse` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hausnummer` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plz` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ort` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`knr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kategorie`
--

DROP TABLE IF EXISTS `kategorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategorie` (
  `knr_id` bigint(20) unsigned NOT NULL,
  `order` int(11) NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontaktdaten`
--

DROP TABLE IF EXISTS `kontaktdaten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontaktdaten` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ansprechpartner` bigint(20) unsigned DEFAULT NULL,
  `kontaktart` smallint(6) NOT NULL,
  `kontakt` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kontakt` (`ansprechpartner`,`kontaktart`),
  KEY `IDX_884B6FBA130315FA` (`ansprechpartner`),
  CONSTRAINT `FK_884B6FBA130315FA` FOREIGN KEY (`ansprechpartner`) REFERENCES `ansprechpartner` (`knr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `land`
--

DROP TABLE IF EXISTS `land`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `land` (
  `knr_id` int(10) unsigned NOT NULL,
  `aktiv` tinyint(1) NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `knr` int(11) DEFAULT NULL,
  `time` datetime NOT NULL,
  `priority` int(11) NOT NULL,
  `priorityName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `info` longtext COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ort`
--

DROP TABLE IF EXISTS `ort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ort` (
  `knr_id` varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `idgfi` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `roleid` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `parent` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`roleid`),
  KEY `IDX_57698A6A3D8E604F` (`parent`),
  CONSTRAINT `FK_57698A6A3D8E604F` FOREIGN KEY (`parent`) REFERENCES `role` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar`
--

DROP TABLE IF EXISTS `seminar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seminar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kennung` bigint(20) unsigned NOT NULL,
  `gruppe` varchar(33) COLLATE utf8_unicode_ci NOT NULL,
  `ort` varchar(23) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ansprechpartner` bigint(20) unsigned DEFAULT NULL,
  `dozent` bigint(20) unsigned DEFAULT NULL,
  `art` smallint(6) NOT NULL,
  `startdatum` date NOT NULL,
  `endedatum` date DEFAULT NULL,
  `anzeigedatum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `anzeigezeit` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `wochentag` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `preis` decimal(10,2) NOT NULL,
  `lehrmittel` tinyint(1) NOT NULL,
  `einheiten` smallint(6) NOT NULL,
  `preisinfo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `infoabend` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BFFD2C88CB8D08FD` (`kennung`),
  KEY `IDX_BFFD2C883DD6F931` (`gruppe`),
  KEY `IDX_BFFD2C88F6ABFB5E` (`ort`),
  KEY `IDX_BFFD2C88130315FA` (`ansprechpartner`),
  KEY `IDX_BFFD2C88DF4DB64C` (`dozent`),
  CONSTRAINT `FK_BFFD2C88130315FA` FOREIGN KEY (`ansprechpartner`) REFERENCES `ansprechpartner` (`knr_id`),
  CONSTRAINT `FK_BFFD2C883DD6F931` FOREIGN KEY (`gruppe`) REFERENCES `seminargruppe` (`knr_id`),
  CONSTRAINT `FK_BFFD2C88CB8D08FD` FOREIGN KEY (`kennung`) REFERENCES `seminarkennung` (`knr_id`),
  CONSTRAINT `FK_BFFD2C88DF4DB64C` FOREIGN KEY (`dozent`) REFERENCES `dozent` (`knr_id`),
  CONSTRAINT `FK_BFFD2C88F6ABFB5E` FOREIGN KEY (`ort`) REFERENCES `ort` (`knr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2068 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_kategorie`
--

DROP TABLE IF EXISTS `seminar_kategorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seminar_kategorie` (
  `seminarid` bigint(20) unsigned NOT NULL,
  `kategorieid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`seminarid`,`kategorieid`),
  KEY `IDX_9C7DD3A17EBBA60D` (`seminarid`),
  KEY `IDX_9C7DD3A1D1604B7E` (`kategorieid`),
  CONSTRAINT `FK_9C7DD3A17EBBA60D` FOREIGN KEY (`seminarid`) REFERENCES `seminarkennung` (`knr_id`),
  CONSTRAINT `FK_9C7DD3A1D1604B7E` FOREIGN KEY (`kategorieid`) REFERENCES `kategorie` (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminargruppe`
--

DROP TABLE IF EXISTS `seminargruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seminargruppe` (
  `knr_id` varchar(33) COLLATE utf8_unicode_ci NOT NULL,
  `ziel` longtext COLLATE utf8_unicode_ci NOT NULL,
  `inhalt` longtext COLLATE utf8_unicode_ci NOT NULL,
  `allgemein` longtext COLLATE utf8_unicode_ci NOT NULL,
  `gebuehr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminarkennung`
--

DROP TABLE IF EXISTS `seminarkennung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seminarkennung` (
  `knr_id` bigint(20) unsigned NOT NULL,
  `kennzeichen` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `bez1` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bez2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`knr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teilnehmer`
--

DROP TABLE IF EXISTS `teilnehmer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teilnehmer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seminar` bigint(20) unsigned NOT NULL,
  `debitor` int(11) DEFAULT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `geburtsland` int(10) unsigned DEFAULT NULL,
  `status` smallint(5) unsigned NOT NULL COMMENT '\n	 0x*2 = generiert,\n	 0x*1 = anschreiben',
  `anmeldezeit` datetime NOT NULL,
  `nachname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `geburtsdatum` date NOT NULL,
  `geburtsort` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bemerkung` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `strasse` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hausnummer` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plz` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ort` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F25E8A04BFFD2C88` (`seminar`),
  KEY `IDX_F25E8A0410E61231` (`debitor`),
  KEY `IDX_F25E8A045A844226` (`geschlecht`),
  KEY `IDX_F25E8A046FF1D5BB` (`geburtsland`),
  CONSTRAINT `FK_F25E8A0410E61231` FOREIGN KEY (`debitor`) REFERENCES `debitor` (`id`),
  CONSTRAINT `FK_F25E8A045A844226` FOREIGN KEY (`geschlecht`) REFERENCES `geschlecht` (`gen`),
  CONSTRAINT `FK_F25E8A046FF1D5BB` FOREIGN KEY (`geburtsland`) REFERENCES `land` (`knr_id`),
  CONSTRAINT `FK_F25E8A04BFFD2C88` FOREIGN KEY (`seminar`) REFERENCES `seminarkennung` (`knr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11188 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02 12:56:14

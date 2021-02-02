-- MySQL dump 10.13  Distrib 5.5.62, for Linux (x86_64)
--
-- Host: localhost    Database: traffic
-- ------------------------------------------------------
-- Server version	5.5.62-log

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
-- Table structure for table `Offences`
--

DROP TABLE IF EXISTS `Offences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Offences` (
  `Offence_id` int(11) NOT NULL AUTO_INCREMENT,
  `Offence_description` varchar(100) NOT NULL,
  `Offence_fine` double NOT NULL,
  `Offence_point` int(11) NOT NULL,
  PRIMARY KEY (`Offence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Offences`
--

LOCK TABLES `Offences` WRITE;
/*!40000 ALTER TABLE `Offences` DISABLE KEYS */;
INSERT INTO `Offences` VALUES (1,'drive after taking a drink',2000,12),(2,'Speeding',1000,6);
/*!40000 ALTER TABLE `Offences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Person`
--

DROP TABLE IF EXISTS `Person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Person` (
  `Person_id` int(11) NOT NULL AUTO_INCREMENT,
  `Person_name` varchar(100) NOT NULL,
  `Person_address` varchar(100) NOT NULL,
  `Person_birth` varchar(100) NOT NULL,
  `Person_license` varchar(16) NOT NULL,
  `Person_points` int(11) NOT NULL,
  `Person_add_time` varchar(45) NOT NULL,
  PRIMARY KEY (`Person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Person`
--

LOCK TABLES `Person` WRITE;
/*!40000 ALTER TABLE `Person` DISABLE KEYS */;
INSERT INTO `Person` VALUES (2,'Jennifer Allen','46 Bramcote Drive, Nottingham','19920111','ALLEN88K23KLR9B3',10,'2021-01-25-18-05-47'),(3,'John Myers','323 Derby Road, Nottingham','19550601','MYERS99JDW8REWL3',6,'2021-01-25-18-06-29'),(4,'James Smith','26 Devonshire Avenue, Nottingham','19400322','SMITHR004JFS20TR',4,'2021-01-25-18-07-36'),(5,'Terry Brown','7 Clarke Rd, Nottingham','19800723','BROWND3PJJ39DLFG',9,'2021-01-25-18-08-01'),(6,'Mary Adams','38 Thurman St, Nottingham','19880504','ADAMSH9O3JRHH107',4,'2021-01-25-18-09-32'),(7,'Neil Becker','6 Fairfax Close, Nottingham','19951224','BECKE88UPR840F9R',12,'2021-01-25-18-10-08'),(8,'Angela Smith','30 Avenue Road, Grantham','19980317','SMITH222LE9FJ5DS',10,'2021-01-25-18-10-35');
/*!40000 ALTER TABLE `Person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reports`
--

DROP TABLE IF EXISTS `Reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reports` (
  `Report_id` int(11) NOT NULL AUTO_INCREMENT,
  `Report_description` varchar(1000) NOT NULL,
  `Police_id` int(11) NOT NULL,
  `Finalfine` double NOT NULL,
  `Submit_time` varchar(100) NOT NULL,
  PRIMARY KEY (`Report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reports`
--

LOCK TABLES `Reports` WRITE;
/*!40000 ALTER TABLE `Reports` DISABLE KEYS */;
INSERT INTO `Reports` VALUES (12,'speeding',3,8,'20191126 111158');
/*!40000 ALTER TABLE `Reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reports_Offence`
--

DROP TABLE IF EXISTS `Reports_Offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reports_Offence` (
  `Reports_Offence_id` int(11) NOT NULL AUTO_INCREMENT,
  `Offence_id` int(11) NOT NULL,
  `Report_id` int(11) NOT NULL,
  PRIMARY KEY (`Reports_Offence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reports_Offence`
--

LOCK TABLES `Reports_Offence` WRITE;
/*!40000 ALTER TABLE `Reports_Offence` DISABLE KEYS */;
INSERT INTO `Reports_Offence` VALUES (8,2,12);
/*!40000 ALTER TABLE `Reports_Offence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reports_Person`
--

DROP TABLE IF EXISTS `Reports_Person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reports_Person` (
  `Reports_Person_id` int(11) NOT NULL AUTO_INCREMENT,
  `Person_id` int(11) NOT NULL,
  `Report_id` int(11) NOT NULL,
  PRIMARY KEY (`Reports_Person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reports_Person`
--

LOCK TABLES `Reports_Person` WRITE;
/*!40000 ALTER TABLE `Reports_Person` DISABLE KEYS */;
INSERT INTO `Reports_Person` VALUES (9,7,12),(10,6,12);
/*!40000 ALTER TABLE `Reports_Person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reports_Vehicles`
--

DROP TABLE IF EXISTS `Reports_Vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reports_Vehicles` (
  `Reports_Vehicles_id` int(11) NOT NULL AUTO_INCREMENT,
  `Vehicle_id` varchar(100) NOT NULL,
  `Report_id` int(11) NOT NULL,
  PRIMARY KEY (`Reports_Vehicles_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reports_Vehicles`
--

LOCK TABLES `Reports_Vehicles` WRITE;
/*!40000 ALTER TABLE `Reports_Vehicles` DISABLE KEYS */;
INSERT INTO `Reports_Vehicles` VALUES (17,'NY64KWD',12),(18,'DJ14SLE',12);
/*!40000 ALTER TABLE `Reports_Vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Role`
--

DROP TABLE IF EXISTS `Role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Role` (
  `Role_id` int(11) NOT NULL AUTO_INCREMENT,
  `Role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`Role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Role`
--

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;
INSERT INTO `Role` VALUES (1,'admin'),(2,'user');
/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `User_id` int(11) NOT NULL AUTO_INCREMENT,
  `User_roleid` int(11) NOT NULL,
  `User_username` varchar(100) NOT NULL,
  `User_password` varchar(100) NOT NULL,
  `User_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`User_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,2,'Carter','fuzz42','Carter'),(2,2,'Regan','plod123','Regan'),(3,1,'haskins','coper99','haskins');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Vehicles`
--

DROP TABLE IF EXISTS `Vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Vehicles` (
  `Vehicle_id` varchar(100) NOT NULL,
  `Vehicle_make` varchar(100) NOT NULL,
  `Vehicle_model` varchar(100) NOT NULL,
  `Vehicle_color` varchar(100) NOT NULL,
  `Person_id` int(11) DEFAULT NULL,
  `Vehicle_add_time` varchar(45) NOT NULL,
  PRIMARY KEY (`Vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Vehicles`
--

LOCK TABLES `Vehicles` WRITE;
/*!40000 ALTER TABLE `Vehicles` DISABLE KEYS */;
INSERT INTO `Vehicles` VALUES ('BC16OEA','Renault','Scenic','Silver',8,'2021-01-25-18-13-09'),('DJ14SLE','Ford','Focus','White',-1,'2021-01-25-18-12-36'),('FD65WPQ','Vauxhall','Astra','Silver',3,'2021-01-25-18-11-22'),('FJ17AUG','Honda','Civic','Green',9,'2021-01-25-18-11-45'),('FP16KKE','Toyota','Prius','Silver',-1,'2021-01-25-18-12-03'),('FP66KLM','Ford','Mondeo','Black',-1,'2021-01-25-18-12-22'),('LB15AJL','Ford','Fiesta','Blue',-1,'2021-01-25-18-04-00'),('MY64PRE','Ferrari','458','Red',-1,'2021-01-25-18-11-08'),('NY64KWD','Nissan','Pulsar','Red',4,'2021-01-25-18-12-49');
/*!40000 ALTER TABLE `Vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-01-26 13:10:07

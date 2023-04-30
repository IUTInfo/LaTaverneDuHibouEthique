--
-- Table structure for table `beer`
--

DROP TABLE IF EXISTS `beer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beer` (
                        `beerid` int NOT NULL,
                        `name` varchar(45) NOT NULL,
                        `type` varchar(45) NOT NULL,
                        `alcohol` int NOT NULL,
                        `price` int NOT NULL,
                        `mark` int NOT NULL,
                        `description` text,
                        `imagepath` varchar(128) DEFAULT NULL,
                        `stock` int DEFAULT '0',
                        PRIMARY KEY (`beerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beer`
--

LOCK TABLES `beer` WRITE;
/*!40000 ALTER TABLE `beer` DISABLE KEYS */;
INSERT INTO `beer` VALUES (1,'Kill Elves','pale ale',13,35,5,'La description','Le path',30),(2,'Woman\'s beard','stout',7,30,3,NULL,NULL,0),(3,'Axes','pils',6,28,4,NULL,NULL,15),(4,'Break the rocks','IPA',8,42,1,NULL,NULL,0),(5,'Triple Mithril juice','pils',11,53,3,NULL,NULL,0),(6,'Tor Durin','IPA',8,25,1,NULL,NULL,0);
/*!40000 ALTER TABLE `beer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_beer`
--

DROP TABLE IF EXISTS `order_beer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_beer` (
  `orderid` int NOT NULL,
  `beerid` int NOT NULL,
  `amount` int NOT NULL,
  PRIMARY KEY (`orderid`,`beerid`),
  KEY `FK_ob_beer_idx` (`beerid`),
  CONSTRAINT `FK_ob_beer` FOREIGN KEY (`beerid`) REFERENCES `beer` (`beerid`),
  CONSTRAINT `FK_ob_order` FOREIGN KEY (`orderid`) REFERENCES `orders` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_beer`
--

LOCK TABLES `order_beer` WRITE;
/*!40000 ALTER TABLE `order_beer` DISABLE KEYS */;
INSERT INTO `order_beer` VALUES (1,1,2),(2,1,8),(2,2,15),(2,4,40),(3,3,75),(4,1,10),(4,3,5),(5,1,10),(5,3,5);
/*!40000 ALTER TABLE `order_beer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `orderid` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `pigeonnumber` varchar(45) NOT NULL,
  `address` varchar(128) NOT NULL,
  PRIMARY KEY (`orderid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'eeee','fffff','12','aaaa'),(2,'Emilien','Girolet','1245','8 rue des Ouches'),(3,'Marina','Carbone','12345','Rue de la motte'),(4,'_','marinaaaa','4','motte'),(5,'_','sdfsdfs','12','sdfsdf');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'css117'
--

--
-- Dumping routines for database 'css117'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-30  4:38:25

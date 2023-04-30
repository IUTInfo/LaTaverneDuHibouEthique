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
INSERT INTO `beer` VALUES (1,'Kill Elves','pale ale',13,35,5,'Une bière qui vous fera sentir libre ! Débarrassons-nous de ces créatures puantes que sont les elfes ! Les saveurs vont vous faire perdre vos esprits et faire des choses. Mais les elfes ne sont pas réellement des êtres importants donc ce n’est pas très grave. Une bière brune symbole de guerre entre nos peuples. J’ai eu un ami Elfe un jour il était très sympa… Non je déconne bien évidement, aucun elfe ne mérite notre regard. Signez la pétition en description contre les elfes.','/img/b12.png',24),(2,'Woman\'s beard','stout',7,30,3,'Cette boisson est aussi envouteuse que vos propres femmes ! Notre bière est une ale rousse de fermentation haute, légèrement pétillante et facile à boire pour tous les nains qui aiment passer du bon temps entre amis. Le nom de notre produit vient d’une légende naine selon laquelle les femmes sont capables de cultiver des barbes aussi épaisses que celles des hommes. La « Woman’s Beard » est servie dans des tonneaux en bois, ajoutant ainsi une touche d’authenticité et de tradition à votre expérience de la boisson. Evidement elle ne comblera jamais le vide intense que vous pouvez ressentir mais au moins que serez trop éclaté pour ressentir quoi que ce soit.','/img/b2.png',30),(3,'Axes','pils',6,28,4,'Une bière blonde légère et rafraîchissante, parfaitement adaptée aux longues soirées passées à refaire le monde avec vos amis nains. Elle tire son nom des haches et des outils que les nains utilisent pour creuser les montagnes, symbolisant leur force, leur ténacité et leur détermination. Cette bière est l’incarnation de l’esprit de camaraderie et de la convivialité que pourrais potentiellement ressentir les nains. Si bien sûr les nains ressentent des émotions évidement. Buvez-en et vous grandirez !','/img/b3.png',12),(4,'Break the rocks','IPA',8,42,1,'Comment faire un breuvage plus emblématique que celui-ci ? Son pouvoir de destruction vient des montagnes les plus grandes et plus lointaine de la terre du milieu. C’est une bière brune de fermentation haute, avec une teneur en alcool modérée, qui rappelle les profondeurs des montagnes que les nains appellent leur maison (mdr). La recette spéciale utilise des ingrédients de qualité supérieure pour créer un goût robuste et malté, avec des notes de chocolat, de café et de cendres. L’histoire des nains minant résonne dans la boisson. Rappelons-nous de tous ces nains ayant péris en ramassant des cailloux… Pierrick, Rodrigo et Antoine, vous resterez des héros dans nos mémoires !','/img/b4.png',0),(5,'Triple Mithril juice','pils',11,53,3,'Cette boisson vient directement des entrailles de la terre ! Sa composition est riche, mais surtout très précieuse. Nous vous proposons une bière blonde à fermentation triple, avec une teneur en alcool élevée qui lui confère une puissance et une robustesse digne des plus grands guerriers nains. Le Mithril, ces puissants métaux des montagnes est symbole de puissance et prestige. Prenez-en et vous vous sentirez très fort. Mais surtout vous vous sentirez prestigieux ivre mort sur le contoir de votre taverne.','/img/b5.png',18),(6,'Tor Durin','IPA',8,25,1,'La légende raconte que ce breuvage mène vers les portes de l’enfer… Seul un nain suffisamment courageux pourrait l’affronter ! Cette tour de bière est elle-même originelle de notre peuple. Son pouvoir et sa recette se transmet de génération en génération depuis des millénaires. Sa composition aussi vielle que notre ère, promet un mélange de saveur et de piment. Seriez-vous prêt à tenter l’expérience ? Où vous reculer devant le combat telle un lâche ? Evidement le produit est en rupture de stock… ça doit être embêtant pour prouver ces preuves ça… C’est la fin de l’abondance ! En tout cas pour ma part j’ai quelques tonneaux chez moi donc je ne risque rien.','/img/b5.png',0);
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
INSERT INTO `order_beer` VALUES (1,1,2),(2,1,8),(2,2,15),(2,4,40),(3,3,75),(4,1,10),(4,3,5),(5,1,10),(5,3,5),(6,1,6),(7,3,3),(7,5,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'eeee','fffff','12','aaaa'),(2,'Emilien','Girolet','1245','8 rue des Ouches'),(3,'Marina','Carbone','12345','Rue de la motte'),(4,'_','marinaaaa','4','motte'),(5,'_','sdfsdfs','12','sdfsdf'),(6,'_','rfdgfd','585','dfgfdgdf'),(7,'_','marinananananan','9999','kosdkofgdkofgd');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'css117'
--

-- Dump completed on 2023-04-30  7:55:13

-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: library
-- ------------------------------------------------------
-- Server version	10.10.2-MariaDB-1:10.10.2+maria~ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lended_to_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `rating` double DEFAULT NULL,
  `image` longblob DEFAULT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CBE5A3315592F884` (`lended_to_id`),
  KEY `IDX_CBE5A3317E3C61F9` (`owner_id`),
  CONSTRAINT `FK_CBE5A3315592F884` FOREIGN KEY (`lended_to_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_CBE5A3317E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (37,NULL,12,'Make It Stop','Jim Ruland','http://books.google.com/books/content?id=RIw3zwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A speculative tale of dysfunctional vigilantes, sex-crazed junkies, and corporate healthcare run amok from best-selling chronicler of LA punk Jim Ruland. Scores of detox and rehab centers across Southern California have adopted a controversial new co',1,NULL,NULL,'9781644283035'),(38,NULL,12,'The Wager','David Grann','http://books.google.com/books/content?id=GEGNEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','From the #1 New York Times bestselling author of Killers of the Flower Moon, a page-turning story of shipwreck, survival, and savagery, culminating in a court martial that reveals a shocking truth. With the twists and turns of a thriller Grann uneart',0,NULL,NULL,'9780385534260'),(39,NULL,12,'The Creative Act','Rick Rubin','http://books.google.com/books/content?id=l3dtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','The #1 New York Times bestseller. From the legendary music producer, a master at helping people connect with the wellsprings of their creativity, comes a beautifully crafted book many years in the making that offers that same deep wisdom to all of us',0,NULL,NULL,'9780593652886'),(40,NULL,12,'Pirate Enlightenment, Or the Real Libertalia','David Graeber','http://books.google.com/books/content?id=UGNzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','The final posthumous work by the coauthor of the major New York Times bestseller The Dawn of Everything. Pirates have long lived in the realm of romance and fantasy, symbolizing risk, lawlessness, and radical visions of freedom. But at the root of th',0,NULL,NULL,'9780374610197'),(41,NULL,12,'Hummingbird Salamander','Jeff VanderMeer','http://books.google.com/books/content?id=Nhs7EAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','From the author of Annihilation, a brilliant speculative thriller of dark conspiracy, endangered species, and the possible end of all things. The security consultant \"Jane Smith\" receives an envelope with a key to a storage unit that holds a taxiderm',0,NULL,NULL,'9781250829771'),(42,NULL,12,'Dievų miškas','Balys Sruoga','6459a7a8b9f40.jpg','Dievų miškas – memuarinė knyga, parašyta 1945 m. lietuvių rašytojo ir poeto Balio Sruogos (1896–1947).',0,NULL,NULL,'4415187418741'),(43,NULL,13,'The Deluge','Stephen Markley','http://books.google.com/books/content?id=aJKhEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','In 2013 California, environmental scientist Tony Pietrus, after receiving a death threat, is linked to a colorful cast of characters, including a brazen young activist who, in the mountains of Wyoming, begins a project that will alter the course of t',1,2.3333333333333,NULL,'9781982123093'),(44,NULL,13,'Babel','R. F. Kuang','http://books.google.com/books/content?id=rkO-zgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','From award-winning author R. F. Kuang comes Babel, a thematic response to The Secret History and a tonal retort to Jonathan Strange & Mr. Norrell that grapples with student revolutions, colonial resistance, and the use of language and translation as ',0,NULL,NULL,'9780063021426'),(45,NULL,13,'Poverty, by America','Matthew Desmond','http://books.google.com/books/content?id=Ly2OEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','The Pulitzer Prize–winning, bestselling author of Evicted reimagines the debate on poverty, making a new and bracing argument about why it persists in America: because the rest of us benefit from it. The United States, the richest country on earth,',0,NULL,NULL,'9780593239919'),(46,NULL,13,'Hummingbird Salamander','Jeff VanderMeer','http://books.google.com/books/content?id=Nhs7EAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','From the author of Annihilation, a brilliant speculative thriller of dark conspiracy, endangered species, and the possible end of all things. The security consultant \"Jane Smith\" receives an envelope with a key to a storage unit that holds a taxiderm',0,NULL,NULL,'9781250829771');
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_history`
--

DROP TABLE IF EXISTS `book_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `performed_by_id` int(11) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `action` int(11) NOT NULL,
  `is_request` tinyint(1) NOT NULL,
  `currently_active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B49A58DD16A2B381` (`book_id`),
  KEY `IDX_B49A58DD2E65C292` (`performed_by_id`),
  CONSTRAINT `FK_B49A58DD16A2B381` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`),
  CONSTRAINT `FK_B49A58DD2E65C292` FOREIGN KEY (`performed_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_history`
--

LOCK TABLES `book_history` WRITE;
/*!40000 ALTER TABLE `book_history` DISABLE KEYS */;
INSERT INTO `book_history` VALUES (78,37,12,'2023-05-09 03:43:07am',1,0,NULL),(79,38,12,'2023-05-09 03:44:11am',1,0,NULL),(80,39,12,'2023-05-09 03:44:34am',1,0,NULL),(81,40,12,'2023-05-09 03:45:04am',1,0,NULL),(82,41,12,'2023-05-09 03:47:38am',1,0,NULL),(83,42,12,'2023-05-09 03:53:44am',1,0,NULL),(84,43,13,'2023-05-09 03:55:00am',1,0,NULL),(85,44,13,'2023-05-09 03:55:20am',1,0,NULL),(86,45,13,'2023-05-09 03:55:42am',1,0,NULL),(87,46,13,'2023-05-09 03:55:59am',1,0,NULL),(88,43,12,'2023-05-09 04:05:08am',2,0,NULL),(89,43,13,'2023-05-09 04:07:25am',3,0,NULL),(90,43,12,'2023-05-09 04:09:55am',5,0,NULL),(91,43,13,'2023-05-09 04:13:13am',6,0,NULL);
/*!40000 ALTER TABLE `book_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_request`
--

DROP TABLE IF EXISTS `book_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `request_date` varchar(255) NOT NULL,
  `return_date` varchar(255) NOT NULL,
  `is_active` int(11) NOT NULL,
  `is_lent` int(11) NOT NULL,
  `requested_by_id` int(11) NOT NULL,
  `is_return` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A8B7A70916A2B381` (`book_id`),
  KEY `IDX_A8B7A7094DA1E751` (`requested_by_id`),
  CONSTRAINT `FK_A8B7A70916A2B381` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`),
  CONSTRAINT `FK_A8B7A7094DA1E751` FOREIGN KEY (`requested_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_request`
--

LOCK TABLES `book_request` WRITE;
/*!40000 ALTER TABLE `book_request` DISABLE KEYS */;
INSERT INTO `book_request` VALUES (19,43,'2023-05-09 04:05:08am','2023-05-30T21:00:00.000Z',0,0,12,0);
/*!40000 ALTER TABLE `book_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_review`
--

DROP TABLE IF EXISTS `book_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `review` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `reviewed_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_50948A4B16A2B381` (`book_id`),
  CONSTRAINT `FK_50948A4B16A2B381` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_review`
--

LOCK TABLES `book_review` WRITE;
/*!40000 ALTER TABLE `book_review` DISABLE KEYS */;
INSERT INTO `book_review` VALUES (15,43,'Gera knyga, rekomenduoju',5,12),(16,43,'Antra karta skaitant nelabai patiko',1,12),(17,43,'knyga nepatiko',1,12);
/*!40000 ALTER TABLE `book_review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20230330173046','2023-03-30 19:31:31',149),('DoctrineMigrations\\Version20230412180239','2023-04-12 20:04:00',170),('DoctrineMigrations\\Version20230413141935','2023-04-13 16:19:52',112),('DoctrineMigrations\\Version20230413220018','2023-04-14 00:00:24',63),('DoctrineMigrations\\Version20230414131946','2023-04-14 15:19:55',104),('DoctrineMigrations\\Version20230416131432','2023-04-16 15:14:46',69),('DoctrineMigrations\\Version20230416143913','2023-04-16 19:09:19',4),('DoctrineMigrations\\Version20230416170821','2023-04-16 19:09:19',84),('DoctrineMigrations\\Version20230418221525','2023-04-19 00:15:35',165),('DoctrineMigrations\\Version20230419221720','2023-04-20 00:17:30',65),('DoctrineMigrations\\Version20230419224612','2023-04-20 00:46:18',28),('DoctrineMigrations\\Version20230422113851','2023-04-22 13:39:03',82),('DoctrineMigrations\\Version20230422130130','2023-04-22 15:03:32',24),('DoctrineMigrations\\Version20230422215649','2023-04-22 23:57:08',145),('DoctrineMigrations\\Version20230423145150','2023-04-23 16:51:57',65),('DoctrineMigrations\\Version20230424185939','2023-04-24 21:00:06',100),('DoctrineMigrations\\Version20230427201518','2023-04-27 22:15:29',85),('DoctrineMigrations\\Version20230427204707','2023-04-27 22:47:28',39),('DoctrineMigrations\\Version20230427205148','2023-04-27 22:52:03',24),('DoctrineMigrations\\Version20230428143705','2023-04-28 16:37:29',368),('DoctrineMigrations\\Version20230429131918','2023-04-29 15:21:43',90);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `main_book_image`
--

DROP TABLE IF EXISTS `main_book_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `main_book_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `main_book_image`
--

LOCK TABLES `main_book_image` WRITE;
/*!40000 ALTER TABLE `main_book_image` DISABLE KEYS */;
INSERT INTO `main_book_image` VALUES (9,'9781644283035','http://books.google.com/books/content?id=RIw3zwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Make It Stop','Jim Ruland'),(10,'9780385534260','http://books.google.com/books/content?id=GEGNEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','The Wager','David Grann'),(11,'9780593652886','http://books.google.com/books/content?id=l3dtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','The Creative Act','Rick Rubin'),(12,'9780374610197','http://books.google.com/books/content?id=UGNzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Pirate Enlightenment, Or the Real Libertalia','David Graeber'),(13,'9781250829771','http://books.google.com/books/content?id=Nhs7EAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Hummingbird Salamander','Jeff VanderMeer'),(14,'4415187418741','6459a7a8b9f40.jpg','Dievų miškas','Balys Sruoga'),(15,'9781982123093','http://books.google.com/books/content?id=aJKhEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','The Deluge','Stephen Markley'),(16,'9780063021426','http://books.google.com/books/content?id=rkO-zgEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Babel','R. F. Kuang'),(17,'9780593239919','http://books.google.com/books/content?id=Ly2OEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Poverty, by America','Matthew Desmond');
/*!40000 ALTER TABLE `main_book_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_report`
--

DROP TABLE IF EXISTS `return_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `returned_by_id` int(11) NOT NULL,
  `report` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `request_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_80F2ED5B427EB8A5` (`request_id`),
  KEY `IDX_80F2ED5BA76ED395` (`user_id`),
  KEY `IDX_80F2ED5B71AD87D9` (`returned_by_id`),
  CONSTRAINT `FK_80F2ED5B427EB8A5` FOREIGN KEY (`request_id`) REFERENCES `book_request` (`id`),
  CONSTRAINT `FK_80F2ED5B71AD87D9` FOREIGN KEY (`returned_by_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_80F2ED5BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_report`
--

LOCK TABLES `return_report` WRITE;
/*!40000 ALTER TABLE `return_report` DISABLE KEYS */;
INSERT INTO `return_report` VALUES (3,12,13,'negrazino knygos','2023-05-08',19);
/*!40000 ALTER TABLE `return_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `rating` double DEFAULT NULL,
  `number` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `other_contacts` varchar(255) DEFAULT NULL,
  `is_active` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (12,'adomej99','[\"ROLE_ADMIN\"]','$2y$13$Lxjfg2H.vbIUUzUJ6.UZiO8pczWBilZHSAQNi9e6X6sSo92YWZvM2','adomej99@gmail.com',5,'+37064534999','Kauno r.','Vilkija','Fb: Adomas Mejaras',1),(13,'mejarasadomas','[\"ROLE_USER\"]','$2y$13$jH3zCpe4P/BKK2LYeXLkB.qK6OK1j2rbzOOuVBN/h1V6mu3qkO9y.','mejarasadomas@gmail.com',3,'+37064534599','Kauno r.','Kaunas','Discord: Adomas#3333',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_review`
--

DROP TABLE IF EXISTS `user_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `review` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `reviewed_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1C119AFBA76ED395` (`user_id`),
  KEY `IDX_1C119AFBFC6B21F1` (`reviewed_by_id`),
  CONSTRAINT `FK_1C119AFBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1C119AFBFC6B21F1` FOREIGN KEY (`reviewed_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_review`
--

LOCK TABLES `user_review` WRITE;
/*!40000 ALTER TABLE `user_review` DISABLE KEYS */;
INSERT INTO `user_review` VALUES (23,13,'Geras skolintojas',5,12),(24,13,'skolintojas nepatiko',1,12),(25,12,'puikus skolininkas',5,13);
/*!40000 ALTER TABLE `user_review` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-09  5:33:59

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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (4,NULL,10,'The Big Reveal','Sasha Velour','http://books.google.com/books/content?id=LmRIzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','\"Drag embodies the queer possibility that exists within each of us--the infinite ways in which gender, good taste, and art can be lived.\" -Sasha Velour This book is a quilt, piecing together memoir, history, and theory into a living portrait of an ar',0,1,NULL,NULL),(5,NULL,10,'Breakup','Anjan Sundaram','http://books.google.com/books/content?id=vLKQEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Award-winning journalist Anjan Sundaram, hailed as “the Indian successor to Kapuscinski” (Basharat Peer) and praised for “remarkable” (Jon Stewart), “excellent” (Fareed Zakaria), and “courageous and heartfelt” (The Washington Post) wo',0,1,NULL,NULL),(6,NULL,10,'Yours Truly','Abby Jimenez','http://books.google.com/books/content?id=2URTzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A novel of terrible first impressions, hilarious second chances, and the joy in finding your perfect match from \"a true talent\" (Emily Henry, #1 New York Times bestselling author). Dr. Briana Ortiz\'s life is seriously flatlining. Her divorce is just ',0,NULL,NULL,NULL),(7,NULL,10,'Your Super Life','Michael Kuech, Kristel de Groot','http://books.google.com/books/content?id=GVk6zwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','With a foreword by Dr. William Li, New York Times bestselling author of Eat to Beat Disease From the founders of the popular superfood brand, Your Super, comes a beautiful cookbook designed to supercharge health and healing with a customizable plant-',0,NULL,NULL,NULL),(8,NULL,10,'You Could Make This Place Beautiful','Maggie Smith','http://books.google.com/books/content?id=A7CzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','“[Smith]...reminds you that you can...survive deep loss, sink into life’s deep beauty, and constantly, constantly make yourself new.” —Glennon Doyle, #1 New York Times bestselling author The bestselling poet and author of the “powerful” (',0,4,NULL,NULL),(9,NULL,10,'You Could Make This Place Beautiful','Maggie Smith','http://books.google.com/books/content?id=A7CzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','“[Smith]...reminds you that you can...survive deep loss, sink into life’s deep beauty, and constantly, constantly make yourself new.” —Glennon Doyle, #1 New York Times bestselling author The bestselling poet and author of the “powerful” (',0,NULL,NULL,NULL),(10,NULL,10,'You Could Make This Place Beautiful','Maggie Smith','http://books.google.com/books/content?id=A7CzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','“[Smith]...reminds you that you can...survive deep loss, sink into life’s deep beauty, and constantly, constantly make yourself new.” —Glennon Doyle, #1 New York Times bestselling author The bestselling poet and author of the “powerful” (',0,NULL,NULL,NULL),(11,NULL,10,'You Could Make This Place Beautiful','Maggie Smith','http://books.google.com/books/content?id=A7CzEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api','“[Smith]...reminds you that you can...survive deep loss, sink into life’s deep beauty, and constantly, constantly make yourself new.” —Glennon Doyle, #1 New York Times bestselling author The bestselling poet and author of the “powerful” (',0,NULL,NULL,NULL),(15,NULL,12,'Safe and Sound','Mercury Stardust','http://books.google.com/books/content?id=2Tt5zwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Don\'t panic--I\'m here to help! Dear Readers',0,NULL,NULL,NULL),(18,NULL,10,'Hot Dog','Doug Salati','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','NEW YORK TIMES BESTSELLER • WINNER OF THE 2023 CALDECOTT MEDAL • This glowing and playful picture book features an overheated—and overwhelmed—pup who finds his calm with some sea, sand, and fresh air. Destined to become a classic! NAMED ONE O',1,4,NULL,'9780593308431'),(19,NULL,10,'Hot Dog','Doug Salati','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','NEW YORK TIMES BESTSELLER • WINNER OF THE 2023 CALDECOTT MEDAL • This glowing and playful picture book features an overheated—and overwhelmed—pup who finds his calm with some sea, sand, and fresh air. Destined to become a classic! NAMED ONE O',1,NULL,NULL,'9780593308431'),(20,NULL,10,'Hot Dog','Doug Salati','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','NEW YORK TIMES BESTSELLER • WINNER OF THE 2023 CALDECOTT MEDAL • This glowing and playful picture book features an overheated—and overwhelmed—pup who finds his calm with some sea, sand, and fresh air. Destined to become a classic! NAMED ONE O',1,NULL,NULL,NULL),(21,NULL,10,'Hot Dog','Doug Salati','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','NEW YORK TIMES BESTSELLER • WINNER OF THE 2023 CALDECOTT MEDAL • This glowing and playful picture book features an overheated—and overwhelmed—pup who finds his calm with some sea, sand, and fresh air. Destined to become a classic! NAMED ONE O',1,NULL,NULL,'9780593308431'),(22,NULL,12,'Rosewater','Liv Little','http://books.google.com/books/content?id=i_lGzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A TODAY and LGBTQ Reads Most Anticipated Book of 2023 - A Goodreads Buzziest Debut Novel of the New Year - An Electric Lit Most Anticipated LGBTQ+ Book of Spring 2023 For fans of Queenie and Such a Fun Age comes a deliciously gritty and strikingly bo',0,NULL,NULL,NULL),(23,NULL,12,'Rosewater','Liv Little','http://books.google.com/books/content?id=i_lGzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A TODAY and LGBTQ Reads Most Anticipated Book of 2023 - A Goodreads Buzziest Debut Novel of the New Year - An Electric Lit Most Anticipated LGBTQ+ Book of Spring 2023 For fans of Queenie and Such a Fun Age comes a deliciously gritty and strikingly bo',0,NULL,NULL,NULL),(24,NULL,12,'Rosewater','Liv Little','http://books.google.com/books/content?id=i_lGzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A TODAY and LGBTQ Reads Most Anticipated Book of 2023 - A Goodreads Buzziest Debut Novel of the New Year - An Electric Lit Most Anticipated LGBTQ+ Book of Spring 2023 For fans of Queenie and Such a Fun Age comes a deliciously gritty and strikingly bo',0,NULL,NULL,NULL),(25,NULL,12,'Hot Dog','Doug Salati','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','NEW YORK TIMES BESTSELLER • WINNER OF THE 2023 CALDECOTT MEDAL • This glowing and playful picture book features an overheated—and overwhelmed—pup who finds his calm with some sea, sand, and fresh air. Destined to become a classic! NAMED ONE O',1,NULL,NULL,'9780593308431'),(26,12,12,'I Will Teach You to Be Rich: The Journal','Ramit Sethi','http://books.google.com/books/content?id=B-BCEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','A guided journal from the bestselling author of I Will Teach You to Be Rich, with inspiring questions and thought-provoking exercises to help you understand your own money behavior and create your vision of a Rich Life.',0,NULL,NULL,'9781523516872'),(30,NULL,12,'sdfsdfsdf','dfsdfsdf','64517be96443f.jpg','sdfsdfsdfsdfd',0,NULL,NULL,'2545545154');
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_history`
--

LOCK TABLES `book_history` WRITE;
/*!40000 ALTER TABLE `book_history` DISABLE KEYS */;
INSERT INTO `book_history` VALUES (1,4,10,'2023-04-13 04:30:34pm',1,0,NULL),(2,5,10,'2023-04-14 12:18:51am',1,0,NULL),(3,6,10,'2023-04-14 06:04:12pm',1,0,NULL),(4,4,10,'2023-04-16 02:18:19pm',2,0,NULL),(5,7,10,'2023-04-16 03:37:52pm',1,0,NULL),(6,4,10,'2023-04-16 04:23:24pm',2,0,NULL),(7,4,10,'2023-04-16 04:44:17pm',2,0,NULL),(8,4,10,'2023-04-16 06:49:26pm',3,0,NULL),(9,5,10,'2023-04-16 07:00:42pm',2,0,NULL),(10,5,10,'2023-04-16 07:31:23pm',2,0,NULL),(11,5,10,'2023-04-16 07:34:59pm',3,0,NULL),(12,5,10,'2023-04-17 12:26:32am',5,0,NULL),(13,4,10,'2023-04-17 10:04:32pm',2,0,NULL),(14,4,10,'2023-04-17 10:05:22pm',3,0,NULL),(15,4,10,'2023-04-18 11:58:10pm',5,0,NULL),(16,4,10,'2023-04-19 12:00:47am',2,0,NULL),(17,4,10,'2023-04-19 12:01:31am',3,0,NULL),(18,4,10,'2023-04-19 12:47:35am',5,0,NULL),(19,5,10,'2023-04-19 11:22:11pm',6,0,NULL),(20,4,10,'2023-04-19 11:23:17pm',6,0,NULL),(21,4,10,'2023-04-20 12:39:35am',6,0,NULL),(22,4,10,'2023-04-20 12:40:00am',2,0,NULL),(23,4,10,'2023-04-20 12:40:59am',3,0,NULL),(24,5,10,'2023-04-20 12:41:57am',5,0,NULL),(25,5,10,'2023-04-20 12:42:58am',6,0,NULL),(26,5,10,'2023-04-20 12:44:25am',2,0,NULL),(27,5,10,'2023-04-20 12:44:48am',3,0,NULL),(28,5,10,'2023-04-20 12:49:30am',5,0,NULL),(29,4,10,'2023-04-20 12:50:09am',5,0,NULL),(30,5,10,'2023-04-20 12:51:01am',6,0,NULL),(31,5,10,'2023-04-20 12:55:25am',5,0,NULL),(32,5,10,'2023-04-20 12:56:00am',6,0,NULL),(33,5,10,'2023-04-20 01:01:07am',5,0,NULL),(34,4,10,'2023-04-20 01:01:38am',5,0,NULL),(35,5,10,'2023-04-20 01:02:08am',5,0,NULL),(36,4,10,'2023-04-20 01:03:36am',5,0,NULL),(37,8,10,'2023-04-21 04:51:02pm',1,0,NULL),(38,9,10,'2023-04-21 04:51:05pm',1,0,NULL),(39,10,10,'2023-04-21 04:51:08pm',1,0,NULL),(40,8,10,'2023-04-21 04:52:07pm',2,0,NULL),(41,5,10,'2023-04-21 04:53:49pm',2,0,NULL),(42,8,10,'2023-04-21 04:55:36pm',3,0,NULL),(43,8,10,'2023-04-21 04:56:22pm',5,0,NULL),(44,5,10,'2023-04-21 04:57:32pm',6,0,NULL),(45,5,10,'2023-04-22 01:38:10pm',2,0,NULL),(46,11,10,'2023-04-23 12:30:49pm',1,0,NULL),(47,5,10,'2023-04-23 12:31:18pm',2,0,NULL),(48,4,10,'2023-04-23 01:13:31pm',7,0,NULL),(49,15,12,'2023-04-25 11:45:44pm',1,0,NULL),(50,15,10,'2023-04-26 08:55:45pm',2,0,NULL),(52,18,12,'2023-04-27 10:47:52pm',1,0,NULL),(53,19,12,'2023-04-27 10:48:14pm',1,0,NULL),(54,20,12,'2023-04-27 10:52:18pm',1,0,NULL),(55,21,12,'2023-04-27 10:52:39pm',1,0,NULL),(56,22,12,'2023-04-27 10:53:26pm',1,0,NULL),(57,23,12,'2023-04-27 10:58:41pm',1,0,NULL),(58,24,12,'2023-04-27 11:00:36pm',1,0,NULL),(59,25,12,'2023-04-28 04:37:46pm',1,0,NULL),(60,5,10,'2023-04-28 05:30:50pm',3,0,NULL),(61,26,10,'2023-04-28 07:33:26pm',1,0,NULL),(62,26,12,'2023-04-28 07:46:20pm',2,0,NULL),(63,26,10,'2023-04-28 07:46:49pm',3,0,NULL),(64,18,10,'2023-04-28 10:00:07pm',2,0,NULL),(65,18,10,'2023-04-28 10:42:26pm',2,0,NULL),(66,18,12,'2023-05-01 03:07:02pm',5,0,NULL),(67,18,10,'2023-05-01 03:56:17pm',6,0,NULL),(68,18,12,'2023-05-02 08:54:46pm',5,0,NULL),(69,18,12,'2023-05-02 09:12:47pm',5,0,NULL),(70,30,12,'2023-05-02 11:08:57pm',1,0,NULL),(71,26,12,'2023-05-07 06:05:04pm',3,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_request`
--

LOCK TABLES `book_request` WRITE;
/*!40000 ALTER TABLE `book_request` DISABLE KEYS */;
INSERT INTO `book_request` VALUES (16,26,'2023-04-28 07:46:20pm','2023-04-29T21:00:00.000Z',1,0,10,0),(18,18,'2023-04-28 10:42:26pm','2023-04-29T21:00:00.000Z',1,0,12,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_review`
--

LOCK TABLES `book_review` WRITE;
/*!40000 ALTER TABLE `book_review` DISABLE KEYS */;
INSERT INTO `book_review` VALUES (11,18,'asdasdasd',4,12),(12,18,'review',3,12),(13,18,'testas',5,12),(14,18,'asdasdasd',4,12);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `main_book_image`
--

LOCK TABLES `main_book_image` WRITE;
/*!40000 ALTER TABLE `main_book_image` DISABLE KEYS */;
INSERT INTO `main_book_image` VALUES (2,'9780593308431','http://books.google.com/books/content?id=HSBtEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Hot Dog','Doug Salati'),(3,'1638930228','http://books.google.com/books/content?id=i_lGzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Rosewater','Liv Little'),(4,'9781638930228','http://books.google.com/books/content?id=i_lGzwEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','Rosewater','Liv Little'),(5,'9781523516872','http://books.google.com/books/content?id=B-BCEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api','I Will Teach You to Be Rich: The Journal','Ramit Sethi');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_report`
--

LOCK TABLES `return_report` WRITE;
/*!40000 ALTER TABLE `return_report` DISABLE KEYS */;
INSERT INTO `return_report` VALUES (2,10,10,'aasdasd','1010',16);
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'username','[]','qweasd123','email@email.com',NULL,'','','',NULL,0),(3,'username2','[]','qweasd123','email@email.com',NULL,'','','',NULL,1),(5,'username3','[]','qweasd123','email@email.com',NULL,'','','',NULL,1),(10,'adomas3000','[]','$2y$13$tZazvGAVC2kprY4POLpPKeEd2Q4EaXWyuGFlmQg/vbLwQjLykQDSm','asdasds@asdasd.lt',3,'+37064534599','','',NULL,0),(12,'adomej99','[\"ROLE_ADMIN\"]','$2y$13$Lxjfg2H.vbIUUzUJ6.UZiO8pczWBilZHSAQNi9e6X6sSo92YWZvM2','adomej99@gmail.com',5,'+37064534596','Kauno r.','asdasdsa','Fb: Adomas Mejaras',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_review`
--

LOCK TABLES `user_review` WRITE;
/*!40000 ALTER TABLE `user_review` DISABLE KEYS */;
INSERT INTO `user_review` VALUES (18,10,'asdasdasd',3,12),(19,10,'lender',3,12),(21,10,'test',2,12),(22,10,'asdasdasdasd',4,12);
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

-- Dump completed on 2023-05-08 22:11:51

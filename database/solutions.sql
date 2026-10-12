-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: solutions
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Current Database: `solutions`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `solutions` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `solutions`;

--
-- Table structure for table `dev_resources`
--

DROP TABLE IF EXISTS `dev_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `type` enum('snippet','project','package','tool','link') NOT NULL DEFAULT 'snippet',
  `language` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime` varchar(120) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `dev_resources_user_id_index` (`user_id`),
  KEY `dev_resources_type_index` (`type`),
  CONSTRAINT `dev_resources_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_resources`
--

LOCK TABLES `dev_resources` WRITE;
/*!40000 ALTER TABLE `dev_resources` DISABLE KEYS */;
INSERT INTO `dev_resources` VALUES (1,1,'Detector de acordes con Web Audio API','Snippet en JavaScript que analiza el microfono y estima el acorde dominante en tiempo real.','snippet','JavaScript','','const analyser = ctx.createAnalyser();\nanalyser.fftSize = 4096;\nconst data = new Float32Array(analyser.fftSize);',NULL,NULL,NULL,NULL,'2026-10-12 21:16:54','2026-10-12 21:16:54'),(2,1,'Utilidades de teoria musical','Libreria con conversiones entre notas, frecuencias y numeros MIDI.','package','TypeScript','https://www.npmjs.com/',NULL,NULL,NULL,NULL,NULL,'2026-10-12 21:16:54','2026-10-12 21:16:54'),(3,1,'Plantilla MVC en PHP','Estructura base de un proyecto PHP sin framework, muy util para empezar rapido.','project','PHP','https://github.com/',NULL,NULL,NULL,NULL,NULL,'2026-10-12 21:16:54','2026-10-12 21:16:54');
/*!40000 ALTER TABLE `dev_resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(190) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `migrations_migration_unique` (`migration`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_create_users_table.sql','2026-10-12 21:16:50'),(2,'0002_create_scores_table.sql','2026-10-12 21:16:50'),(3,'0003_create_songs_table.sql','2026-10-12 21:16:50'),(4,'0004_create_dev_resources_table.sql','2026-10-12 21:16:50'),(5,'0005_add_preview_url_to_songs.sql','2026-10-12 21:51:22');
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `composer` varchar(120) NOT NULL DEFAULT '',
  `instrument` varchar(80) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime` varchar(120) NOT NULL DEFAULT 'application/octet-stream',
  `size` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `scores_user_id_index` (`user_id`),
  CONSTRAINT `scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scores`
--

LOCK TABLES `scores` WRITE;
/*!40000 ALTER TABLE `scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `songs`
--

DROP TABLE IF EXISTS `songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `songs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `artist` varchar(160) NOT NULL DEFAULT '',
  `album` varchar(160) NOT NULL DEFAULT '',
  `genre` varchar(80) NOT NULL DEFAULT '',
  `preview_url` varchar(500) DEFAULT NULL,
  `year` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `songs_title_index` (`title`),
  KEY `songs_artist_index` (`artist`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `songs`
--

LOCK TABLES `songs` WRITE;
/*!40000 ALTER TABLE `songs` DISABLE KEYS */;
INSERT INTO `songs` VALUES (1,'Claro de Luna','Ludwig van Beethoven','Sonatas','Clasica','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',1801,'2026-10-12 21:16:54'),(2,'Canon en Re mayor','Johann Pachelbel','Obras barrocas','Clasica','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',1680,'2026-10-12 21:16:54'),(3,'Asturias (Leyenda)','Isaac Albeniz','Suite Espanola','Clasica','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',1892,'2026-10-12 21:16:54'),(4,'Entre dos aguas','Paco de Lucia','Fuente y caudal','Flamenco','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',1973,'2026-10-12 21:16:54'),(5,'Spain','Chick Corea','Light as a Feather','Jazz','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3',1973,'2026-10-12 21:16:54'),(6,'Take Five','Dave Brubeck','Time Out','Jazz','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3',1959,'2026-10-12 21:16:54'),(7,'So What','Miles Davis','Kind of Blue','Jazz','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',1959,'2026-10-12 21:16:54'),(8,'Autumn Leaves','Joseph Kosma','Standards','Jazz','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',1945,'2026-10-12 21:16:54'),(9,'Hotel California','Eagles','Hotel California','Rock','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3',1976,'2026-10-12 21:16:54'),(10,'Stairway to Heaven','Led Zeppelin','Led Zeppelin IV','Rock','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3',1971,'2026-10-12 21:16:54'),(11,'Bohemian Rhapsody','Queen','A Night at the Opera','Rock','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3',1975,'2026-10-12 21:16:54'),(12,'Wonderwall','Oasis','(What\'s the Story) Morning Glory?','Rock','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3',1995,'2026-10-12 21:16:54'),(13,'Blackbird','The Beatles','The White Album','Rock','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3',1968,'2026-10-12 21:16:54'),(14,'Shape of You','Ed Sheeran','Divide','Pop','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3',2017,'2026-10-12 21:16:54'),(15,'Someone Like You','Adele','21','Pop','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3',2011,'2026-10-12 21:16:54'),(16,'Billie Jean','Michael Jackson','Thriller','Pop','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',1982,'2026-10-12 21:16:54');
/*!40000 ALTER TABLE `songs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `username` varchar(40) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Usuario Demo','demo','demo@musicsolutions.test','$2y$10$SD18Vv3NfI7fRfPHzYMu.udYjdGcggPUZGeMJAC0A5MkKBQGYqYIy','admin','2026-10-12 21:16:54','2026-10-12 21:16:54'),(3,'MAGO','MAGO','al@gmail.com','$2y$10$xgK7O.Rvw6VA3lWioiomxue6GtC3mUFOFnOY4r9qyLWx0aYZp8eVu','member','2026-10-12 21:31:43','2026-10-12 21:31:43');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-12 16:58:26

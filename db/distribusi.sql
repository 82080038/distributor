-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: distributor
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
-- Table structure for table `branch_product_prices`
--

DROP TABLE IF EXISTS `branch_product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_product_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `buy_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sell_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `effective_date` date NOT NULL DEFAULT curdate(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bpp_branch_product_date` (`branch_id`,`product_id`,`effective_date`),
  KEY `fk_bpp_product` (`product_id`),
  CONSTRAINT `fk_bpp_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_bpp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_product_prices`
--

LOCK TABLES `branch_product_prices` WRITE;
/*!40000 ALTER TABLE `branch_product_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_product_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int(10) unsigned NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `regency_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `village_id` int(10) unsigned DEFAULT NULL,
  `street_address` text DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `owner_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_branches_perusahaan` (`perusahaan_id`),
  CONSTRAINT `fk_branches_perusahaan` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id_perusahaan`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,2,'CBG2001','USAHA PATRI','081265511982',NULL,NULL,NULL,NULL,'DUSUN 2, JL, SIMANINDO-PANGURURAN NO 111','',1,'2026-01-19 04:43:46',NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orang`
--

DROP TABLE IF EXISTS `orang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orang` (
  `id_orang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int(10) unsigned DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `regency_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `village_id` int(10) unsigned DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `is_supplier` tinyint(1) NOT NULL DEFAULT 0,
  `is_customer` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_orang`),
  KEY `fk_orang_perusahaan` (`perusahaan_id`),
  KEY `idx_orang_supplier_active_name` (`is_supplier`,`is_active`,`nama_lengkap`),
  KEY `idx_orang_customer_active_name` (`is_customer`,`is_active`,`nama_lengkap`),
  CONSTRAINT `fk_orang_perusahaan` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id_perusahaan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orang`
--

LOCK TABLES `orang` WRITE;
/*!40000 ALTER TABLE `orang` DISABLE KEYS */;
INSERT INTO `orang` VALUES (1,2,'PATRI SIHALOHO','DUSUN 2, JL, SIMANINDO-PANGURURAN NO 111','081265511982',3,40,590,10630,'22392',1,1,1,'2026-01-17 18:58:34','2026-01-18 00:21:05'),(2,NULL,'SPPG LUMBAN SUHI','LUMBAN SUHI','08156554418',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 04:19:16',NULL),(3,NULL,'siapa','','15254',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 17:26:39',NULL),(4,NULL,'siapa','','15254',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 17:39:30',NULL),(5,NULL,'siapa','','15254',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 17:39:35',NULL),(6,NULL,'siapa','','15254',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 17:40:03',NULL),(7,NULL,'siapa','','15254',NULL,NULL,NULL,NULL,NULL,0,1,1,'2026-01-18 17:42:15',NULL);
/*!40000 ALTER TABLE `orang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perusahaan`
--

DROP TABLE IF EXISTS `perusahaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perusahaan` (
  `id_perusahaan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_perusahaan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perusahaan`
--

LOCK TABLES `perusahaan` WRITE;
/*!40000 ALTER TABLE `perusahaan` DISABLE KEYS */;
INSERT INTO `perusahaan` VALUES (1,'Perusahaan Utama',NULL,NULL,'2026-01-17 18:18:07',NULL),(2,'USAHA PATRI','DUSUN 2, JL, SIMANINDO-PANGURURAN NO 111','081265511982','2026-01-17 18:58:34',NULL);
/*!40000 ALTER TABLE `perusahaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plu_codes`
--

DROP TABLE IF EXISTS `plu_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plu_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plu_number` varchar(10) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `scientific_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `variety` varchar(100) DEFAULT NULL,
  `size_grade` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `commodity_code` varchar(50) DEFAULT NULL,
  `is_organic` tinyint(1) DEFAULT 0,
  `is_conventional` tinyint(1) DEFAULT 1,
  `is_gmo` tinyint(1) DEFAULT 0,
  `standard_unit` varchar(20) DEFAULT 'KG',
  `country_origin` varchar(3) DEFAULT NULL,
  `seasonality` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seasonality`)),
  `storage_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`storage_requirements`)),
  `shelf_life_days` int(11) DEFAULT NULL,
  `nutrition_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nutrition_info`)),
  `allergen_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allergen_info`)),
  `certification` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certification`)),
  `is_active` tinyint(1) DEFAULT 1,
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `plu_number` (`plu_number`),
  KEY `idx_plu_number` (`plu_number`),
  KEY `idx_plu_name` (`product_name`),
  KEY `idx_plu_category` (`category`),
  KEY `idx_plu_subcategory` (`subcategory`),
  KEY `idx_plu_variety` (`variety`),
  KEY `idx_plu_active` (`is_active`),
  KEY `idx_plu_origin` (`country_origin`),
  FULLTEXT KEY `idx_plu_search` (`product_name`,`description`,`variety`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plu_codes`
--

LOCK TABLES `plu_codes` WRITE;
/*!40000 ALTER TABLE `plu_codes` DISABLE KEYS */;
INSERT INTO `plu_codes` VALUES (1,'4011','Red Delicious Apples',NULL,'FRUITS','APPLES','RED DELICIOUS',NULL,'Crispy and sweet red apples',NULL,0,1,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(2,'4012','Golden Delicious Apples',NULL,'FRUITS','APPLES','GOLDEN DELICIOUS',NULL,'Sweet yellow apples with thin skin',NULL,0,1,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(3,'4015','Gala Apples',NULL,'FRUITS','APPLES','GALA',NULL,'Sweet and crisp apples',NULL,0,1,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(4,'4016','Granny Smith Apples',NULL,'FRUITS','APPLES','GRANNY SMITH',NULL,'Tart green apples',NULL,0,1,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(5,'4017','Fuji Apples',NULL,'FRUITS','APPLES','FUJI',NULL,'Very sweet and crisp Japanese apples',NULL,0,1,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(6,'94011','Organic Red Delicious Apples',NULL,'FRUITS','APPLES','RED DELICIOUS',NULL,'Organic crispy and sweet red apples',NULL,1,0,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(7,'94012','Organic Golden Delicious Apples',NULL,'FRUITS','APPLES','GOLDEN DELICIOUS',NULL,'Organic sweet yellow apples',NULL,1,0,0,'KG',NULL,NULL,NULL,180,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(8,'4030','Bananas',NULL,'FRUITS','BANANAS','CAVENDISH',NULL,'Standard yellow bananas',NULL,0,1,0,'KG',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(9,'4031','Organic Bananas',NULL,'FRUITS','BANANAS','CAVENDISH',NULL,'Organic yellow bananas',NULL,1,0,0,'KG',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(10,'4032','Plantains',NULL,'FRUITS','BANANAS','COOKING',NULL,'Cooking bananas, starchy',NULL,0,1,0,'KG',NULL,NULL,NULL,21,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(11,'4033','Red Bananas',NULL,'FRUITS','BANANAS','RED',NULL,'Sweet red bananas',NULL,0,1,0,'KG',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(12,'4046','Oranges',NULL,'FRUITS','CITRUS','NAVEL',NULL,'Sweet seedless oranges',NULL,0,1,0,'KG',NULL,NULL,NULL,56,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(13,'4047','Organic Oranges',NULL,'FRUITS','CITRUS','NAVEL',NULL,'Organic sweet seedless oranges',NULL,1,0,0,'KG',NULL,NULL,NULL,56,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(14,'4048','Valencia Oranges',NULL,'FRUITS','CITRUS','VALENCIA',NULL,'Juicy oranges, great for juicing',NULL,0,1,0,'KG',NULL,NULL,NULL,56,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(15,'4050','Lemons',NULL,'FRUITS','CITRUS','EUREKA',NULL,'Tart yellow lemons',NULL,0,1,0,'KG',NULL,NULL,NULL,42,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(16,'4052','Limes',NULL,'FRUITS','CITRUS','PERSIAN',NULL,'Tart green limes',NULL,0,1,0,'KG',NULL,NULL,NULL,42,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(17,'4060','Grapes',NULL,'FRUITS','GRAPES','RED GLOBE',NULL,'Sweet red grapes',NULL,0,1,0,'KG',NULL,NULL,NULL,21,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(18,'4062','Green Grapes',NULL,'FRUITS','GRAPES','THOMPSON',NULL,'Sweet green grapes',NULL,0,1,0,'KG',NULL,NULL,NULL,21,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(19,'4063','Organic Grapes',NULL,'FRUITS','GRAPES','RED GLOBE',NULL,'Organic sweet red grapes',NULL,1,0,0,'KG',NULL,NULL,NULL,21,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(20,'4066','Strawberries',NULL,'FRUITS','BERRIES','ALBION',NULL,'Sweet red strawberries',NULL,0,1,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(21,'4067','Organic Strawberries',NULL,'FRUITS','BERRIES','ALBION',NULL,'Organic sweet red strawberries',NULL,1,0,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(22,'4068','Blueberries',NULL,'FRUITS','BERRIES','HIGHBUSH',NULL,'Sweet blueberries',NULL,0,1,0,'KG',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(23,'4069','Organic Blueberries',NULL,'FRUITS','BERRIES','HIGHBUSH',NULL,'Organic sweet blueberries',NULL,1,0,0,'KG',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(24,'4080','Mangoes',NULL,'FRUITS','TROPICAL','TOMMY ATKINS',NULL,'Sweet tropical mangoes',NULL,0,1,0,'PCS',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(25,'4081','Organic Mangoes',NULL,'FRUITS','TROPICAL','TOMMY ATKINS',NULL,'Organic sweet tropical mangoes',NULL,1,0,0,'PCS',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(26,'4082','Pineapples',NULL,'FRUITS','TROPICAL','QUEEN',NULL,'Sweet pineapples',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(27,'4083','Papayas',NULL,'FRUITS','TROPICAL','SOLO',NULL,'Sweet papayas',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(28,'4084','Organic Papayas',NULL,'FRUITS','TROPICAL','SOLO',NULL,'Organic sweet papayas',NULL,1,0,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:30:28','2026-01-17 18:30:28'),(73,'9001','Red Large Chili',NULL,'VEGETABLES','CHILI','BESAR',NULL,'Large red cooking chili',NULL,0,1,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(74,'9002','Bird\'s Eye Chili',NULL,'VEGETABLES','CHILI','RAWIT',NULL,'Very small spicy chili',NULL,0,1,0,'KG',NULL,NULL,NULL,5,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(75,'9003','Green Chili',NULL,'VEGETABLES','CHILI','HIJAU',NULL,'Unripe red chili',NULL,0,1,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(76,'9004','Cayenne Pepper',NULL,'VEGETABLES','CHILI','KERITING',NULL,'Curly red chili',NULL,0,1,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(77,'9010','Purple Eggplant',NULL,'VEGETABLES','EGGPLANT','UNGU',NULL,'Common purple eggplant',NULL,0,1,0,'KG',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(78,'9011','Green Eggplant',NULL,'VEGETABLES','EGGPLANT','HIJAU',NULL,'Local green eggplant',NULL,0,1,0,'KG',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(79,'9012','Thai Eggplant',NULL,'VEGETABLES','EGGPLANT','BELUT',NULL,'Long thin Thai eggplant',NULL,0,1,0,'KG',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(80,'9020','Water Spinach',NULL,'VEGETABLES','LEAFY','AIR',NULL,'Aquatic vegetable, popular in stir-fry',NULL,0,1,0,'IKAT',NULL,NULL,NULL,3,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(81,'9021','Amaranth',NULL,'VEGETABLES','LEAFY','MERAH',NULL,'Red amaranth leaves',NULL,0,1,0,'IKAT',NULL,NULL,NULL,3,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(82,'9022','Chinese Cabbage',NULL,'VEGETABLES','LEAFY','PAHIT',NULL,'Bitter Chinese cabbage',NULL,0,1,0,'IKAT',NULL,NULL,NULL,4,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(83,'9023','Cassava Leaves',NULL,'VEGETABLES','LEAFY','TIPAR',NULL,'Young cassava leaves, local delicacy',NULL,0,1,0,'IKAT',NULL,NULL,NULL,2,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(84,'9030','Ambon Banana',NULL,'FRUITS','BANANAS','AMBON',NULL,'Sweet local banana variety',NULL,0,1,0,'SISIR',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(85,'9031','King Banana',NULL,'FRUITS','BANANAS','RAJA',NULL,'Premium local banana',NULL,0,1,0,'SISIR',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(86,'9032','Kepok Banana',NULL,'FRUITS','BANANAS','KEPOK',NULL,'Cooking banana variety',NULL,0,1,0,'SISIR',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(87,'9033','Horn Banana',NULL,'FRUITS','BANANAS','TANDUK',NULL,'Large banana variety',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(88,'9040','Pineapple',NULL,'FRUITS','TROPICAL','QUEEN',NULL,'Sweet local pineapple',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(89,'9041','Watermelon',NULL,'FRUITS','MELONS','MERAH',NULL,'Red flesh watermelon',NULL,0,1,0,'PCS',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(90,'9042','Melon',NULL,'FRUITS','MELONS','GALIA',NULL,'Green flesh galia melon',NULL,0,1,0,'PCS',NULL,NULL,NULL,10,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(91,'9043','Durian',NULL,'FRUITS','TROPICAL','MONTHONG',NULL,'King of fruits, premium variety',NULL,0,1,0,'PCS',NULL,NULL,NULL,5,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(92,'9044','Mangosteen',NULL,'FRUITS','TROPICAL','TANPA BJIH',NULL,'Queen of fruits',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(93,'9045','Rambutan',NULL,'FRUITS','TROPICAL','BINJAI',NULL,'Hairy sweet fruit',NULL,0,1,0,'PCS',NULL,NULL,NULL,5,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(94,'9046','Salak',NULL,'FRUITS','TROPICAL','PONDOK',NULL,'Snake fruit, sweet and tangy',NULL,0,1,0,'PCS',NULL,NULL,NULL,14,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(95,'9047','Jackfruit',NULL,'FRUITS','TROPICAL','MINI',NULL,'Small variety jackfruit',NULL,0,1,0,'PCS',NULL,NULL,NULL,7,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(96,'9050','Sunkist Orange',NULL,'FRUITS','CITRUS','SUNKIST',NULL,'Imported Sunkist oranges',NULL,0,1,0,'KG',NULL,NULL,NULL,56,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(97,'9051','Local Orange',NULL,'FRUITS','CITRUS','KEPROK',NULL,'Local sweet oranges',NULL,0,1,0,'KG',NULL,NULL,NULL,42,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(98,'9052','Mandarin Orange',NULL,'FRUITS','CITRUS','MANDARIN',NULL,'Easy peel mandarin oranges',NULL,0,1,0,'KG',NULL,NULL,NULL,35,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(99,'9053','Pomelo',NULL,'FRUITS','CITRUS','BALI',NULL,'Large citrus from Bali',NULL,0,1,0,'PCS',NULL,NULL,NULL,30,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:32:16','2026-01-17 18:32:16'),(100,'4086','Red Onions',NULL,'VEGETABLES','ALLIUM','RED',NULL,'Mild red onions',NULL,0,1,0,'KG',NULL,NULL,NULL,90,NULL,NULL,NULL,1,NULL,NULL,'2026-01-17 18:40:45','2026-01-17 18:40:45');
/*!40000 ALTER TABLE `plu_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_barcodes`
--

DROP TABLE IF EXISTS `product_barcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_barcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `plu_code_id` bigint(20) unsigned DEFAULT NULL,
  `barcode_type` varchar(20) NOT NULL,
  `barcode_value` varchar(50) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_barcodes_value` (`barcode_value`),
  KEY `idx_product_barcodes_product` (`product_id`),
  KEY `idx_product_barcodes_plu` (`plu_code_id`),
  KEY `idx_product_barcodes_type` (`barcode_type`),
  CONSTRAINT `fk_product_barcodes_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_barcodes`
--

LOCK TABLES `product_barcodes` WRITE;
/*!40000 ALTER TABLE `product_barcodes` DISABLE KEYS */;
INSERT INTO `product_barcodes` VALUES (1,1,73,'PLU','9001',1,'PLU 9001 untuk Cabai Merah Besar',1,'2026-01-18 01:35:34',NULL),(2,2,80,'PLU','9020',1,'PLU 9020 untuk Kangkung Segar',1,'2026-01-18 01:35:34',NULL),(4,7,100,'PLU','4086',1,'PLU 4086 untuk Bawang Merah',1,'2026-01-18 01:40:45',NULL);
/*!40000 ALTER TABLE `product_barcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (10,'Buah-buahan'),(11,'Bumbu & Rempah Segar'),(6,'Daging & Unggas'),(4,'Gula & Pemanis'),(7,'Ikan & Seafood'),(8,'Kacang-kacangan & Kedelai'),(12,'Lain-lain'),(3,'Minyak & Lemak'),(1,'Pangan Pokok'),(9,'Sayuran'),(5,'Telur & Produk Hewani'),(2,'Tepung & Bahan Roti');
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories_backup`
--

DROP TABLE IF EXISTS `product_categories_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_categories_backup` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories_backup`
--

LOCK TABLES `product_categories_backup` WRITE;
/*!40000 ALTER TABLE `product_categories_backup` DISABLE KEYS */;
INSERT INTO `product_categories_backup` VALUES (11,'Bawang'),(6,'Beras'),(13,'Buah'),(10,'Cabai'),(15,'Daging Ayam'),(3,'Daging dan Protein'),(14,'Daging Sapi'),(9,'Gula'),(16,'Ikan'),(18,'Kedelai'),(5,'Lain-lain'),(4,'Minuman'),(7,'Minyak Goreng'),(2,'Sayur dan Buah'),(12,'Sayuran'),(1,'Sembako'),(8,'Telur'),(17,'Tepung Terigu');
/*!40000 ALTER TABLE `product_categories_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_plu_mapping`
--

DROP TABLE IF EXISTS `product_plu_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_plu_mapping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `plu_code_id` bigint(20) unsigned NOT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `local_name` varchar(150) DEFAULT NULL,
  `is_primary_plu` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date NOT NULL DEFAULT curdate(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product_plu_plu` (`plu_code_id`),
  KEY `idx_product_plu_province` (`province_id`),
  KEY `idx_product_plu_product` (`product_id`),
  CONSTRAINT `fk_product_plu_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_plu_mapping`
--

LOCK TABLES `product_plu_mapping` WRITE;
/*!40000 ALTER TABLE `product_plu_mapping` DISABLE KEYS */;
INSERT INTO `product_plu_mapping` VALUES (1,1,73,NULL,'Cabai Merah Besar',1,'2026-01-18','2026-01-18 01:35:50',NULL),(2,2,80,NULL,'Kangkung Segar',1,'2026-01-18','2026-01-18 01:35:50',NULL),(4,7,100,NULL,'Bawang Merah',1,'2026-01-18','2026-01-18 01:40:45',NULL);
/*!40000 ALTER TABLE `product_plu_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `buy_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `profit_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sell_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `internet_price` decimal(15,2) DEFAULT NULL,
  `internet_date` date DEFAULT NULL,
  `stock_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `min_stock_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_products_category` (`category_id`),
  KEY `idx_products_active_name` (`is_active`,`name`),
  KEY `idx_products_code` (`code`),
  KEY `idx_products_barcode` (`barcode`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'CABAI_MERAH','Cabai Merah Besar','KG','9001',11,19102.00,25.00,23877.00,26530.00,'2024-11-10',47.143,15.017,1,'2026-01-18 01:35:23','2026-01-18 21:51:18'),(2,'KANGKUNG','Kangkung Segar','IKAT','9020',9,7680.00,25.00,9600.00,NULL,NULL,33.996,8.945,1,'2026-01-18 01:35:23','2026-01-18 21:37:53'),(3,'BERAS_MEDIUM','Beras Medium','KG',NULL,1,13286.00,25.00,16608.00,13840.00,'2024-11-10',21.629,14.795,1,'2026-01-18 01:37:59','2026-01-18 21:51:18'),(4,'MINYAK_GORENG_SAWIT','Minyak Goreng Sawit','LITER',NULL,3,16320.00,25.00,20400.00,18350.00,'2024-11-10',4.982,0.598,1,'2026-01-18 01:37:59','2026-01-18 21:49:05'),(5,'TELUR_AYAM_RAS','Telur Ayam Ras','TRAY',NULL,5,26880.00,25.00,33600.00,NULL,NULL,0.009,18.214,1,'2026-01-18 01:37:59','2026-01-18 22:01:44'),(6,'GULA_PASIR','Gula Pasir','KG',NULL,4,14400.00,25.00,18000.00,18030.00,'2024-11-10',55.334,0.690,1,'2026-01-18 01:37:59','2026-01-18 21:49:05'),(7,'BAWANG_MERAH','Bawang Merah','KG',NULL,11,30720.00,25.00,38400.00,32920.00,'2024-11-10',51.259,9.187,1,'2026-01-18 01:37:59','2026-01-18 21:49:05'),(9,'BAWANG_PUTIH','Bawang Putih','KG','',11,28591.00,25.00,35739.00,39710.00,'2024-11-10',75.908,8.346,1,'2026-01-18 19:14:37','2026-01-18 21:51:18'),(10,'IKAN_NILA','Ikan Nila','KG','',7,33600.00,25.00,42000.00,NULL,NULL,80.933,15.894,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(11,'MELON','Melon','KG','',10,14400.00,25.00,18000.00,NULL,NULL,54.553,6.871,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(12,'TEPUNG_TERIGU','Tepung Terigu','KG','',2,10224.00,25.00,12780.00,14200.00,'2024-11-10',8.107,7.496,1,'2026-01-18 19:14:37','2026-01-18 21:51:18'),(13,'KEMIRI','Kemiri','KG','',11,9600.00,25.00,12000.00,NULL,NULL,63.066,0.580,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(14,'CABAI_RAWIT_HIJAU','Cabai Rawit Hijau','KG','',11,9600.00,25.00,12000.00,NULL,NULL,25.289,3.551,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(15,'CABAI_RAWIT_MERAH','Cabai Rawit Merah','KG','',11,24386.00,25.00,30483.00,33870.00,'2024-11-10',12.903,2.251,1,'2026-01-18 19:14:37','2026-01-18 21:51:18'),(16,'JAHE','Jahe','KG','',11,9600.00,25.00,12000.00,NULL,NULL,17.559,10.807,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(17,'PAKCOY','Pakcoy','KG','',11,14400.00,25.00,18000.00,NULL,NULL,17.499,5.077,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(18,'KUNYIT_BUBUK','Kunyit Bubuk','RENCENG','',11,9600.00,25.00,12000.00,NULL,NULL,74.438,19.206,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(19,'GARAM_HALUS','Garam Halus','BUNGKUS','',11,7680.00,25.00,9600.00,NULL,NULL,56.833,19.215,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(20,'TEMPE','Tempe','PAPAN','',8,7680.00,25.00,9600.00,NULL,NULL,9.881,12.236,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(21,'BATANG_RIAS','Batang Rias','KG','',11,9600.00,25.00,12000.00,NULL,NULL,76.247,19.540,0,'2026-01-18 19:14:37','2026-01-19 01:18:30'),(22,'SAUS_TIRAM','Saus Tiram','BOTOL','',11,9600.00,25.00,12000.00,NULL,NULL,59.768,1.147,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(23,'TEPUNG_MAIZENA','Tepung Maizena','KG','',2,9600.00,25.00,12000.00,NULL,NULL,49.368,5.927,1,'2026-01-18 19:14:37','2026-01-18 21:37:53'),(24,'ANDALIMAN','Andaliman','KG','',11,9600.00,25.00,12000.00,NULL,NULL,0.072,2.291,0,'2026-01-18 19:14:37','2026-01-19 01:18:16'),(25,'GARAM_KASAR','Garam Kasar','PAK','',11,7680.00,25.00,9600.00,NULL,NULL,57.053,10.181,1,'2026-01-18 19:19:55','2026-01-18 21:37:53'),(26,'JERUK_NIPIS','Jeruk Nipis','KG','',11,19200.00,25.00,24000.00,NULL,NULL,83.366,12.823,1,'2026-01-18 19:19:55','2026-01-18 21:37:53'),(27,'TOMAT','Tomat','KG','',9,9600.00,25.00,12000.00,NULL,NULL,70.481,12.011,1,'2026-01-18 19:19:55','2026-01-18 21:37:53'),(28,'APEL_MERAH','Apel Merah','KG',NULL,10,25520.00,25.00,31900.00,NULL,NULL,88.837,12.804,0,'2026-01-18 21:02:37','2026-01-19 01:18:23'),(29,'PISANG_CAVENDISH','Pisang Cavendish','KG',NULL,10,15920.00,25.00,19900.00,NULL,NULL,53.580,15.169,1,'2026-01-18 21:02:37','2026-01-18 21:37:53'),(30,'CABAI_MERAH_SPPG','Cabai Merah','KG',NULL,11,19102.00,25.00,23877.00,26530.00,'2024-11-10',18.491,12.983,1,'2026-01-18 21:02:37','2026-01-18 21:51:18'),(31,'BAWANG_BOMBAI','Bawang Bombay','KG',NULL,11,28000.00,25.00,35000.00,NULL,NULL,69.102,10.153,0,'2026-01-18 21:14:28','2026-01-19 01:19:55'),(32,'BAWANG_PUTIH_BUBUK','Bawang Putih Bubuk','RENCENG',NULL,11,14400.00,25.00,18000.00,NULL,NULL,46.516,16.058,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(33,'BUMBU_RACIK_RENDANG','Bumbu Racik Rendang','RENCENG',NULL,11,4800.00,25.00,6000.00,NULL,NULL,61.896,13.723,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(34,'BUNCIS','Buncis Segar','KG',NULL,9,14400.00,25.00,18000.00,NULL,NULL,57.376,16.208,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(35,'CABAI_HIJAU','Cabai Hijau','KG',NULL,11,28800.00,25.00,36000.00,NULL,NULL,33.081,4.456,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(36,'DAGING_AYAM','Daging Ayam','KG',NULL,6,32035.00,25.00,40044.00,33370.00,'2024-11-10',12.148,18.782,1,'2026-01-18 21:14:28','2026-01-18 21:51:18'),(37,'DAUN_BAWANG','Daun Bawang','KG',NULL,11,8000.00,25.00,10000.00,NULL,NULL,33.093,16.748,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(38,'DAUN_JERUK','Daun Jeruk','KG',NULL,11,32000.00,25.00,40000.00,NULL,NULL,19.429,9.184,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(39,'DAUN_KUNYIT','Daun Kunyit','KG',NULL,11,32000.00,25.00,40000.00,NULL,NULL,71.313,3.761,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(40,'DAUN_SALAM','Daun Salam','KG',NULL,11,32000.00,25.00,40000.00,NULL,NULL,80.085,8.802,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(41,'DUKU','Duku','KG',NULL,10,19200.00,25.00,24000.00,NULL,NULL,79.794,13.388,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(42,'JAGUNG','Jagung','KG',NULL,9,5434.00,24.99,6792.00,5660.00,'2024-11-10',95.318,15.154,1,'2026-01-18 21:14:28','2026-01-18 21:51:18'),(43,'JERUK_BUAH','Jeruk Buah','KG',NULL,10,19200.00,25.00,24000.00,NULL,NULL,92.900,7.437,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(44,'KALDU_BUBUK','Kaldu Bubuk','RENCENG',NULL,11,8000.00,25.00,10000.00,NULL,NULL,7.236,4.924,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(45,'KECAP_ASIN','Kecap Asin','BOTOL',NULL,11,16000.00,25.00,20000.00,NULL,NULL,1.388,6.617,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(46,'KECAP_MANIS','Kecap Manis','BOTOL',NULL,11,20000.00,25.00,25000.00,NULL,NULL,61.248,1.399,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(47,'KENTANG','Kentang','KG',NULL,9,12000.00,25.00,15000.00,NULL,NULL,51.224,7.028,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(48,'KETUMBAR_BUBUK','Ketumbar Bubuk','RENCENG',NULL,11,12000.00,25.00,15000.00,NULL,NULL,22.024,0.941,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(49,'LABU_SIAM','Labu Siam','KG',NULL,9,8000.00,25.00,10000.00,NULL,NULL,57.447,14.625,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(50,'LADA','Lada','KG',NULL,11,72000.00,25.00,90000.00,NULL,NULL,93.272,9.398,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(51,'PISANG','Pisang','KG',NULL,10,12800.00,25.00,16000.00,NULL,NULL,55.144,6.949,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(52,'ROTI','Roti','BUNGKUS',NULL,2,12000.00,25.00,15000.00,NULL,NULL,8.287,7.441,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(53,'SALAK','Salak','KG',NULL,10,8000.00,25.00,10000.00,NULL,NULL,61.160,18.838,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(54,'SANTAN_KENTAL','Santan Kental','BUNGKUS',NULL,3,4000.00,25.00,5000.00,NULL,NULL,87.462,10.949,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(55,'SAWI_PUTIH','Sawi Putih','KG',NULL,9,8000.00,25.00,10000.00,NULL,NULL,11.342,18.495,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(56,'SEMANGKA','Semangka','KG',NULL,10,5600.00,25.00,7000.00,NULL,NULL,28.345,12.860,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(57,'SERAI','Serai','KG',NULL,11,32000.00,25.00,40000.00,NULL,NULL,36.467,17.887,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(58,'SUSU_UHT_115','Susu UHT 115 ml','PCS',NULL,5,2800.00,25.00,3500.00,NULL,NULL,37.763,4.103,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(59,'SUSU_UHT_125','Susu UHT 125 ml','PCS',NULL,5,2800.00,25.00,3500.00,NULL,NULL,89.297,16.987,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(60,'TAHU','Tahu','PAPAN',NULL,8,44000.00,25.00,55000.00,NULL,NULL,56.781,5.820,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(61,'TERASI','Terasi','BUNGKUS',NULL,11,6400.00,25.00,8000.00,NULL,NULL,75.155,17.695,1,'2026-01-18 21:14:28','2026-01-18 21:37:53'),(62,'WORTEL','Wortel','KG',NULL,9,11200.00,25.00,14000.00,NULL,NULL,16.916,3.831,1,'2026-01-18 21:14:28','2026-01-18 21:37:53');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_backup_categories`
--

DROP TABLE IF EXISTS `products_backup_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products_backup_categories` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `buy_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sell_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `min_stock_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_backup_categories`
--

LOCK TABLES `products_backup_categories` WRITE;
/*!40000 ALTER TABLE `products_backup_categories` DISABLE KEYS */;
INSERT INTO `products_backup_categories` VALUES (1,'CABAI_MERAH','Cabai Merah Besar','KG','9001',10,10000.00,12000.00,80.000,24.000,1,'2026-01-18 01:35:23','2026-01-18 19:17:05'),(2,'KANGKUNG','Kangkung Segar','IKAT','9020',12,8000.00,9600.00,36.000,10.000,1,'2026-01-18 01:35:23','2026-01-18 19:17:05'),(3,'BERAS_MEDIUM','Beras Medium','KG',NULL,6,14000.00,16800.00,60.000,18.000,1,'2026-01-18 01:37:59','2026-01-18 19:17:05'),(4,'MINYAK_GORENG_SAWIT','Minyak Goreng Sawit','LITER',NULL,7,17000.00,20400.00,18.000,5.000,1,'2026-01-18 01:37:59','2026-01-18 19:17:05'),(5,'TELUR_AYAM_RAS','Telur Ayam Ras','PCS',NULL,8,28000.00,33600.00,19.000,3.000,1,'2026-01-18 01:37:59','2026-01-18 19:17:05'),(6,'GULA_PASIR','Gula Pasir','KG',NULL,9,15000.00,18000.00,76.000,22.000,1,'2026-01-18 01:37:59','2026-01-18 19:17:05'),(7,'BAWANG_MERAH','Bawang Merah','KG',NULL,11,32000.00,38400.00,13.000,3.000,1,'2026-01-18 01:37:59','2026-01-18 19:17:05'),(9,'BAWANG_PUTIH','Bawang Putih','KG','',11,28000.00,33600.00,14.000,4.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(10,'IKAN_NILA','Ikan Nila','KG','',16,35000.00,42000.00,1.000,1.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(11,'MELON','Melon','KG','',11,15000.00,18000.00,18.000,5.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(12,'TEPUNG_TERIGU','Tepung Terigu','KG','',17,10000.00,12000.00,98.000,29.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(13,'KEMIRI','Kemiri','KG','',11,10000.00,12000.00,74.000,22.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(14,'CABAI_RAWIT_HIJAU','Cabai Rawit Hijau','KG','',10,10000.00,12000.00,50.000,15.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(15,'CABAI_RAWIT_MERAH','Cabai Rawit Merah','KG','',10,10000.00,12000.00,18.000,5.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(16,'JAHE','Jahe','KG','',11,10000.00,12000.00,86.000,25.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(17,'PAKCOY','Pakcoy','KG','',11,15000.00,18000.00,100.000,30.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(18,'KUNYIT_BUBUK','Kunyit Bubuk','RENCENG','',11,10000.00,12000.00,22.000,6.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(19,'GARAM_HALUS','Garam Halus','BUNGKUS','',11,8000.00,9600.00,72.000,21.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(20,'TEMPE','Tempe','PAPAN','',11,8000.00,9600.00,82.000,24.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(21,'BATANG_RIAS','Batang Rias','KG','',11,10000.00,12000.00,71.000,21.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(22,'SAUS_TIRAM','Saus Tiram','BOTOL','',11,10000.00,12000.00,14.000,4.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(23,'TEPUNG_MAIZENA','Tepung Maizena','KG','',11,10000.00,12000.00,14.000,4.000,1,'2026-01-18 19:14:37','2026-01-18 19:17:05'),(24,'ANDALIMAN','Andaliman','KG','',11,10000.00,12000.00,12.000,2.000,0,'2026-01-18 19:14:37','2026-01-18 20:31:53'),(25,'GARAM_KASAR','Garam Kasar','PAK','',11,8000.00,9600.00,24.000,7.000,1,'2026-01-18 19:19:55',NULL),(26,'JERUK_NIPIS','Jeruk Nipis','KG','',11,20000.00,24000.00,14.000,4.000,1,'2026-01-18 19:19:55',NULL),(27,'TOMAT','Tomat','KG','',11,10000.00,12000.00,77.000,23.000,1,'2026-01-18 19:19:55',NULL);
/*!40000 ALTER TABLE `products_backup_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `qty` decimal(15,3) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_purchase_items_purchase` (`purchase_id`),
  KEY `fk_purchase_items_product` (`product_id`),
  CONSTRAINT `fk_purchase_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_purchase_items_purchase` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (3,1,7,2.000,30720.00,61440.00),(4,1,9,2.000,28591.00,57182.00);
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `supplier_invoice_no` varchar(100) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_purchases_branch` (`branch_id`),
  KEY `fk_purchases_user` (`created_by`),
  KEY `fk_purchases_supplier` (`supplier_id`),
  CONSTRAINT `fk_purchases_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_purchases_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `fk_purchases_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,1,1,'PATRI SIHALOHO','PB-001-20260118-0001','','2026-01-18',118622.00,'0',1,'2026-01-19 04:44:03');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (2,'manager'),(1,'owner'),(3,'staff');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `qty` decimal(15,3) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sale_items_sale` (`sale_id`),
  KEY `fk_sale_items_product` (`product_id`),
  CONSTRAINT `fk_sale_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_sale_items_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sales_branch` (`branch_id`),
  KEY `fk_sales_user` (`created_by`),
  KEY `fk_sales_customer` (`customer_id`),
  CONSTRAINT `fk_sales_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `fk_sales_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_daily_material_demand`
--

DROP TABLE IF EXISTS `sppg_daily_material_demand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_daily_material_demand` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sppg_id` varchar(50) NOT NULL,
  `demand_date` date NOT NULL,
  `material_code` varchar(50) NOT NULL,
  `target_group` enum('anak','balita','remaja','dewasa','lansia') NOT NULL,
  `beneficiaries_count` int(11) NOT NULL,
  `total_quantity_grams` decimal(14,3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_demand_sppg_date` (`sppg_id`,`demand_date`),
  KEY `idx_demand_material` (`material_code`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_daily_material_demand`
--

LOCK TABLES `sppg_daily_material_demand` WRITE;
/*!40000 ALTER TABLE `sppg_daily_material_demand` DISABLE KEYS */;
INSERT INTO `sppg_daily_material_demand` VALUES (15,'SPPG_JAN2026','2026-01-01','CABAI_RAWIT_HIJAU','anak',1710,500.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(16,'SPPG_JAN2026','2026-01-01','CABAI_RAWIT_MERAH','anak',1710,500.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(17,'SPPG_JAN2026','2026-01-01','CABAI_MERAH','anak',1710,8000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(18,'SPPG_JAN2026','2026-01-01','TEPUNG_TERIGU','anak',1710,3000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(19,'SPPG_JAN2026','2026-01-01','KUNYIT_BUBUK','anak',1710,120.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(20,'SPPG_JAN2026','2026-01-01','MINYAK_GORENG_SAWIT','anak',1710,65000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(21,'SPPG_JAN2026','2026-01-01','PAKCOY','anak',1710,125000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(22,'SPPG_JAN2026','2026-01-01','BAWANG_PUTIH','anak',1710,4000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(23,'SPPG_JAN2026','2026-01-01','SAUS_TIRAM','anak',1710,266.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(24,'SPPG_JAN2026','2026-01-01','GARAM_HALUS','anak',1710,1500.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(25,'SPPG_JAN2026','2026-01-01','TEPUNG_MAIZENA','anak',1710,3000.000,'2026-01-17 19:43:29','2026-01-17 19:43:29'),(37,'SPPG_JAN2026_MINGGU1','2026-01-15','CABAI_RAWIT_HIJAU','anak',2316,1000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(38,'SPPG_JAN2026_MINGGU1','2026-01-15','CABAI_RAWIT_MERAH','anak',2316,1000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(39,'SPPG_JAN2026_MINGGU1','2026-01-15','CABAI_MERAH','anak',2316,8000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(40,'SPPG_JAN2026_MINGGU1','2026-01-15','TEPUNG_TERIGU','anak',2316,6000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(41,'SPPG_JAN2026_MINGGU1','2026-01-15','KUNYIT_BUBUK','anak',2316,120.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(42,'SPPG_JAN2026_MINGGU1','2026-01-15','PAKCOY','anak',2316,170000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(43,'SPPG_JAN2026_MINGGU1','2026-01-15','BAWANG_PUTIH','anak',2316,4000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(44,'SPPG_JAN2026_MINGGU1','2026-01-15','SAUS_TIRAM','anak',2316,532.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(45,'SPPG_JAN2026_MINGGU1','2026-01-15','GARAM_HALUS','anak',2316,1500.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(46,'SPPG_JAN2026_MINGGU1','2026-01-15','TEPUNG_MAIZENA','anak',2316,5000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(47,'SPPG_JAN2026_MINGGU1','2026-01-15','MELON','anak',2316,160000.000,'2026-01-17 20:03:08','2026-01-17 20:03:08'),(48,'SPPG_JAN2026_INVOICE','2026-01-15','BERAS_MEDIUM','anak',0,130000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(49,'SPPG_JAN2026_INVOICE','2026-01-15','IKAN_NILA','anak',0,260000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(50,'SPPG_JAN2026_INVOICE','2026-01-15','GARAM_KASAR','anak',0,3000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(51,'SPPG_JAN2026_INVOICE','2026-01-15','JERUK_NIPIS','anak',0,10000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(53,'SPPG_JAN2026_INVOICE','2026-01-15','BAWANG_MERAH','anak',0,6000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(55,'SPPG_JAN2026_INVOICE','2026-01-15','CABAI_RAWIT_HIJAU','anak',0,1000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(56,'SPPG_JAN2026_INVOICE','2026-01-15','CABAI_RAWIT_MERAH','anak',0,1000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(57,'SPPG_JAN2026_INVOICE','2026-01-15','CABAI_MERAH','anak',0,8000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(58,'SPPG_JAN2026_INVOICE','2026-01-15','JAHE','anak',0,1000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(59,'SPPG_JAN2026_INVOICE','2026-01-15','KEMIRI','anak',0,3000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(60,'SPPG_JAN2026_INVOICE','2026-01-15','TOMAT','anak',0,35000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(61,'SPPG_JAN2026_INVOICE','2026-01-15','BATANG_RIAS','anak',0,10000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(62,'SPPG_JAN2026_INVOICE','2026-01-15','ANDALIMAN','anak',0,1000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(65,'SPPG_JAN2026_INVOICE','2026-01-15','TEMPE','anak',0,1485000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(66,'SPPG_JAN2026_INVOICE','2026-01-15','TEPUNG_TERIGU','anak',0,6000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(67,'SPPG_JAN2026_INVOICE','2026-01-15','KUNYIT_BUBUK','anak',0,120.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(69,'SPPG_JAN2026_INVOICE','2026-01-15','PAKCOY','anak',0,170000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(70,'SPPG_JAN2026_INVOICE','2026-01-15','BAWANG_PUTIH','anak',0,4000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(71,'SPPG_JAN2026_INVOICE','2026-01-15','SAUS_TIRAM','anak',0,532.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(72,'SPPG_JAN2026_INVOICE','2026-01-15','GARAM_HALUS','anak',0,1500.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(73,'SPPG_JAN2026_INVOICE','2026-01-15','GULA_PASIR','anak',0,0.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(74,'SPPG_JAN2026_INVOICE','2026-01-15','TEPUNG_MAIZENA','anak',0,5000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(75,'SPPG_JAN2026_INVOICE','2026-01-15','MINYAK_GORENG_SAWIT','anak',0,0.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(76,'SPPG_JAN2026_INVOICE','2026-01-15','MELON','anak',0,160000.000,'2026-01-17 20:14:32','2026-01-17 20:14:32'),(77,'SPPG_JAN2026_INVOICE','2026-01-16','BERAS_MEDIUM','anak',0,140000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(78,'SPPG_JAN2026_INVOICE','2026-01-16','DAUN_JERUK','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(79,'SPPG_JAN2026_INVOICE','2026-01-16','SERAI','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(80,'SPPG_JAN2026_INVOICE','2026-01-16','BAWANG_PUTIH_BUBUK','anak',0,120.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(81,'SPPG_JAN2026_INVOICE','2026-01-16','GARAM_KASAR','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(82,'SPPG_JAN2026_INVOICE','2026-01-16','TELUR_AYAM_RAS','anak',0,390000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(86,'SPPG_JAN2026_INVOICE','2026-01-16','CABAI_RAWIT_MERAH','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(87,'SPPG_JAN2026_INVOICE','2026-01-16','DAUN_BAWANG','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(88,'SPPG_JAN2026_INVOICE','2026-01-16','KECAP_MANIS','anak',0,3591.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(90,'SPPG_JAN2026_INVOICE','2026-01-16','GARAM_HALUS','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(91,'SPPG_JAN2026_INVOICE','2026-01-16','GULA_PASIR','anak',0,0.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(94,'SPPG_JAN2026_INVOICE','2026-01-16','TAHU','anak',0,82500.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(95,'SPPG_JAN2026_INVOICE','2026-01-16','TEPUNG_TERIGU','anak',0,6000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(96,'SPPG_JAN2026_INVOICE','2026-01-16','KUNYIT_BUBUK','anak',0,0.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(97,'SPPG_JAN2026_INVOICE','2026-01-16','KETUMBAR_BUBUK','anak',0,0.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(99,'SPPG_JAN2026_INVOICE','2026-01-16','KANGKUNG','anak',0,160000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(100,'SPPG_JAN2026_INVOICE','2026-01-16','BAWANG_PUTIH','anak',0,1500.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(101,'SPPG_JAN2026_INVOICE','2026-01-16','BAWANG_MERAH','anak',0,2000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(102,'SPPG_JAN2026_INVOICE','2026-01-16','TERASI','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(103,'SPPG_JAN2026_INVOICE','2026-01-16','CABAI_MERAH','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(104,'SPPG_JAN2026_INVOICE','2026-01-16','SAUS_TIRAM','anak',0,1000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(105,'SPPG_JAN2026_INVOICE','2026-01-16','KALDU_BUBUK','anak',0,500.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(106,'SPPG_JAN2026_INVOICE','2026-01-16','MINYAK_GORENG_SAWIT','anak',0,0.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(107,'SPPG_JAN2026_INVOICE','2026-01-16','JERUK_BUAH','anak',0,165000.000,'2026-01-17 20:18:49','2026-01-17 20:18:49'),(108,'SPPG_JAN2026_INVOICE','2026-01-17','BERAS_MEDIUM','anak',0,140000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(109,'SPPG_JAN2026_INVOICE','2026-01-17','DAGING_AYAM','anak',0,238000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(110,'SPPG_JAN2026_INVOICE','2026-01-17','SERAI','anak',0,1000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(112,'SPPG_JAN2026_INVOICE','2026-01-17','BUMBU_RACIK_RENDANG','anak',0,6650.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(113,'SPPG_JAN2026_INVOICE','2026-01-17','DAUN_KUNYIT','anak',0,1000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(114,'SPPG_JAN2026_INVOICE','2026-01-17','SANTAN_KENTAL','anak',0,5500.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(115,'SPPG_JAN2026_INVOICE','2026-01-17','GARAM_KASAR','anak',0,1000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(121,'SPPG_JAN2026_INVOICE','2026-01-17','JAHE','anak',0,1000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(122,'SPPG_JAN2026_INVOICE','2026-01-17','KEMIRI','anak',0,3000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(124,'SPPG_JAN2026_INVOICE','2026-01-17','TEMPE','anak',0,1500000.000,'2026-01-17 20:21:21','2026-01-17 20:21:21'),(128,'SPPG_JAN2026_INVOICE','2026-01-17','CABAI_RAWIT_MERAH','anak',0,500.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(129,'SPPG_JAN2026_INVOICE','2026-01-17','DAUN_SALAM','anak',0,500.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(130,'SPPG_JAN2026_INVOICE','2026-01-17','KECAP_MANIS','anak',0,3325.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(135,'SPPG_JAN2026_INVOICE','2026-01-17','LABU_SIAM','anak',0,140000.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(136,'SPPG_JAN2026_INVOICE','2026-01-17','BAWANG_PUTIH','anak',0,1000.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(137,'SPPG_JAN2026_INVOICE','2026-01-17','BAWANG_MERAH','anak',0,1500.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(138,'SPPG_JAN2026_INVOICE','2026-01-17','CABAI_MERAH','anak',0,1500.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(139,'SPPG_JAN2026_INVOICE','2026-01-17','SAUS_TIRAM','anak',0,1000.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(140,'SPPG_JAN2026_INVOICE','2026-01-17','GARAM_HALUS','anak',0,500.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(141,'SPPG_JAN2026_INVOICE','2026-01-17','KALDU_BUBUK','anak',0,0.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(142,'SPPG_JAN2026_INVOICE','2026-01-17','MINYAK_GORENG_SAWIT','anak',0,0.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(143,'SPPG_JAN2026_INVOICE','2026-01-17','SEMANGKA','anak',0,155000.000,'2026-01-17 20:21:22','2026-01-17 20:21:22'),(144,'SPPG_JAN2026_INVOICE','2026-01-18','BERAS_MEDIUM','anak',0,130000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(145,'SPPG_JAN2026_INVOICE','2026-01-18','IKAN_NILA','anak',0,250000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(148,'SPPG_JAN2026_INVOICE','2026-01-18','CABAI_RAWIT_MERAH','anak',0,1000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(149,'SPPG_JAN2026_INVOICE','2026-01-18','CABAI_HIJAU','anak',0,10000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(150,'SPPG_JAN2026_INVOICE','2026-01-18','TOMAT','anak',0,35000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(151,'SPPG_JAN2026_INVOICE','2026-01-18','GARAM_KASAR','anak',0,2000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(152,'SPPG_JAN2026_INVOICE','2026-01-18','LADA','anak',0,120.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(153,'SPPG_JAN2026_INVOICE','2026-01-18','SERAI','anak',0,1000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(154,'SPPG_JAN2026_INVOICE','2026-01-18','DAUN_JERUK','anak',0,1000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(155,'SPPG_JAN2026_INVOICE','2026-01-18','DAUN_SALAM','anak',0,500.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(157,'SPPG_JAN2026_INVOICE','2026-01-18','TAHU','anak',0,82500.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(158,'SPPG_JAN2026_INVOICE','2026-01-18','TEPUNG_TERIGU','anak',0,4000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(159,'SPPG_JAN2026_INVOICE','2026-01-18','KUNYIT_BUBUK','anak',0,120.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(160,'SPPG_JAN2026_INVOICE','2026-01-18','KETUMBAR_BUBUK','anak',0,0.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(162,'SPPG_JAN2026_INVOICE','2026-01-18','SAWI_PUTIH','anak',0,150000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(163,'SPPG_JAN2026_INVOICE','2026-01-18','BAWANG_PUTIH','anak',0,1000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(164,'SPPG_JAN2026_INVOICE','2026-01-18','BAWANG_MERAH','anak',0,1500.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(165,'SPPG_JAN2026_INVOICE','2026-01-18','CABAI_MERAH','anak',0,1500.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(166,'SPPG_JAN2026_INVOICE','2026-01-18','SAUS_TIRAM','anak',0,1000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(167,'SPPG_JAN2026_INVOICE','2026-01-18','KALDU_BUBUK','anak',0,500.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(168,'SPPG_JAN2026_INVOICE','2026-01-18','MINYAK_GORENG_SAWIT','anak',0,0.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(169,'SPPG_JAN2026_INVOICE','2026-01-18','SALAK','anak',0,102000.000,'2026-01-17 20:23:41','2026-01-17 20:23:41'),(170,'SPPG_JAN2026_INVOICE','2026-01-19','DAGING_AYAM','anak',0,236000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(172,'SPPG_JAN2026_INVOICE','2026-01-19','TEPUNG_MAIZENA','anak',0,0.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(173,'SPPG_JAN2026_INVOICE','2026-01-19','TEPUNG_TERIGU','anak',0,65000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(174,'SPPG_JAN2026_INVOICE','2026-01-19','KECAP_ASIN','anak',0,532.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(175,'SPPG_JAN2026_INVOICE','2026-01-19','BAWANG_BOMBAI','anak',0,14000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(177,'SPPG_JAN2026_INVOICE','2026-01-19','KECAP_MANIS','anak',0,1995.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(178,'SPPG_JAN2026_INVOICE','2026-01-19','TOMAT','anak',0,10000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(179,'SPPG_JAN2026_INVOICE','2026-01-19','GULA_PASIR','anak',0,0.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(181,'SPPG_JAN2026_INVOICE','2026-01-19','KALDU_BUBUK','anak',0,500.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(183,'SPPG_JAN2026_INVOICE','2026-01-19','KENTANG','anak',0,147000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(184,'SPPG_JAN2026_INVOICE','2026-01-19','JAGUNG','anak',0,30000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(185,'SPPG_JAN2026_INVOICE','2026-01-19','WORTEL','anak',0,35000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(186,'SPPG_JAN2026_INVOICE','2026-01-19','BUNCIS','anak',0,35000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(187,'SPPG_JAN2026_INVOICE','2026-01-19','BAWANG_MERAH','anak',0,1000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(188,'SPPG_JAN2026_INVOICE','2026-01-19','BAWANG_PUTIH','anak',0,1000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(189,'SPPG_JAN2026_INVOICE','2026-01-19','CABAI_RAWIT_MERAH','anak',0,500.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(190,'SPPG_JAN2026_INVOICE','2026-01-19','SAUS_TIRAM','anak',0,0.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(191,'SPPG_JAN2026_INVOICE','2026-01-19','GARAM_HALUS','anak',0,1000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(192,'SPPG_JAN2026_INVOICE','2026-01-19','LADA','anak',0,120.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(193,'SPPG_JAN2026_INVOICE','2026-01-19','MINYAK_GORENG_SAWIT','anak',0,0.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(194,'SPPG_JAN2026_INVOICE','2026-01-19','DUKU','anak',0,120000.000,'2026-01-17 20:26:32','2026-01-17 20:26:32'),(195,'SPPG_JAN2026_INVOICE','2026-01-20','ROTI','anak',0,39000.000,'2026-01-17 20:26:42','2026-01-17 20:26:42'),(196,'SPPG_JAN2026_INVOICE','2026-01-20','TELUR_AYAM_RAS','anak',0,390000.000,'2026-01-17 20:26:42','2026-01-17 20:26:42'),(197,'SPPG_JAN2026_INVOICE','2026-01-20','SUSU_UHT_115','anak',0,96957.000,'2026-01-17 20:26:42','2026-01-17 20:26:42'),(198,'SPPG_JAN2026_INVOICE','2026-01-20','SUSU_UHT_125','anak',0,213465.000,'2026-01-17 20:26:42','2026-01-17 20:26:42'),(199,'SPPG_JAN2026_INVOICE','2026-01-20','PISANG','anak',0,148000.000,'2026-01-17 20:26:42','2026-01-17 20:26:42');
/*!40000 ALTER TABLE `sppg_daily_material_demand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_material_ai_products`
--

DROP TABLE IF EXISTS `sppg_material_ai_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_material_ai_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_code` varchar(50) NOT NULL,
  `ai_product_id` varchar(50) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ai_map_material` (`material_code`),
  KEY `idx_ai_map_product` (`ai_product_id`),
  KEY `idx_ai_map_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_material_ai_products`
--

LOCK TABLES `sppg_material_ai_products` WRITE;
/*!40000 ALTER TABLE `sppg_material_ai_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `sppg_material_ai_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_material_plu_mappings`
--

DROP TABLE IF EXISTS `sppg_material_plu_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_material_plu_mappings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_code` varchar(50) NOT NULL,
  `plu_number` varchar(10) NOT NULL,
  `mapping_type` enum('direct','approximation') NOT NULL DEFAULT 'direct',
  `conversion_factor` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sppg_mapping_material` (`material_code`),
  KEY `idx_sppg_mapping_plu` (`plu_number`),
  KEY `idx_sppg_mapping_primary` (`is_primary`),
  CONSTRAINT `fk_sppg_mapping_material` FOREIGN KEY (`material_code`) REFERENCES `sppg_materials` (`material_code`),
  CONSTRAINT `fk_sppg_mapping_plu` FOREIGN KEY (`plu_number`) REFERENCES `plu_codes` (`plu_number`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_material_plu_mappings`
--

LOCK TABLES `sppg_material_plu_mappings` WRITE;
/*!40000 ALTER TABLE `sppg_material_plu_mappings` DISABLE KEYS */;
INSERT INTO `sppg_material_plu_mappings` VALUES (3,'APEL_MERAH','4011','direct',1.0000,1,NULL,'2026-01-17 18:35:11','2026-01-17 18:35:11'),(4,'PISANG_CAVENDISH','4030','direct',1.0000,1,NULL,'2026-01-17 18:35:11','2026-01-17 18:35:11'),(11,'CABAI_MERAH','9001','direct',1.0000,1,NULL,'2026-01-17 19:43:50','2026-01-17 19:43:50'),(12,'KANGKUNG','9020','direct',1.0000,1,NULL,'2026-01-17 19:43:50','2026-01-17 19:43:50'),(13,'BAWANG_MERAH','4086','direct',1.0000,1,NULL,'2026-01-17 19:43:50','2026-01-17 19:43:50');
/*!40000 ALTER TABLE `sppg_material_plu_mappings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_materials`
--

DROP TABLE IF EXISTS `sppg_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_code` varchar(50) NOT NULL,
  `material_name` varchar(255) NOT NULL,
  `category` enum('B','K','M','O','D','T') NOT NULL,
  `subcategory` varchar(50) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `package_size` varchar(50) NOT NULL,
  `shelf_life_months` int(11) NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `protein_per_100g` decimal(5,2) DEFAULT NULL,
  `carb_per_100g` decimal(5,2) DEFAULT NULL,
  `fat_per_100g` decimal(5,2) DEFAULT NULL,
  `fiber_per_100g` decimal(5,2) DEFAULT NULL,
  `calories_per_100g` int(11) DEFAULT NULL,
  `vitamin_a_mcg` int(11) DEFAULT NULL,
  `vitamin_c_mcg` int(11) DEFAULT NULL,
  `iron_mcg` decimal(5,2) DEFAULT NULL,
  `calcium_mcg` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_code` (`material_code`),
  KEY `idx_sppg_material_code` (`material_code`),
  KEY `idx_sppg_material_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_materials`
--

LOCK TABLES `sppg_materials` WRITE;
/*!40000 ALTER TABLE `sppg_materials` DISABLE KEYS */;
INSERT INTO `sppg_materials` VALUES (1,'CABAI_MERAH','Cabai Merah','O','CABAI',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 18:34:59','2026-01-17 19:43:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'KANGKUNG','Kangkung Segar','O','SAYUR_HIJAU',NULL,'IKAT','1 IKAT',1,NULL,1,'2026-01-17 18:34:59','2026-01-17 18:34:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'APEL_MERAH','Apel Merah','O','BUAH',NULL,'KG','1 KG',2,NULL,1,'2026-01-17 18:34:59','2026-01-17 18:34:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'PISANG_CAVENDISH','Pisang Cavendish','O','BUAH',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 18:34:59','2026-01-17 18:34:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'BERAS_MEDIUM','Beras Medium','B','',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:20:59','2026-01-17 19:20:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'MINYAK_GORENG_SAWIT','Minyak Goreng Sawit','B','',NULL,'LITER','1 LITER',6,NULL,1,'2026-01-17 19:20:59','2026-01-17 19:20:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'TELUR_AYAM_RAS','Telur Ayam Ras','B','',NULL,'PCS','1 PCS',6,NULL,1,'2026-01-17 19:20:59','2026-01-17 19:20:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'GULA_PASIR','Gula Pasir','B','',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:20:59','2026-01-17 19:20:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'BAWANG_MERAH','Bawang Merah','B','',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:20:59','2026-01-17 19:20:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'IKAN_NILA','Ikan Nila','D','Ikan Segar',NULL,'KG','1 KG ± 3 ekor',1,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'GARAM_KASAR','Garam Kasar','B','Garam',NULL,'PAK','1 PAK',24,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'JERUK_NIPIS','Jeruk Nipis','B','Buah',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,'BAWANG_PUTIH','Bawang Putih','M','Bumbu',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'CABAI_RAWIT_HIJAU','Cabai Rawit Hijau','M','Bumbu',NULL,'KG','1 KG',3,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'CABAI_RAWIT_MERAH','Cabai Rawit Merah','M','Bumbu',NULL,'KG','1 KG',3,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'JAHE','Jahe','M','Bumbu',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,'KEMIRI','Kemiri','M','Bumbu',NULL,'KG','1 KG',12,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'TOMAT','Tomat','B','Sayur',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'BATANG_RIAS','Batang Rias','M','Bumbu',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'ANDALIMAN','Andaliman','M','Bumbu',NULL,'KG','1 KG',6,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'GARAM_HALUS','Garam Halus','B','Garam',NULL,'BUNGKUS','1 BUNGKUS',24,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'TEMPE','Tempe','O','Protein Nabati',NULL,'PAPAN','1 PAPAN',5,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,'TEPUNG_TERIGU','Tepung Terigu','B','Tepung',NULL,'KG','1 KG',12,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,'KUNYIT_BUBUK','Kunyit Bubuk','M','Bumbu',NULL,'RENCENG','1 RENCENG (12 sachet)',12,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,'PAKCOY','Pakcoy','B','Sayur',NULL,'KG','1 KG',1,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,'SAUS_TIRAM','Saus Tiram','M','Saus',NULL,'BOTOL','1 BOTOL',12,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,'TEPUNG_MAIZENA','Tepung Maizena','B','Tepung',NULL,'KG','1 KG',12,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,'MELON','Melon','B','Buah',NULL,'KG','1 KG',0,NULL,1,'2026-01-17 19:30:13','2026-01-17 19:30:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `sppg_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_menu_items`
--

DROP TABLE IF EXISTS `sppg_menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_menu_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) unsigned NOT NULL,
  `material_code` varchar(50) NOT NULL,
  `quantity_grams_per_portion` decimal(10,3) NOT NULL,
  `is_optional` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_menu_items_menu` (`menu_id`),
  KEY `idx_menu_items_material` (`material_code`),
  CONSTRAINT `sppg_menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `sppg_menus` (`id`),
  CONSTRAINT `sppg_menu_items_ibfk_2` FOREIGN KEY (`material_code`) REFERENCES `sppg_materials` (`material_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_menu_items`
--

LOCK TABLES `sppg_menu_items` WRITE;
/*!40000 ALTER TABLE `sppg_menu_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sppg_menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_menu_logs`
--

DROP TABLE IF EXISTS `sppg_menu_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_menu_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sppg_id` varchar(50) NOT NULL,
  `menu_date` date NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `menu_id` bigint(20) unsigned NOT NULL,
  `target_group` enum('anak','balita','remaja','dewasa','lansia') NOT NULL,
  `portions` int(11) NOT NULL,
  `beneficiaries_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_menu_logs_sppg_date` (`sppg_id`,`menu_date`),
  KEY `idx_menu_logs_menu` (`menu_id`),
  KEY `idx_menu_logs_target_group` (`target_group`),
  CONSTRAINT `sppg_menu_logs_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `sppg_menus` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_menu_logs`
--

LOCK TABLES `sppg_menu_logs` WRITE;
/*!40000 ALTER TABLE `sppg_menu_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sppg_menu_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sppg_menus`
--

DROP TABLE IF EXISTS `sppg_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sppg_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(50) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_group` enum('anak','balita','remaja','dewasa','lansia') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_code` (`menu_code`),
  KEY `idx_menus_code` (`menu_code`),
  KEY `idx_menus_target_group` (`target_group`),
  KEY `idx_menus_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sppg_menus`
--

LOCK TABLES `sppg_menus` WRITE;
/*!40000 ALTER TABLE `sppg_menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `sppg_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_orang` int(10) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uk_user_orang` (`id_orang`),
  KEY `fk_user_role` (`role_id`),
  KEY `fk_user_branch` (`branch_id`),
  CONSTRAINT `fk_user_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_user_orang` FOREIGN KEY (`id_orang`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'owner','owner@gmail.com','$2y$10$6wOHl3X9A83RedKD6dMEAepIHK1skKgSuxqWQxQw6pe/AiBsi3L.m',1,1,1,'2026-01-19 04:43:46','2026-01-17 18:58:34','2026-01-19 04:43:46');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `owner_id` int(10) unsigned DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `regency_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `village_id` int(10) unsigned DEFAULT NULL,
  `street_address` text DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_role` (`role_id`),
  KEY `fk_users_branch` (`branch_id`),
  CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_sppg_material_demand_monthly`
--

DROP TABLE IF EXISTS `v_sppg_material_demand_monthly`;
/*!50001 DROP VIEW IF EXISTS `v_sppg_material_demand_monthly`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_sppg_material_demand_monthly` AS SELECT
 1 AS `sppg_id`,
  1 AS `demand_month`,
  1 AS `month_start_date`,
  1 AS `month_end_date`,
  1 AS `material_code`,
  1 AS `material_name`,
  1 AS `target_group`,
  1 AS `total_quantity_kg`,
  1 AS `total_beneficiaries` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_sppg_material_demand_weekly`
--

DROP TABLE IF EXISTS `v_sppg_material_demand_weekly`;
/*!50001 DROP VIEW IF EXISTS `v_sppg_material_demand_weekly`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_sppg_material_demand_weekly` AS SELECT
 1 AS `sppg_id`,
  1 AS `demand_week`,
  1 AS `week_start_date`,
  1 AS `week_end_date`,
  1 AS `material_code`,
  1 AS `material_name`,
  1 AS `target_group`,
  1 AS `total_quantity_kg`,
  1 AS `total_beneficiaries` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_sppg_material_plu_nutrition`
--

DROP TABLE IF EXISTS `v_sppg_material_plu_nutrition`;
/*!50001 DROP VIEW IF EXISTS `v_sppg_material_plu_nutrition`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_sppg_material_plu_nutrition` AS SELECT
 1 AS `material_code`,
  1 AS `material_name`,
  1 AS `category`,
  1 AS `subcategory`,
  1 AS `unit`,
  1 AS `package_size`,
  1 AS `plu_number`,
  1 AS `plu_product_name`,
  1 AS `plu_category`,
  1 AS `plu_subcategory`,
  1 AS `protein_per_100g`,
  1 AS `carb_per_100g`,
  1 AS `fat_per_100g`,
  1 AS `fiber_per_100g`,
  1 AS `calories_per_100g` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_sppg_material_demand_monthly`
--

/*!50001 DROP VIEW IF EXISTS `v_sppg_material_demand_monthly`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`::1` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sppg_material_demand_monthly` AS select `d`.`sppg_id` AS `sppg_id`,date_format(`d`.`demand_date`,'%Y-%m') AS `demand_month`,min(`d`.`demand_date`) AS `month_start_date`,max(`d`.`demand_date`) AS `month_end_date`,`d`.`material_code` AS `material_code`,`sm`.`material_name` AS `material_name`,`d`.`target_group` AS `target_group`,sum(`d`.`total_quantity_grams`) / 1000 AS `total_quantity_kg`,sum(`d`.`beneficiaries_count`) AS `total_beneficiaries` from (`sppg_daily_material_demand` `d` left join `sppg_materials` `sm` on(`sm`.`material_code` = `d`.`material_code`)) group by `d`.`sppg_id`,date_format(`d`.`demand_date`,'%Y-%m'),`d`.`material_code`,`sm`.`material_name`,`d`.`target_group` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_sppg_material_demand_weekly`
--

/*!50001 DROP VIEW IF EXISTS `v_sppg_material_demand_weekly`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`::1` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sppg_material_demand_weekly` AS select `d`.`sppg_id` AS `sppg_id`,yearweek(`d`.`demand_date`,3) AS `demand_week`,min(`d`.`demand_date`) AS `week_start_date`,max(`d`.`demand_date`) AS `week_end_date`,`d`.`material_code` AS `material_code`,`sm`.`material_name` AS `material_name`,`d`.`target_group` AS `target_group`,sum(`d`.`total_quantity_grams`) / 1000 AS `total_quantity_kg`,sum(`d`.`beneficiaries_count`) AS `total_beneficiaries` from (`sppg_daily_material_demand` `d` left join `sppg_materials` `sm` on(`sm`.`material_code` = `d`.`material_code`)) group by `d`.`sppg_id`,yearweek(`d`.`demand_date`,3),`d`.`material_code`,`sm`.`material_name`,`d`.`target_group` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_sppg_material_plu_nutrition`
--

/*!50001 DROP VIEW IF EXISTS `v_sppg_material_plu_nutrition`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`::1` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sppg_material_plu_nutrition` AS select `sm`.`material_code` AS `material_code`,`sm`.`material_name` AS `material_name`,`sm`.`category` AS `category`,`sm`.`subcategory` AS `subcategory`,`sm`.`unit` AS `unit`,`sm`.`package_size` AS `package_size`,`mpm`.`plu_number` AS `plu_number`,`pc`.`product_name` AS `plu_product_name`,`pc`.`category` AS `plu_category`,`pc`.`subcategory` AS `plu_subcategory`,`sm`.`protein_per_100g` AS `protein_per_100g`,`sm`.`carb_per_100g` AS `carb_per_100g`,`sm`.`fat_per_100g` AS `fat_per_100g`,`sm`.`fiber_per_100g` AS `fiber_per_100g`,`sm`.`calories_per_100g` AS `calories_per_100g` from ((`sppg_materials` `sm` left join `sppg_material_plu_mappings` `mpm` on(`mpm`.`material_code` = `sm`.`material_code` and `mpm`.`is_primary` = 1)) left join `plu_codes` `pc` on(`pc`.`plu_number` = `mpm`.`plu_number`)) where `sm`.`is_active` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-19  5:15:53

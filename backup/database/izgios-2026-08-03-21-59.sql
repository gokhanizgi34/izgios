-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: izgios
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `arac_hasar_fotograflari`
--

DROP TABLE IF EXISTS `arac_hasar_fotograflari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arac_hasar_fotograflari` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `arac_hasari_id` bigint unsigned NOT NULL,
  `dosya_yolu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `arac_hasar_fotograflari_arac_hasari_id_foreign` (`arac_hasari_id`),
  CONSTRAINT `arac_hasar_fotograflari_arac_hasari_id_foreign` FOREIGN KEY (`arac_hasari_id`) REFERENCES `arac_hasarlari` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arac_hasar_fotograflari`
--

LOCK TABLES `arac_hasar_fotograflari` WRITE;
/*!40000 ALTER TABLE `arac_hasar_fotograflari` DISABLE KEYS */;
/*!40000 ALTER TABLE `arac_hasar_fotograflari` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arac_hasarlari`
--

DROP TABLE IF EXISTS `arac_hasarlari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arac_hasarlari` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `arac_id` bigint unsigned NOT NULL,
  `servis_id` bigint unsigned DEFAULT NULL,
  `parca_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `konum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `arac_hasarlari_arac_id_foreign` (`arac_id`),
  KEY `arac_hasarlari_servis_id_foreign` (`servis_id`),
  CONSTRAINT `arac_hasarlari_arac_id_foreign` FOREIGN KEY (`arac_id`) REFERENCES `araclar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `arac_hasarlari_servis_id_foreign` FOREIGN KEY (`servis_id`) REFERENCES `servisler` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arac_hasarlari`
--

LOCK TABLES `arac_hasarlari` WRITE;
/*!40000 ALTER TABLE `arac_hasarlari` DISABLE KEYS */;
/*!40000 ALTER TABLE `arac_hasarlari` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `araclar`
--

DROP TABLE IF EXISTS `araclar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `araclar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `musteri_id` bigint unsigned DEFAULT NULL,
  `plaka` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marka` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_yili` year DEFAULT NULL,
  `kilometre` int DEFAULT NULL,
  `sase_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motor_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `yakit_tipi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vites` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `qr_token` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_created_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `araclar_plaka_unique` (`plaka`),
  UNIQUE KEY `araclar_qr_token_unique` (`qr_token`),
  KEY `araclar_musteri_id_foreign` (`musteri_id`),
  CONSTRAINT `araclar_musteri_id_foreign` FOREIGN KEY (`musteri_id`) REFERENCES `musteris` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `araclar`
--

LOCK TABLES `araclar` WRITE;
/*!40000 ALTER TABLE `araclar` DISABLE KEYS */;
INSERT INTO `araclar` VALUES (18,NULL,'34QR001','RENAULT','CLIO',2025,10000,NULL,NULL,'Benzin','Otomatik',NULL,'68fc75b9-593f-498f-82fa-1e217e876f4a','2026-08-02 19:38:09','2026-08-02 19:38:09','2026-08-02 19:38:09'),(19,9,'34ASD342','BUICK','ENCORE',2022,123,'123','123','Benzin','Manuel','123','1520b903-c452-4864-875c-79902d42d1ce','2026-08-02 20:37:43','2026-08-02 20:37:43','2026-08-02 20:37:43'),(20,18,'34KL1081001','CADILLAC','ESCALADE',2023,123,'123','123','Benzin','Otomatik',NULL,'f25bb6ad-17a8-4ccd-b6b2-31ca6aa81758','2026-08-02 20:51:38','2026-08-02 20:51:38','2026-08-02 20:51:38'),(21,NULL,'34ASD3400991111','VOLKSWAGEN','TRANSPORTER',2024,123,'A15165','2626','Benzin','Otomatik','1111','8d7e4b04-0c3e-4c27-a788-e13372265e6c','2026-08-02 21:07:39','2026-08-02 21:07:39','2026-08-03 10:58:02'),(23,8,'34ERT1200','OPEL','MOKKA',2023,125000,'123','123','Dizel','Otomatik','OKU','d4392507-8890-4bf8-8492-583667f8244d','2026-08-03 08:32:52','2026-08-03 08:32:52','2026-08-03 08:32:52'),(25,8,'34ACS12499','ALFA ROMEO','GIULIA',2024,123,'123','123','Benzin','Manuel','123123','88405f65-9947-4634-aa15-5252aae5b0b0','2026-08-03 10:15:45','2026-08-03 10:15:45','2026-08-03 10:58:21');
/*!40000 ALTER TABLE `araclar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'create_musteris_table',2),(6,'2026_07_28_101617_add_tc_kimlik_no_to_musteris_table',3),(7,'2026_07_28_151400_create_araclar_table',4),(8,'2026_07_28_151500_create_servisler_table',4),(9,'160000_create_arac_hasarlari_table',5),(10,'160100_create_arac_hasar_fotograflari_table',5),(11,'2026_08_01_000001_make_musteri_id_nullable_on_araclar_table',6),(12,'2026_08_02_220004_add_qr_fields_to_araclar_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `musteris`
--

DROP TABLE IF EXISTS `musteris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `musteris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ad_soyad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tc_kimlik_no` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefon2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adres` text COLLATE utf8mb4_unicode_ci,
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `musteris`
--

LOCK TABLES `musteris` WRITE;
/*!40000 ALTER TABLE `musteris` DISABLE KEYS */;
INSERT INTO `musteris` VALUES (8,'YILDIRAY İZGİ','12345678901','55555555555555555','55555555555555','YYQYYY@YYY.COM','ASDNASDNJJASDNMIPASJ','NCSIDCNS',1,'2026-07-31 13:27:16','2026-07-31 13:27:16'),(9,'GİRAY BATUR İZGİ','12345679000','05374916936','ASDASD','gokhanizgi@gmail.com','MİMAR SİNAN MAHALLESİ MİMAR SİNAN CADDESİ KILIÇLAR SOKAK NO 24 DAİRE 3','LASDLA',1,'2026-07-31 16:26:53','2026-07-31 17:10:23'),(18,'EKREM İZGİ','22222222','5374916936','02122121212','EKREM@EKREM.COM','SDASDASDAS1111111112222222222222222222222222222AAAAAAAAAAA','ASDADAS1111111111111111112222222222222222A22222222SDAAAAAAAAAAAAAAAAAAA',1,'2026-08-01 07:28:02','2026-08-03 13:11:50'),(25,'YUSUF EFE İZGİ','12300005856','5555558888','888888888','usuguasl@ajlnflasd.com','ASLDĞKASDASDSA','ASDASD',1,'2026-08-03 13:12:38','2026-08-03 13:12:38'),(26,'YUSUF EFE İZGİ','12300005856','5555558888','888888888','usuguasl@ajlnflasd.com','ASLDĞKASDASDSA','ASDASD',1,'2026-08-03 13:12:38','2026-08-03 13:12:38');
/*!40000 ALTER TABLE `musteris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servisler`
--

DROP TABLE IF EXISTS `servisler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servisler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `musteri_id` bigint unsigned NOT NULL,
  `arac_id` bigint unsigned NOT NULL,
  `servis_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sikayet` text COLLATE utf8mb4_unicode_ci,
  `yapilan_islem` text COLLATE utf8mb4_unicode_ci,
  `kullanilan_parca` text COLLATE utf8mb4_unicode_ci,
  `parca_tutari` decimal(10,2) NOT NULL DEFAULT '0.00',
  `iscilik_tutari` decimal(10,2) NOT NULL DEFAULT '0.00',
  `toplam_tutar` decimal(10,2) NOT NULL DEFAULT '0.00',
  `durum` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bekliyor',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `servisler_servis_no_unique` (`servis_no`),
  KEY `servisler_musteri_id_foreign` (`musteri_id`),
  KEY `servisler_arac_id_foreign` (`arac_id`),
  CONSTRAINT `servisler_arac_id_foreign` FOREIGN KEY (`arac_id`) REFERENCES `araclar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `servisler_musteri_id_foreign` FOREIGN KEY (`musteri_id`) REFERENCES `musteris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servisler`
--

LOCK TABLES `servisler` WRITE;
/*!40000 ALTER TABLE `servisler` DISABLE KEYS */;
/*!40000 ALTER TABLE `servisler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('vNsEud6jBwYkuZSlRBuu4AKJnaqq2RAgEhugX6ST',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJycmZYN2xFcHpxNGVvQkRYWHRibE95QmVuT2FwcjdDV1haNFE1Q2dMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9tdXN0ZXJpbGVyIiwicm91dGUiOiJtdXN0ZXJpbGVyLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1785783523);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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

-- Dump completed on 2026-08-03 21:59:19

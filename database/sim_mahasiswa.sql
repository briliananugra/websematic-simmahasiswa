
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
DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_log` (
  `id_audit` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pengguna` bigint unsigned DEFAULT NULL,
  `tabel_nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` enum('LOGIN','LOGOUT','INSERT','UPDATE','DELETE','VIEW') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `data_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `waktu` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_audit`),
  KEY `idx_audit_pengguna` (`id_pengguna`),
  KEY `idx_audit_tabel_record` (`tabel_nama`,`record_id`),
  KEY `idx_audit_waktu` (`waktu`),
  CONSTRAINT `fk_audit_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `audit_log_chk_1` CHECK (json_valid(`data_lama`)),
  CONSTRAINT `audit_log_chk_2` CHECK (json_valid(`data_baru`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES (1,6,'pengguna','6','LOGIN',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-28 10:01:52');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fakultas` (
  `id_fakultas` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_universitas` bigint unsigned NOT NULL,
  `kode_fakultas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_fakultas` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_fakultas`),
  UNIQUE KEY `uk_fakultas_univ_kode` (`id_universitas`,`kode_fakultas`),
  CONSTRAINT `fk_fakultas_universitas` FOREIGN KEY (`id_universitas`) REFERENCES `universitas` (`id_universitas`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `fakultas` WRITE;
/*!40000 ALTER TABLE `fakultas` DISABLE KEYS */;
INSERT INTO `fakultas` VALUES (1,1,'FT','Fakultas Teknik','2026-09-28 02:53:26','2026-09-28 02:53:26');
/*!40000 ALTER TABLE `fakultas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa` (
  `id_mahasiswa` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pengguna` bigint unsigned DEFAULT NULL,
  `id_program_studi` bigint unsigned NOT NULL,
  `npm` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mahasiswa` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `status_mahasiswa` enum('Aktif','Cuti','Lulus','Mengundurkan Diri','Drop Out','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mahasiswa`),
  UNIQUE KEY `uk_mahasiswa_npm` (`npm`),
  UNIQUE KEY `uk_mahasiswa_pengguna` (`id_pengguna`),
  KEY `idx_mahasiswa_prodi` (`id_program_studi`),
  KEY `idx_mahasiswa_nama` (`nama_mahasiswa`),
  KEY `idx_mahasiswa_status` (`status_mahasiswa`),
  CONSTRAINT `fk_mahasiswa_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_mahasiswa_prodi` FOREIGN KEY (`id_program_studi`) REFERENCES `program_studi` (`id_program_studi`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES (1,5,1,'20260001','Contoh Mahasiswa','L','Bengkulu','2005-01-15','2026-09-01','Bengkulu','Aktif','2026-09-28 02:53:27','2026-09-28 02:53:27');
/*!40000 ALTER TABLE `mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `pengguna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengguna` (
  `id_pengguna` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_role` smallint unsigned NOT NULL,
  `id_universitas` bigint unsigned DEFAULT NULL,
  `id_fakultas` bigint unsigned DEFAULT NULL,
  `id_program_studi` bigint unsigned DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aktif` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_pengguna_role` (`id_role`),
  KEY `idx_pengguna_universitas` (`id_universitas`),
  KEY `idx_pengguna_fakultas` (`id_fakultas`),
  KEY `idx_pengguna_prodi` (`id_program_studi`),
  CONSTRAINT `fk_pengguna_fakultas` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id_fakultas`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pengguna_prodi` FOREIGN KEY (`id_program_studi`) REFERENCES `program_studi` (`id_program_studi`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pengguna_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pengguna_universitas` FOREIGN KEY (`id_universitas`) REFERENCES `universitas` (`id_universitas`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` VALUES (2,2,1,1,1,'operator.ti','$2y$10$ZAlToJRVxQtnW/vjD9ZOQuqb7g.9GvoabAM.q0x7kdbKzdtZxuFHi','Operator Program Studi Teknik Informatika','operator.ti@umb.ac.id',NULL,'Aktif','2026-11-10 14:10:54','2026-09-28 02:53:26','2026-11-10 07:10:54'),(3,3,1,1,NULL,'dekanat.ft','$2y$10$ZAlToJRVxQtnW/vjD9ZOQuqb7g.9GvoabAM.q0x7kdbKzdtZxuFHi','Operator Dekanat Fakultas Teknik','dekanat.ft@umb.ac.id',NULL,'Aktif','2026-11-10 14:11:37','2026-09-28 02:53:26','2026-11-10 07:11:37'),(4,4,1,NULL,NULL,'rektorat','$2y$10$ZAlToJRVxQtnW/vjD9ZOQuqb7g.9GvoabAM.q0x7kdbKzdtZxuFHi','Operator Rektorat','rektorat@umb.ac.id',NULL,'Aktif','2026-11-10 14:11:54','2026-09-28 02:53:27','2026-11-10 07:11:54'),(5,5,1,1,1,'20260001','$2y$10$ZAlToJRVxQtnW/vjD9ZOQuqb7g.9GvoabAM.q0x7kdbKzdtZxuFHi','Contoh Mahasiswa','20260001@student.umb.ac.id',NULL,'Aktif','2026-11-10 14:12:15','2026-09-28 02:53:27','2026-11-10 07:12:15'),(6,1,1,NULL,NULL,'Admin','$2y$10$ZAlToJRVxQtnW/vjD9ZOQuqb7g.9GvoabAM.q0x7kdbKzdtZxuFHi','Admin SIM Mhs UMB','harrywitriyono@umb.ac.id',NULL,'Aktif','2026-11-10 10:56:32','2026-09-28 03:01:42','2026-11-10 03:56:32');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `program_studi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_studi` (
  `id_program_studi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_fakultas` bigint unsigned NOT NULL,
  `kode_program_studi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_program_studi` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S1',
  `status_aktif` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_program_studi`),
  UNIQUE KEY `uk_prodi_fakultas_kode` (`id_fakultas`,`kode_program_studi`),
  CONSTRAINT `fk_prodi_fakultas` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id_fakultas`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `program_studi` WRITE;
/*!40000 ALTER TABLE `program_studi` DISABLE KEYS */;
INSERT INTO `program_studi` VALUES (1,1,'TI','Teknik Informatika','S1','Aktif','2026-09-28 02:53:26','2026-09-28 02:53:26');
/*!40000 ALTER TABLE `program_studi` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_role` smallint unsigned NOT NULL AUTO_INCREMENT,
  `kode_role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `kode_role` (`kode_role`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','Admin','Mengelola seluruh data dan konfigurasi aplikasi','2026-09-28 02:53:26'),(2,'OPERATOR_PRODI','Operator Program Studi','Mengelola mahasiswa pada Program Studi yang menjadi kewenangannya','2026-09-28 02:53:26'),(3,'DEKANAT','Dekanat','Melihat data mahasiswa pada Fakultas yang menjadi kewenangannya','2026-09-28 02:53:26'),(4,'REKTORAT','Rektorat','Melihat data mahasiswa pada tingkat universitas','2026-09-28 02:53:26'),(5,'MAHASISWA','Mahasiswa','Melihat data pribadi/rekord mahasiswa sendiri','2026-09-28 02:53:26');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `universitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universitas` (
  `id_universitas` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_universitas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_universitas` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slogan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_universitas`),
  UNIQUE KEY `kode_universitas` (`kode_universitas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `universitas` WRITE;
/*!40000 ALTER TABLE `universitas` DISABLE KEYS */;
INSERT INTO `universitas` VALUES (1,'UMB','Universitas Muhammadiyah Bengkulu',NULL,'Jl. Bali, Kampung Bali','Bengkulu','Bengkulu',NULL,NULL,NULL,NULL,'2026-09-28 02:53:26','2026-11-09 17:19:42');
/*!40000 ALTER TABLE `universitas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


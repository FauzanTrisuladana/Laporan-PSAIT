/*
SQLyog Community v13.1.9 (64 bit)
MySQL - 10.4.22-MariaDB : Database - sait_new_a1_6
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`sait_new_a1_6` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `sait_new_a1_6`;

/*Table structure for table `koperasi` */

DROP TABLE IF EXISTS `anggota`;
DROP TABLE IF EXISTS `koperasi`;

CREATE TABLE `koperasi` (
  `id_koperasi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_koperasi` varchar(100) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `telp` varchar(30) DEFAULT NULL,
  `tgl_berdiri` date DEFAULT NULL,
  PRIMARY KEY (`id_koperasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `anggota` */

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL AUTO_INCREMENT,
  `id_koperasi` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `no_hp` varchar(30) DEFAULT NULL,
  `tanggal_gabung` date DEFAULT NULL,
  PRIMARY KEY (`id_anggota`),
  KEY `idx_anggota_id_koperasi` (`id_koperasi`),
  CONSTRAINT `fk_anggota_koperasi` FOREIGN KEY (`id_koperasi`) REFERENCES `koperasi` (`id_koperasi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `koperasi` */

INSERT INTO `koperasi` (`id_koperasi`, `nama_koperasi`, `alamat`, `telp`, `tgl_berdiri`) VALUES
(1,'Koperasi Sejahtera','Surabaya','031-555000','2010-05-12'),
(2,'Koperasi Makmur','Yogyakarta','0274-777888','2015-08-01');

/*Data for the table `anggota` */

INSERT INTO `anggota` (`id_anggota`, `id_koperasi`, `nama`, `alamat`, `no_hp`, `tanggal_gabung`) VALUES
(1,1,'Andi','Surabaya','081234567890','2022-01-10'),
(2,1,'Dwi','Sidoarjo','081298765432','2022-02-15'),
(3,2,'David','Sleman','082111223344','2023-06-02'),
(4,2,'Paul','Bantul','082233445566','2023-07-18');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Cuplikan diambil dari file resource/all.sql (mysqldump --all-databases)
-- Bagian ini menunjukkan bahwa database sait_db sudah ada di Master,
-- sehingga jika replikasi dimulai setelah database dibuat, maka Slave tidak
-- otomatis mendapatkan database ini tanpa proses initial seeding (dump & import).

-- Current Database: `sait_db`

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `sait_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `sait_db`;

-- Table structure for table `mahasiswa`

DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
  `id_mhs` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_mhs`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `mahasiswa`

INSERT INTO `mahasiswa` VALUES
(1,'Andi','Surabaya'),
(2,'Dwi','Semarang'),
(3,'David','Sleman'),
(4,'Paul','Bantul');

-- Current Database: `uji_coba`

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `uji_coba` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `uji_coba`;

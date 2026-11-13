/* =====================================================================
   HealthyHabit - Skema & Data
   Sistem Pembentukan Kebiasaan Hidup Sehat Berbasis Web Native
   (PHP Native + MySQL)

   Hasil dump langsung dari database aplikasi `healthyhabit1`
   sehingga seluruh struktur tabel, foreign key, dan data sudah
   sinkron dengan aplikasi yang berjalan.

   Cara impor:
     1. Jalankan Apache & MySQL dari XAMPP Control Panel
     2. Buka http://localhost/phpmyadmin
     3. Menu "Import" -> pilih file ini -> Go
     4. Sesuaikan DB_NAME di app/config/config.php bila perlu

   Login default (password sudah ter-hash bcrypt):
     - Admin : admin      / admin123
     - User  : andi_sehat / user123
   ===================================================================== */

CREATE DATABASE IF NOT EXISTS `healthyhabit1`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

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
DROP TABLE IF EXISTS `artikel_kesehatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_kesehatan` (
  `id_artikel` int(11) NOT NULL AUTO_INCREMENT,
  `id_kategori_artikel` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `judul_artikel` varchar(180) NOT NULL,
  `isi_artikel` text NOT NULL,
  `foto_artikel` varchar(255) DEFAULT NULL,
  `tanggal_terbit` date NOT NULL,
  PRIMARY KEY (`id_artikel`),
  KEY `fk_artikel_kategori` (`id_kategori_artikel`),
  KEY `fk_artikel_admin` (`id_admin`),
  CONSTRAINT `fk_artikel_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_artikel_kategori` FOREIGN KEY (`id_kategori_artikel`) REFERENCES `kategori_artikel` (`id_kategori_artikel`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `artikel_kesehatan` DISABLE KEYS */;
INSERT INTO `artikel_kesehatan` VALUES (1,1,4,'Panduan Gizi Seimbang: Isi Piringku untuk Keluarga Sehat','Gizi seimbang bukan berarti mahal. Konsep \"Isi Piringku\" dari Kementerian Kesehatan membagi satu piring makan menjadi 50% sayur dan buah, serta 50% makanan pokok dan lauk-pauk.\r\n\r\nPastikan setengah piring berisi sayur dan buah dengan warna beragam. Semakin beragam warna, semakin lengkap mikronutrien yang masuk ke tubuh Anda.\r\n\r\nLengkapi pula dengan aktivitas fisik minimal 30 menit sehari, minum air putih 8 gelas, dan cuci tangan sebelum makan. Kebiasaan kecil yang konsisten akan memberi hasil besar bagi kesehatan jangka panjang.','20261019_130052_cb63deda.jpg','2026-10-22'),(2,2,4,'5 Olahraga Ringan yang Bisa Dilakukan di Rumah','Tidak semua olahraga membutuhkan gym. Berikut lima aktivitas yang bisa dilakukan di rumah tanpa alat khusus:\r\n\r\n1. Jalan cepat di tempat selama 15-20 menit.\r\n2. Push-up dengan variasi lutut menyentuh lantai untuk pemula.\r\n3. Squat untuk memperkuat otot kaki dan bokong.\r\n4. Plank untuk melatih otot inti (core) selama 30 detik hingga 2 menit.\r\n5. Stretching ringan untuk melenturkan otot setelah beraktivitas.\r\n\r\nLakukan 3-5 kali seminggu dan lakukan pemanasan sebelum mulai agar terhindar dari cedera.','20261019_130406_d91c24d5.jpg','2026-10-21'),(3,3,4,'Mengelola Stres dengan Teknik Pernapasan 4-7-8','Stres berkepanjangan berdampak buruk pada tekanan darah, pola tidur, dan sistem imun. Salah satu teknik sederhana dan efektif adalah pernapasan 4-7-8.\r\n\r\nCaranya: tarik napas melalui hidung selama 4 detik, tahan selama 7 detik, lalu buang perlahan melalui mulut selama 8 detik. Ulangi 4 siklus, dua kali sehari.\r\n\r\nKombinasikan dengan jurnal syukur harian dan kurangi konsumsi kafein berlebih agar kualitas tidur Anda semakin baik.','20261019_152715_1b0624a5.jpg','2026-03-15'),(4,4,4,'Mulai Hari dengan 6 Kebiasaan Kecil yang Berdampak Besar','Kebiasaan sehat tidak harus dimulai dengan perubahan besar. Mulailah dari 6 hal berikut:\r\n\r\nMinum segelas air putih setelah bangun tidur, meregangkan badan 5 menit, sarapan tinggi protein, berjalan pagi 10 menit, menulis satu target harian, dan tidur sebelum pukul 23.00.\r\n\r\nCatat progres Anda setiap hari. Setelah 21 hari, kebiasaan ini akan terasa alami dan mampu menurunkan risiko penyakit degeneratif secara signifikan.','20261019_130501_641045b3.jpg','2026-10-20');
/*!40000 ALTER TABLE `artikel_kesehatan` ENABLE KEYS */;
DROP TABLE IF EXISTS `kalkulator_bmi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalkulator_bmi` (
  `id_bmi` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `umur` int(11) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `berat_badan` decimal(5,2) NOT NULL,
  `tinggi_badan` decimal(5,2) NOT NULL,
  `nilai_bmi` decimal(5,2) NOT NULL,
  `kategori_bmi` varchar(30) NOT NULL,
  `estimasi_kalori` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_bmi`),
  KEY `fk_bmi_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_bmi_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `users` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `kalkulator_bmi` DISABLE KEYS */;
INSERT INTO `kalkulator_bmi` VALUES (1,1,24,'L',62.50,170.00,21.60,'Normal',2359),(2,2,27,'P',55.00,160.00,21.50,'Normal',1896),(3,3,19,'P',46.00,158.60,18.30,'Kurus',1853),(4,4,29,'P',90.00,100.00,90.00,'Obesitas',1889);
/*!40000 ALTER TABLE `kalkulator_bmi` ENABLE KEYS */;
DROP TABLE IF EXISTS `kandungan_gizi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kandungan_gizi` (
  `id_gizi` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `nama_makanan_minuman` varchar(120) NOT NULL,
  `kategori` enum('Makanan','Minuman') NOT NULL DEFAULT 'Makanan',
  `porsi` varchar(50) NOT NULL DEFAULT '100 gram',
  `kalori` decimal(7,2) NOT NULL DEFAULT 0.00,
  `karbohidrat` decimal(7,2) NOT NULL DEFAULT 0.00,
  `protein` decimal(7,2) NOT NULL DEFAULT 0.00,
  `lemak` decimal(7,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_gizi`),
  KEY `fk_gizi_admin` (`id_admin`),
  CONSTRAINT `fk_gizi_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `kandungan_gizi` DISABLE KEYS */;
INSERT INTO `kandungan_gizi` VALUES (1,4,'Nasi Putih Matang','Makanan','100 gram',130.00,28.20,2.70,0.30),(2,4,'Ayam Dada Panggang','Makanan','100 gram',165.00,0.00,31.00,3.60),(3,4,'Telur Rebus','Makanan','100 gram',155.00,1.10,12.60,10.60),(4,4,'Tempe Goreng','Makanan','100 gram',225.00,15.00,19.00,11.00),(5,4,'Tahu Putih Kukus','Makanan','100 gram',76.00,1.90,8.10,4.80),(6,4,'Alpukat Matang','Makanan','100 gram',160.00,8.50,2.00,14.70),(7,4,'Brokoli Rebus','Makanan','100 gram',35.00,7.20,2.40,0.40),(8,4,'Oatmeal (tanak air)','Makanan','100 gram',71.00,12.00,2.50,1.50),(9,4,'Jus Alpukat Madu','Minuman','100 gram',115.00,13.00,1.20,6.50),(10,4,'Air Kelapa Murni','Minuman','100 gram',19.00,3.70,0.70,0.20),(11,4,'Jus Jeruk Peras (tanpa gula)','Minuman','100 gram',45.00,10.40,0.70,0.20),(12,4,'Susu Kedelai Tawar','Minuman','100 gram',54.00,2.90,3.30,2.00);
/*!40000 ALTER TABLE `kandungan_gizi` ENABLE KEYS */;
DROP TABLE IF EXISTS `kategori_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategori_artikel` (
  `id_kategori_artikel` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(60) NOT NULL,
  PRIMARY KEY (`id_kategori_artikel`),
  UNIQUE KEY `uq_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `kategori_artikel` DISABLE KEYS */;
INSERT INTO `kategori_artikel` VALUES (5,'Diet Sehat'),(6,'Kesehatan Kulit'),(3,'Kesehatan Mental'),(1,'Nutrisi'),(2,'Olahraga'),(4,'Tips Harian');
/*!40000 ALTER TABLE `kategori_artikel` ENABLE KEYS */;
DROP TABLE IF EXISTS `laporan_statistik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laporan_statistik` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `total_pengguna` int(11) NOT NULL DEFAULT 0,
  `total_artikel` int(11) NOT NULL DEFAULT 0,
  `total_kalori` int(11) NOT NULL DEFAULT 0,
  `total_resep` int(11) NOT NULL DEFAULT 0,
  `total_tempat` int(11) NOT NULL DEFAULT 0,
  `total_menu` int(11) NOT NULL DEFAULT 0,
  `tanggal_cetak` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_laporan`),
  KEY `fk_laporan_admin` (`id_admin`),
  CONSTRAINT `fk_laporan_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `laporan_statistik` DISABLE KEYS */;
INSERT INTO `laporan_statistik` VALUES (1,4,2,4,805,4,2,6,'2026-03-25 08:30:00'),(2,4,3,4,805,4,2,6,'2026-10-18 09:02:07'),(3,4,3,4,805,4,2,19,'2026-10-21 11:38:49');
/*!40000 ALTER TABLE `laporan_statistik` ENABLE KEYS */;
DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `id_tempat` int(11) NOT NULL,
  `id_kategori_menu` int(11) DEFAULT NULL,
  `nama_menu` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL DEFAULT 0,
  `kalori` decimal(7,2) NOT NULL DEFAULT 0.00,
  `protein` decimal(7,2) NOT NULL DEFAULT 0.00,
  `karbohidrat` decimal(7,2) NOT NULL DEFAULT 0.00,
  `lemak` decimal(7,2) NOT NULL DEFAULT 0.00,
  `estimasi_waktu` int(11) NOT NULL DEFAULT 0,
  `foto_menu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `fk_menu_tempat` (`id_tempat`),
  KEY `fk_menu_kategori` (`id_kategori_menu`),
  CONSTRAINT `fk_menu_kategori` FOREIGN KEY (`id_kategori_menu`) REFERENCES `menu_categories` (`id_kategori_menu`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_menu_tempat` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_sehat` (`id_tempat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,1,NULL,'Jus Alpukat Madu','Alpukat matang, madu murni, tanpa gula tambahan.',25000,115.30,1.20,13.00,6.50,5,NULL),(2,1,NULL,'Green Detox Juice','Campuran seledri, apel hijau, lemon, dan jahe.',27000,53.60,1.00,11.50,0.40,7,NULL),(3,1,NULL,'Grilled Chicken Bowl','Ayam panggang, quinoa, brokoli, dan saus lemon.',45000,311.80,31.00,28.50,8.20,15,NULL),(4,1,NULL,'Air Kelapa Murni','Kelapa segar langsung dari kebun lokal.',15000,17.30,0.70,3.70,0.20,3,NULL),(5,2,NULL,'Salad Buah Yogurt','Potongan buah segar dengan yogurt rendah lemak.',28000,132.70,4.20,22.00,3.10,8,NULL),(6,2,NULL,'Tofu Sesame Bowl','Tahu panggang, wijen, edamame, dan saus rendah garam.',35000,223.00,18.00,17.50,9.00,12,NULL),(7,1,1,'Healthy Green','Bayam-Kailan-Pir-Nanas-Sunkist-Timun',25000,17.00,1.00,1.00,1.00,0,'20261019_121925_1c47c50f.jpeg'),(8,1,1,'Almomilk','',28000,0.00,0.00,0.00,0.00,0,'20261019_122321_0392d9b6.jpeg'),(9,1,1,'Berry Love','Stoberi-Apel-Pir',25000,0.00,0.00,0.00,0.00,0,'20261019_122700_4881d426.jpeg'),(10,1,1,'Up The Beet','Brokoli-Timun-Semangka-Mint-Beet-Sunkist',25000,0.00,0.00,0.00,0.00,0,'20261019_123200_993bf163.jpeg'),(11,1,1,'Imunboost','Apel-Pir-Nanas-Lemon-Mint',25000,0.00,0.00,0.00,0.00,0,'20261019_124209_9468e936.jpeg'),(12,1,1,'Honey Bunny','Wortel-Apel-Melon-Timun-Lemon-Chia Seed',25000,0.00,0.00,0.00,0.00,0,'20261019_124324_37545aba.jpeg'),(13,1,1,'Glowskin','Nanas-Apel-Tomat-Sunkist',25000,0.00,0.00,0.00,0.00,0,'20261019_124433_ccb83842.jpeg'),(14,1,1,'Golden Hour','Nanas-Kunyit-Apel-Sunkist',25000,0.00,0.00,0.00,0.00,0,'20261019_124547_9bdae119.jpeg'),(15,1,6,'Fresh n Fit Roll (Chicken/Shrimp)','Gulung udang segar, krispi, dan saus istimewa',35000,0.00,0.00,0.00,0.00,0,'20261020_133732_a0a7a938.webp'),(16,1,6,'Salmon Penne Salad','Salmon segar, pasta penne lembut, dressing lemon segar, taburan keju',35000,0.00,0.00,0.00,0.00,0,'20261020_134009_4f87fce8.webp'),(17,1,3,'Sop Iga Bakar Nasi Merah','Iga bakar dengan nasi merah dan sup hangat yang berisi wotel, kol putih dan sayuran lainnya',55,0.00,0.00,0.00,0.00,0,'20261020_134540_04df5eaf.webp'),(18,1,3,'Chicken Steak with Mushroom Sauce','',35,0.00,0.00,0.00,0.00,0,'20261020_134829_2d14c789.webp'),(19,1,3,'Aglio Glowlio','Pasta sehat dengan aroma bawang putih dan udang segar, lezat dan ringan',35,0.00,0.00,0.00,0.00,0,'20261020_135052_126d0dd4.webp');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_categories` (
  `id_kategori_menu` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(80) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_kategori_menu`),
  UNIQUE KEY `uq_menu_cat_nama` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `menu_categories` DISABLE KEYS */;
INSERT INTO `menu_categories` VALUES (1,'Jus','Minuman jus segar dari buah-buahan pilihan tanpa gula tambahan.'),(2,'Smoothies','Minuman kental buah/dairy yang kaya serat dan cocok untuk sarapan.'),(3,'Makanan Sehat','Hidangan utama rendah kalori dengan protein dan sayur seimbang.'),(4,'Camilan Sehat','Camilan rendah kalori untuk menemani aktivitas harian.'),(5,'Dessert Sehat','Camilan manis rendah gula.'),(6,'Salad','Olahan sayuran dan buah-buahan segar yang dilengkapi dengan dressing lezat dan kaya nutrisi');
/*!40000 ALTER TABLE `menu_categories` ENABLE KEYS */;
DROP TABLE IF EXISTS `resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resep` (
  `id_resep` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `judul_resep` varchar(180) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `bahan_bahan` text NOT NULL,
  `langkah_pembuatan` text NOT NULL,
  `waktu_masak` int(11) NOT NULL DEFAULT 0,
  `status_publikasi` enum('draft','dipublikasikan') NOT NULL DEFAULT 'draft',
  `total_kalori` int(11) NOT NULL DEFAULT 0,
  `foto_resep` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_resep`),
  KEY `fk_resep_admin` (`id_admin`),
  CONSTRAINT `fk_resep_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
INSERT INTO `resep` VALUES (1,4,'Salad Quinoa Ayam Panggang','Salad tinggi protein dengan quinoa dan ayam panggang rendah lemak.','100 gram quinoa mentah\r\n150 gram dada ayam\r\n50 gram selada romaine\r\n1 buah tomat cherry\r\n1 sdm minyak zaitun\r\n1 sdt perasan lemon\r\nLada hitam secukupnya','Rebus quinoa dengan air selama 15 menit hingga mengembang.\r\nMarinasi ayam dengan lemon, lada, dan sedikit minyak zaitun.\r\nPanggang ayam selama 10 menit tiap sisi hingga matang.\r\nTata selada, tomat, dan quinoa dalam mangkuk.\r\nIris ayam panggang di atas salad dan sajikan.',25,'dipublikasikan',420,'20261019_152924_10bbefb6.jpg'),(2,4,'Jus Alpukat Madu Low Sugar','Minuman segar kaya lemak sehat, cocok untuk menu sarapan.','1 buah alpukat matang\r\n200 ml air dingin\r\n1 sdm madu murni\r\n2 sdm susu kedelai\r\nEs batu secukupnya','Keruk daging alpukat dan masukkan ke blender.\r\nTambahkan air dingin, madu, dan susu kedelai.\r\nBlender hingga halus dan lembut.\r\nTuang ke gelas, tambahkan es batu, lalu sajikan.',10,'dipublikasikan',210,'20261019_133610_f81eea2a.jpg'),(3,4,'Sup Brokoli Tahu Putih','Sup hangat rendah kalori dengan protein nabati yang mengenyangkan.','150 gram brokoli\r\n100 gram tahu putih\r\n500 ml kaldu ayam rendah garam\r\n2 siung bawang putih\r\n1/2 bawang bombay\r\nSeledri secukupnya','Tumis bawang putih dan bawang bombay tanpa minyak berlebih.\r\nMasukkan kaldu, didihkan.\r\nTambahkan tahu dan brokoli, masak 7 menit.\r\nBumbui lada dan sedikit garam, taburi seledri.\r\nSajikan selagi hangat.',20,'dipublikasikan',175,'20261019_133220_b5e35061.jpg'),(4,4,'Oatmeal Pisang Kismis (Draft)','Sarapan cepat saji kaya serat, masih dalam tahap penyempurnaan resep.','50 gram oatmeal instan\r\n1 buah pisang ambon\r\n1 sdm kismis\r\n200 ml susu rendah lemak','Panaskan susu hingga hangat.\r\nMasukkan oatmeal, aduk hingga mengental.\r\nTambahkan irisan pisang dan kismis.\r\nSajikan hangat.',8,'draft',285,'20261019_133344_839d50b8.jpg');
/*!40000 ALTER TABLE `resep` ENABLE KEYS */;
DROP TABLE IF EXISTS `tempat_sehat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempat_sehat` (
  `id_tempat` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `nama_tempat` varchar(150) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `foto_tempat` varchar(255) DEFAULT NULL,
  `foto_menu` varchar(255) DEFAULT NULL,
  `link_maps` varchar(255) DEFAULT NULL,
  `link_instagram` varchar(255) DEFAULT NULL,
  `link_grabfood` varchar(255) DEFAULT NULL,
  `link_shopeefood` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_tempat`),
  KEY `fk_tempat_admin` (`id_admin`),
  CONSTRAINT `fk_tempat_admin` FOREIGN KEY (`id_admin`) REFERENCES `users` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `tempat_sehat` DISABLE KEYS */;
INSERT INTO `tempat_sehat` VALUES (1,4,'RealJuice Healthy Caf+?','Jl. Gatot Subroto No.1D, Karanganyar, Kec.Indramayu, Kab.Indramayu, Jawa Barat 45213','Kafe sehat yang menyajikan aneka jus segar tanpa gula tambahan, salad bowl, serta menu rendah kalori. Semua bahan dipilih harian dari petani lokal.','0821-5530-5503','20261019_120128_a679a837.webp','20261019_120128_3864ee3b.webp','https://maps.app.goo.gl/ycBEsmevtZ2R2huS6','','',''),(2,4,'Salad Bar Segar Hijau','Jl. Gajah Mada No. 12, Padang, Sumatera Barat','Kedai salad dengan konsep build-your-own bowl. Tersedia pilihan protein nabati dan hewani dengan informasi gizi pada setiap menunya.','0813-9876-5432',NULL,NULL,'https://maps.google.com/?q=Salad+Bar+Segar+Hijau+Padang',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tempat_sehat` ENABLE KEYS */;
DROP TABLE IF EXISTS `ulasan_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ulasan_rating` (
  `id_usulan_rating` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `komentar` text DEFAULT NULL,
  `tanggal_ulasan` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usulan_rating`),
  KEY `fk_ulasan_pengguna` (`id_pengguna`),
  KEY `fk_ulasan_tempat` (`id_tempat`),
  CONSTRAINT `fk_ulasan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `users` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ulasan_tempat` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_sehat` (`id_tempat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `ulasan_rating` DISABLE KEYS */;
INSERT INTO `ulasan_rating` VALUES (1,1,1,5,'Jusnya segar tanpa gula tambahan, tempatnya bersih dan nyaman. Recommended!','2026-03-10 10:15:00'),(2,2,1,4,'Menu grilled chicken bowl-nya enak dan porsinya pas. Harga masih terjangkau.','2026-03-12 19:40:00'),(3,1,2,5,'Konsep build-your-own salad-nya menarik, informasi gizi tiap menu jelas.','2026-03-18 12:05:00');
/*!40000 ALTER TABLE `ulasan_rating` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_pengguna` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `berat_badan` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tinggi_badan` decimal(5,2) NOT NULL DEFAULT 0.00,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `umur` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `uq_pengguna_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'andi_sehat','$2y$10$HdlM0uEE6ZXs1Aqk0yuZTO4id4xz2unxPUd90TuA/1YS/48.Y2x/W','Andi Pratama','user',62.50,170.00,'L',24,'2026-01-15 09:20:00'),(2,'sari_dewi','$2y$10$HdlM0uEE6ZXs1Aqk0yuZTO4id4xz2unxPUd90TuA/1YS/48.Y2x/W','Sari Dewi','user',55.00,160.00,'P',27,'2026-02-02 14:05:00'),(3,'adorelya','$2y$10$y2le4teCXtEnFFqj2iKb7uAiCYTW9lR8Oe1aG7fbNV0761l4CBo5u','eyi','user',46.00,158.60,'P',19,'2026-10-17 09:47:55'),(4,'admin','$2y$10$eU3P1ieECi3zYzD7G9KVFOMt9GCZjrSqnxZp043c5DOwXJhIr4FYi','Administrator','admin',90.00,100.00,'L',29,'2026-10-18 09:18:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


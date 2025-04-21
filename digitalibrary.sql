-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 21, 2025 at 12:53 PM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digitalibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `BukuID` int(11) NOT NULL,
  `Judul` varchar(255) DEFAULT NULL,
  `gambar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Penulis` varchar(255) DEFAULT NULL,
  `Penerbit` varchar(255) DEFAULT NULL,
  `TahunTerbit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`BukuID`, `Judul`, `gambar`, `Penulis`, `Penerbit`, `TahunTerbit`) VALUES
(53, 'One Piece', '67e57417c68d4_One piece.jpeg', 'Eiichiro Oda', 'Shueisha', 1997),
(55, 'Dragon Ball', '67e5746197bee_Manga Dragon Ball.jpeg', 'Akira Toriyama', ' Shueisha', 1984),
(56, 'Mariposa', '67e574e411fc8_Novel Mariposa 2 - Luluk HF (Paket Loovi) - Coconutbooks.jpeg', 'Luluk HF', 'Coconut Books', 2018),
(58, 'Malioboro Midnightt', '67e572749c506_Malioboro at midnight.jpeg', 'SKYSPHIRE', 'Bukune Kreatif Cipta', 2023),
(59, 'Dia Razi', '67fc601b579ee_b1.jpg', 'Nurwina Sari', 'Gramedia Pustaka Utam', 2024),
(60, 'Hidden Beauty', '67fc6064362e0_b2.jpeg', 'Rome', 'Gramedia Pustaka Utama', 2024),
(61, 'Dikta & Hukum', '67fc60fe6495b_b3.jpg', 'Dhia\'an Farah', ' Gramedia Pustaka Utama', 2024),
(62, 'Bumi Manusia', '67fc615e3932d_b5.jpeg', ' Pramoedya Ananta Toer', 'Lentera Timur', 2024);

-- --------------------------------------------------------

--
-- Table structure for table `denda`
--

CREATE TABLE `denda` (
  `dendaID` int(11) NOT NULL,
  `jumlah_hari` int(11) NOT NULL,
  `jumlah_denda` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `denda`
--

INSERT INTO `denda` (`dendaID`, `jumlah_hari`, `jumlah_denda`) VALUES
(0, 1, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `kategoribuku`
--

CREATE TABLE `kategoribuku` (
  `KategoriID` int(11) NOT NULL,
  `NamaKategori` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategoribuku`
--

INSERT INTO `kategoribuku` (`KategoriID`, `NamaKategori`) VALUES
(5, 'komik'),
(7, 'biografii'),
(17, 'sejarah'),
(19, 'novel'),
(20, 'biografi');

-- --------------------------------------------------------

--
-- Table structure for table `kategoribuku_relasi`
--

CREATE TABLE `kategoribuku_relasi` (
  `KategoriBukuID` int(11) NOT NULL,
  `BukuID` int(11) DEFAULT NULL,
  `KategoriID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategoribuku_relasi`
--

INSERT INTO `kategoribuku_relasi` (`KategoriBukuID`, `BukuID`, `KategoriID`) VALUES
(44, 55, 5),
(49, 53, 5),
(52, 56, 19),
(53, 59, 19),
(54, 60, 19),
(55, 61, 19),
(56, 62, 17);

-- --------------------------------------------------------

--
-- Table structure for table `koleksipribadi`
--

CREATE TABLE `koleksipribadi` (
  `KoleksiID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BukuID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `koleksipribadi`
--

INSERT INTO `koleksipribadi` (`KoleksiID`, `UserID`, `BukuID`) VALUES
(43, 47, 53),
(44, 29, 55),
(47, 29, 56),
(48, 86, 59);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `PeminjamanID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BukuID` int(11) DEFAULT NULL,
  `TanggalPeminjaman` date DEFAULT NULL,
  `TanggalPengembalian` date DEFAULT NULL,
  `StatusPeminjaman` varchar(50) DEFAULT NULL,
  `dendaID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`PeminjamanID`, `UserID`, `BukuID`, `TanggalPeminjaman`, `TanggalPengembalian`, `StatusPeminjaman`, `dendaID`) VALUES
(8, 48, 58, '2025-04-07', '2025-04-14', 'Dikembalikan', NULL),
(18, 29, 53, '2025-04-21', '2025-04-24', 'dipinjam', NULL),
(19, 29, 53, '2025-04-21', '2025-04-24', 'dikembalikan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ulasanbuku`
--

CREATE TABLE `ulasanbuku` (
  `UlasanID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BukuID` int(11) DEFAULT NULL,
  `Ulasan` text,
  `Rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasanbuku`
--

INSERT INTO `ulasanbuku` (`UlasanID`, `UserID`, `BukuID`, `Ulasan`, `Rating`) VALUES
(29, 47, 55, 'bukunya bagus banget, lucu juga', 4),
(35, 86, 59, 'bikin baper razinya, baguss banget lagi bukunya fikss kalian harus baca buku iniii', 5),
(36, 29, 59, 'bagusss', 4);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `confirm_password` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `NamaLengkap` varchar(255) DEFAULT NULL,
  `Alamat` text,
  `Role` enum('admin','petugas','peminjam') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Password`, `confirm_password`, `Email`, `NamaLengkap`, `Alamat`, `Role`) VALUES
(29, 'salwa', '$2y$10$BJRC594LzdyBW6NzV83dOOAuaVHO2GqAT0pNwGuwuf9QoBRYonMUu', '12345', 'salwasuci57@gmail.com', 'salwasuci', 'depok', 'peminjam'),
(30, 'admin', '$2y$10$xW50HnJS.rZwvN2MbCZ9sejVct06TnqawTGb00y6OxS67Tf32OsD2', '123456', 'nusapustaka@gmail.com', 'nusa pustaka', 'bogor', 'admin'),
(47, 'gisel', '$2y$10$cf8GrvdZ7FylfsCO//TZm.MinTPGJuQkJRLBj8J7NlC7clkShvOKC', '5277', 'gisella@gmail.com', 'gisela', 'jakarta', 'peminjam'),
(48, 'maura', '$2y$10$aHqL0wnIucBJLZJ4Lm..luCzXVZD/oxqcOi3giwRmx3kTaLl6j16e', '010203', 'maura@gmail.com', 'maura intan', 'bogor', 'peminjam'),
(67, 'petugas', '$2y$10$u7Enfy5MLDsyJMrP0Qrqfe8iDg1bSU/ycwFQ0LAthb1CiJ99L8ohy', '12345678', 'salwasuci57@gmail.com', 'suci ramadhani', 'jln kenangan mantan', 'petugas'),
(84, 'wawa', '$2y$10$DEchAX8lFY5hgJe5seqQguaYoOuJjT4G7E1rtn8J8mA5ZXA7Rwl6u', '1234', 'salwaw@gmail.com', 'salwasuci', 'jln kenangan mantann', 'petugas'),
(86, 'acha', '$2y$10$eLl/mZGtutzJlxJvyi3CaOFLZccwHSYbi.vU6CvfS8CPx6xwPnjHG', 'acha123', 'acha@gmail.com', 'acha', 'cibinong bogor', 'peminjam'),
(87, 'awa', '$2y$10$/UAL9H8BJ75ZHwna9PF6P.BqRfaOrsDclOfaZhduZFEUC3l86DuQW', '213', 'nabila@gmail.com', 'aleshazhafira', 'jln kenangan mantan', 'peminjam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`BukuID`);

--
-- Indexes for table `denda`
--
ALTER TABLE `denda`
  ADD PRIMARY KEY (`dendaID`);

--
-- Indexes for table `kategoribuku`
--
ALTER TABLE `kategoribuku`
  ADD PRIMARY KEY (`KategoriID`);

--
-- Indexes for table `kategoribuku_relasi`
--
ALTER TABLE `kategoribuku_relasi`
  ADD PRIMARY KEY (`KategoriBukuID`),
  ADD KEY `BukuID` (`BukuID`),
  ADD KEY `KategoriID` (`KategoriID`);

--
-- Indexes for table `koleksipribadi`
--
ALTER TABLE `koleksipribadi`
  ADD PRIMARY KEY (`KoleksiID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BukuID` (`BukuID`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`PeminjamanID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BukuID` (`BukuID`),
  ADD KEY `dendaID` (`dendaID`);

--
-- Indexes for table `ulasanbuku`
--
ALTER TABLE `ulasanbuku`
  ADD PRIMARY KEY (`UlasanID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BukuID` (`BukuID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `BukuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `kategoribuku`
--
ALTER TABLE `kategoribuku`
  MODIFY `KategoriID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `kategoribuku_relasi`
--
ALTER TABLE `kategoribuku_relasi`
  MODIFY `KategoriBukuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `koleksipribadi`
--
ALTER TABLE `koleksipribadi`
  MODIFY `KoleksiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `PeminjamanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ulasanbuku`
--
ALTER TABLE `ulasanbuku`
  MODIFY `UlasanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kategoribuku_relasi`
--
ALTER TABLE `kategoribuku_relasi`
  ADD CONSTRAINT `kategoribuku_relasi_ibfk_1` FOREIGN KEY (`BukuID`) REFERENCES `buku` (`BukuID`),
  ADD CONSTRAINT `kategoribuku_relasi_ibfk_2` FOREIGN KEY (`KategoriID`) REFERENCES `kategoribuku` (`KategoriID`);

--
-- Constraints for table `koleksipribadi`
--
ALTER TABLE `koleksipribadi`
  ADD CONSTRAINT `koleksipribadi_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `koleksipribadi_ibfk_2` FOREIGN KEY (`BukuID`) REFERENCES `buku` (`BukuID`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`BukuID`) REFERENCES `buku` (`BukuID`),
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`dendaID`) REFERENCES `denda` (`dendaID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `ulasanbuku`
--
ALTER TABLE `ulasanbuku`
  ADD CONSTRAINT `ulasanbuku_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `ulasanbuku_ibfk_2` FOREIGN KEY (`BukuID`) REFERENCES `buku` (`BukuID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

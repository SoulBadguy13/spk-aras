-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2021 at 08:38 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spkambing`
--

-- --------------------------------------------------------

--
-- Table structure for table `kambing`
--

CREATE TABLE `kambing` (
  `id_kambing` int(10) NOT NULL,
  `no_kalung` varchar(6) NOT NULL,
  `ciri_khas` text NOT NULL,
  `tanggal_input` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kambing`
--

INSERT INTO `kambing` (`id_kambing`, `no_kalung`, `ciri_khas`, `tanggal_input`) VALUES
(15, 'A1', 'Google Clasroom', '2020-12-26'),
(16, 'A2', 'Edmodo', '2020-12-26'),
(17, 'A3', 'Zoom Meeting', '2020-12-26'),
(18, 'A4', 'Google Meet', '2020-12-26'),
(19, 'A5', 'Cisco WebEx', '2020-12-26'),
(20, 'A6', 'WhatsApp Group', '2020-12-26'),
(21, 'A7', 'Discord', '2020-12-26'),
(22, 'A8', 'Youtube', '2021-09-02');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `type` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL,
  `ada_pilihan` tinyint(1) DEFAULT NULL,
  `urutan_order` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `nama`, `type`, `bobot`, `ada_pilihan`, `urutan_order`) VALUES
(16, 'Penggunaan Data Internet', 'cost', 0.3, 1, 1),
(17, 'Kemudahan Akses', 'benefit', 0.25, 1, 2),
(18, 'Kapasitas Pengguna', 'benefit', 0.2, 1, 3),
(19, 'Batas Waktu Penggunaan', 'benefit', 0.15, 1, 4),
(20, 'Interaksi Visual', 'benefit', 0.1, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `nilai_kambing`
--

CREATE TABLE `nilai_kambing` (
  `id_nilai_kambing` int(11) NOT NULL,
  `id_kambing` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `nilai_kambing`
--

INSERT INTO `nilai_kambing` (`id_nilai_kambing`, `id_kambing`, `id_kriteria`, `nilai`) VALUES
(115, 15, 16, 2),
(116, 15, 17, 3),
(117, 15, 18, 3),
(118, 15, 19, 3),
(119, 15, 20, 3),
(125, 16, 16, 2),
(126, 16, 17, 2),
(127, 16, 18, 3),
(128, 16, 19, 3),
(129, 16, 20, 2),
(135, 17, 16, 4),
(136, 17, 17, 3),
(137, 17, 18, 2),
(138, 17, 19, 2),
(139, 17, 20, 3),
(140, 18, 16, 5),
(141, 18, 17, 2),
(142, 18, 18, 2),
(143, 18, 19, 2),
(144, 18, 20, 3),
(145, 19, 16, 4),
(146, 19, 17, 3),
(147, 19, 18, 2),
(148, 19, 19, 2),
(149, 19, 20, 3),
(150, 20, 16, 1),
(151, 20, 17, 3),
(152, 20, 18, 3),
(153, 20, 19, 3),
(154, 20, 20, 3),
(155, 21, 16, 3),
(156, 21, 17, 3),
(157, 21, 18, 3),
(158, 21, 19, 3),
(159, 21, 20, 3),
(195, 22, 16, 5),
(196, 22, 17, 3),
(197, 22, 18, 3),
(198, 22, 19, 3),
(199, 22, 20, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pilihan_kriteria`
--

CREATE TABLE `pilihan_kriteria` (
  `id_pil_kriteria` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `nilai` float NOT NULL,
  `urutan_order` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pilihan_kriteria`
--

INSERT INTO `pilihan_kriteria` (`id_pil_kriteria`, `id_kriteria`, `nama`, `nilai`, `urutan_order`) VALUES
(34, 16, 'Cukup', 3, 3),
(35, 16, 'Besar', 4, 2),
(37, 20, 'Kurang', 1, 3),
(38, 20, 'Cukup', 2, 2),
(42, 17, 'Kurang', 1, 3),
(43, 17, 'Cukup', 2, 2),
(44, 17, 'Baik', 3, 1),
(45, 16, 'Sangat Besar', 5, 1),
(46, 18, 'Kurang', 1, 3),
(47, 18, 'Cukup', 2, 2),
(48, 18, 'Banyak', 3, 1),
(50, 20, 'Baik', 3, 1),
(51, 19, 'Kurang', 1, 3),
(52, 19, 'Cukup', 2, 2),
(53, 19, 'Baik', 3, 1),
(54, 16, 'Kecil', 2, 3),
(55, 16, 'Sangat Kecil', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(70) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `email`, `alamat`, `role`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Zunan Arif R.', 'oxzygenz@gmail.com', 'Jalan Naik Turun 3312', '1'),
(7, 'petugas', '670489f94b6997a870b148f74744ee5676304925', 'Anton S', 'test@thesamplemail.com', 'test', '2'),
(8, 'ichsan', '2adb2d105e51f3795692c6f686472d605a2c27f9', 'ichsan', 'ichsanferdy@gmail.com', 'pwk', '2'),
(9, 'adminn', '6a8437dd0009e4aa220215ad24cf7c7d2e8c95c8', 'ichsan', 'ichsanferdy@gmail.com', 'pwk', '2'),
(10, 'Ichsangaga', '8cb2237d0679ca88db6464eac60da96345513964', 'Ichsan Ferdiansyah', 'ichsanfer@gmail.com', 'pwk', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kambing`
--
ALTER TABLE `kambing`
  ADD PRIMARY KEY (`id_kambing`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `nilai_kambing`
--
ALTER TABLE `nilai_kambing`
  ADD PRIMARY KEY (`id_nilai_kambing`),
  ADD UNIQUE KEY `id_kambing_2` (`id_kambing`,`id_kriteria`),
  ADD KEY `id_kambing` (`id_kambing`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD PRIMARY KEY (`id_pil_kriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kambing`
--
ALTER TABLE `kambing`
  MODIFY `id_kambing` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `nilai_kambing`
--
ALTER TABLE `nilai_kambing`
  MODIFY `id_nilai_kambing` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  MODIFY `id_pil_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai_kambing`
--
ALTER TABLE `nilai_kambing`
  ADD CONSTRAINT `nilai_kambing_ibfk_1` FOREIGN KEY (`id_kambing`) REFERENCES `kambing` (`id_kambing`),
  ADD CONSTRAINT `nilai_kambing_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);

--
-- Constraints for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD CONSTRAINT `pilihan_kriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

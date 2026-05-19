-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 11:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_surat`
--

-- --------------------------------------------------------

--
-- Table structure for table `arsip`
--

CREATE TABLE `arsip` (
  `id` int(11) NOT NULL,
  `kode_arsip` varchar(50) NOT NULL,
  `jenis_surat` enum('masuk','keluar') NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `perihal` text NOT NULL,
  `asal_instansi` varchar(200) DEFAULT NULL,
  `lokasi_fisik` varchar(200) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `file_arsip` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bidang`
--

CREATE TABLE `bidang` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `kepala_bidang` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bidang`
--

INSERT INTO `bidang` (`id`, `kode`, `nama`, `kepala_bidang`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'BID01', 'Umum dan Kepegawaian', 'Novita Min Utami', '', '2026-02-19 08:47:09', '2026-04-14 18:58:33'),
(2, 'BID02', 'Umum dan Kepegawaian', 'Staff Bidang Umum dan Kepegawaian', '', '2026-02-19 08:47:09', '2026-03-10 02:11:54'),
(4, 'BID03', 'Keuangan', 'Nur Faizah', '', '2026-03-07 22:37:24', '2026-04-14 18:58:14'),
(5, 'BID04', 'Kurikulum', 'Rokhmat', '', '2026-04-14 18:58:50', '2026-04-14 18:58:50');

-- --------------------------------------------------------

--
-- Table structure for table `disposisi`
--

CREATE TABLE `disposisi` (
  `id` int(11) NOT NULL,
  `index` varchar(255) NOT NULL,
  `surat_masuk_id` int(11) NOT NULL,
  `dari` int(11) DEFAULT NULL,
  `kepada` varchar(50) DEFAULT NULL,
  `isi_disposisi` text NOT NULL,
  `tanggal_disposisi` date NOT NULL,
  `batas_waktu` date DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai') DEFAULT 'menunggu',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disposisi`
--

INSERT INTO `disposisi` (`id`, `index`, `surat_masuk_id`, `dari`, `kepada`, `isi_disposisi`, `tanggal_disposisi`, `batas_waktu`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(33, '', 11, NULL, 'kepala_bidang_pendidikan_dasar', '', '2026-05-13', NULL, 'menunggu', '', '2026-05-13 01:36:00', '2026-05-13 01:36:00'),
(34, '', 11, NULL, 'kepala_bidang_pendidikan_dasar', '', '2026-05-13', NULL, 'menunggu', '', '2026-05-13 11:39:01', '2026-05-13 11:39:01');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id` int(11) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tujuan` varchar(200) NOT NULL,
  `pengolah` varchar(100) DEFAULT NULL,
  `perihal` text NOT NULL,
  `isi_surat` text DEFAULT NULL,
  `klasifikasi` enum('biasa','penting','rahasia') DEFAULT 'biasa',
  `file_surat` varchar(255) DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_keluar`
--

INSERT INTO `surat_keluar` (`id`, `nomor_surat`, `tanggal_surat`, `tujuan`, `pengolah`, `perihal`, `isi_surat`, `klasifikasi`, `file_surat`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES
(12, '800/1323/2026', '2026-05-04', 'BKPSDM', 'Umpeg', 'Permohonan Penginputan Absensi SIHADIR bln April 2026', '', 'biasa', 'SK_20260512203945_6a0373f157327.pdf', 1, '2026-05-13 01:39:45', '2026-05-13 01:39:45');

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id` int(11) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `pengirim` varchar(200) NOT NULL,
  `perihal` text NOT NULL,
  `catatan` text DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `penerima_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nomor_urut` varchar(50) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `pengolah` varchar(100) DEFAULT NULL,
  `dilihat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_masuk`
--

INSERT INTO `surat_masuk` (`id`, `nomor_surat`, `tanggal_surat`, `tanggal_terima`, `pengirim`, `perihal`, `catatan`, `file_surat`, `penerima_id`, `created_at`, `updated_at`, `nomor_urut`, `lampiran`, `pengolah`, `dilihat`) VALUES
(11, '800.1.6.2/1407/2026', '2026-04-30', NULL, 'BKPSDM', 'Evaluasi Kehadiran ASN Periode Januari s.d Maret 2026', '', 'SM_20260512203507_6a0372dbf2b3e.pdf', 1, '2026-05-13 01:35:07', '2026-05-13 01:35:17', NULL, '1', 'Umpeg', '2026-05-13 01:35:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `bidang_id` int(11) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `email`, `jabatan`, `bidang_id`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Kepala Umpeg', 'admin', '$2y$10$5Bq038RNsUWbIXdM2qOiF.v6D1exr/SMT0FgLqtJECUbCq9ZFeXKK', 'admin@example.com', 'Administrator Sistem', 1, 'admin', 1, '2026-05-13 16:20:43', '2026-02-19 08:47:09', '2026-05-13 16:20:43'),
(7, 'Fahri', 'fahri123', '$2y$10$OIbvam3UFkn9FHO4It3/mOWamfTM2xl21ZsuigIbTYHRDW2zNnpA.', 'muhammadfahrialfaris@gmail.com', 'Pegawai', 1, 'admin', 1, '2026-05-19 16:46:52', '2026-05-13 16:29:56', '2026-05-19 16:46:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip`
--
ALTER TABLE `arsip`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidang`
--
ALTER TABLE `bidang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disposisi`
--
ALTER TABLE `disposisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_masuk_id` (`surat_masuk_id`),
  ADD KEY `dari` (`dari`),
  ADD KEY `kepada` (`kepada`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penerima_id` (`penerima_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `bidang_id` (`bidang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip`
--
ALTER TABLE `arsip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bidang`
--
ALTER TABLE `bidang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `disposisi`
--
ALTER TABLE `disposisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `disposisi`
--
ALTER TABLE `disposisi`
  ADD CONSTRAINT `disposisi_ibfk_1` FOREIGN KEY (`surat_masuk_id`) REFERENCES `surat_masuk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disposisi_ibfk_2` FOREIGN KEY (`dari`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD CONSTRAINT `surat_keluar_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD CONSTRAINT `surat_masuk_ibfk_1` FOREIGN KEY (`penerima_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

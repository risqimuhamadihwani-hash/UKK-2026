-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 07:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'NIP20260001', 'Wawan Kurniawan, S.Pd.I', 'WawanKurniawan@gmail.com', 1, 107, '2025-12-31 18:00:00', '2026-11-14 10:14:07'),
(2, 'NIP20260002', 'Ai Nuryaidah, S.Pd', 'AiNuryaidah@gmail.com', 1, 108, '2026-02-01 18:00:00', '2026-11-11 05:16:54'),
(3, 'NIP20260003', 'Maya Permata Endah, S.Pd', 'MayaPermataEndah@gmail.com', 1, 109, '2026-03-02 18:00:00', '2026-11-11 05:16:54'),
(4, 'NIP20260004', 'Dody Jaenudin, S.Pd', 'DodyJaenudin@gmail.com', 1, 110, '2026-04-03 18:00:00', '2026-11-11 05:16:54'),
(5, 'NIP20260005', 'Dedi Kardiman, S.Pd', 'DediKardiman@gmail.com', 1, 111, '2026-05-04 18:00:00', '2026-11-11 05:16:54'),
(6, 'NIP20260006', 'Uca Musa Yusenda, S.Pd', 'UcaMusaYusenda@gmail.com', 1, 112, '2026-06-05 18:00:00', '2026-11-11 05:16:54'),
(7, 'NIP20260007', 'Drs. Ahmad Nur Mukhlis', 'AhmadNurMukhlis@gmail.com', 1, 113, '2026-07-06 18:00:00', '2026-11-11 05:16:54'),
(8, 'NIP20260008', 'Drs. Heri', 'Heri@gmail.com', 1, 114, '2026-08-07 18:00:00', '2026-11-11 05:16:54'),
(9, 'NIP20260009', 'Asep Bastaman', 'AsepBastaman@gmail.com', 1, 115, '2026-09-08 18:00:00', '2026-11-11 05:16:54'),
(10, 'NIP20260010', 'H. Encep Maemun, S.T., M.Pd', 'EncepMaemun@gmail.com', 1, 116, '2026-10-09 18:00:00', '2026-11-11 05:16:54'),
(11, 'NIP20260011', 'Opik', 'Opik@gmail.com', 1, 117, '2026-11-10 18:00:00', '2026-11-11 05:16:54'),
(12, 'NIP20260012', 'Ida Farida, S.Pd', 'IdaFarida@gmail.com', 1, 118, '2026-12-11 18:00:00', '2026-11-11 05:16:54'),
(13, 'NIP20260013', 'Idah Mahmudah, S.Pd', 'IdahMahmudah@gmail.com', 1, 119, '2026-01-12 18:00:00', '2026-11-11 05:16:54'),
(14, 'NIP20260014', 'Drs. Muhamad Ali', 'MuhamadAli@gmail.com', 1, 120, '2026-02-13 18:00:00', '2026-11-11 05:16:54'),
(15, 'NIP20260015', 'Helmi Ali Akbar, S.Pd.I', 'HelmiAliAkbar@gmail.com', 1, 121, '2026-03-14 18:00:00', '2026-11-11 05:16:54'),
(16, 'NIP20260016', 'Syeni Suswarini, S.Pd', 'SyeniSuswarini@gmail.com', 1, 122, '2026-04-15 18:00:00', '2026-11-11 05:16:54'),
(17, 'NIP20260017', 'Wiwid Yulianti, S.Pd', 'WiwidYulianti@gmail.com', 1, 123, '2026-05-16 18:00:00', '2026-11-11 05:16:54'),
(18, 'NIP20260018', 'Ansor Muhajir, S.T', 'AnsorMuhajir@gmail.com', 1, 124, '2026-06-17 18:00:00', '2026-11-11 05:16:54'),
(19, 'NIP20260019', 'Ogi Gaosun Gais, S.Pd.I', 'OgiGaosunGais@gmail.com', 1, 125, '2026-07-18 18:00:00', '2026-11-11 05:16:54'),
(20, 'NIP20260020', 'Sandi Mulyana, S.Pd', 'SandiMulyana@gmail.com', 1, 126, '2026-08-19 18:00:00', '2026-11-11 05:16:54'),
(21, 'NIP20260021', 'Erna Suryani, S.Pd', 'ErnaSuryani@gmail.com', 1, 127, '2026-09-20 18:00:00', '2026-11-11 05:16:54'),
(22, 'NIP20260022', 'Ai Wahidah, S.Ag', 'AiWahidah@gmail.com', 1, 128, '2026-10-21 18:00:00', '2026-11-11 05:16:54'),
(23, 'NIP20260023', 'Chandra Aditya Yusup, S.Pd', 'ChandraAdityaYusup@gmail.com', 1, 129, '2026-11-22 18:00:00', '2026-11-11 05:16:54'),
(24, 'NIP20260024', 'Arif Kusmawan, S.Pd', 'ArifKusmawan@gmail.com', 1, 130, '2026-12-23 18:00:00', '2026-11-11 05:16:54'),
(25, 'NIP20260025', 'Asep Taufiq Hidayat, S.Kom', 'AsepTaufiqHidayat@gmail.com', 1, 131, '2026-01-24 18:00:00', '2026-11-11 05:16:54'),
(26, 'NIP20260026', 'Rendy Ichsan Kusumah, S.T', 'RendyIchsanKusumah@gmail.com', 1, 132, '2026-02-25 18:00:00', '2026-11-11 05:16:54'),
(27, 'NIP20260027', 'Feri Rahmawan, S.Pd', 'FeriRahmawan@gmail.com', 1, 133, '2026-03-26 18:00:00', '2026-11-11 05:16:54'),
(29, 'NIP20260029', 'Yaneu Nurdian, S.Pd, M.Pd', 'YaneuNurdian@gmail.com', 1, 134, '2026-04-30 18:00:00', '2026-11-11 05:16:54'),
(30, 'NIP20260030', 'Ucu Muhammad Khoer N, S.Pd', 'UcuMuhammadKhoerN@gmail.com', 1, 135, '2026-06-01 18:00:00', '2026-11-11 05:16:54'),
(31, 'NIP20260031', 'Mira Dwi Rahmawati, S.Ag', 'MiraDwiRahmawati@gmail.com', 1, 136, '2026-07-02 18:00:00', '2026-11-11 05:16:54'),
(32, 'NIP20260032', 'Aldi Maulana, S.Pd', 'aldimaulana@gmail.com', 1, 137, '2026-08-03 18:00:00', '2026-11-16 01:50:49'),
(33, 'NIP20260033', 'Eli Rosliah, S.Pd', 'EliRosliah@gmail.com', 1, 138, '2026-09-04 18:00:00', '2026-11-11 05:16:54'),
(34, 'NIP20260034', 'Isni Nur Fitriani, S.Pd', 'IsniNurFitriani@gmail.com', 1, 139, '2026-10-05 18:00:00', '2026-11-11 05:16:54'),
(35, 'NIP20260035', 'Aanisah Putri Nur Aini, S.Pd', 'AanisahPutriNurAini@gmail.com', 1, 140, '2026-11-06 18:00:00', '2026-11-11 05:16:54'),
(36, 'NIP20260036', 'Yusi Luthfiani, S.Sos.I', 'YusiLuthfiani@gmail.com', 1, 141, '2026-12-07 18:00:00', '2026-11-11 05:16:54'),
(37, 'NIP20260037', 'Lilis Rustandy Mawardi, S.Pd.,', 'LilisRustandyMawardi@gmail.com', 1, 142, '2026-01-08 18:00:00', '2026-11-11 05:16:54'),
(38, 'NIP20260038', 'Yusrin Noer Sabrina, S.Pd', 'YusrinNoerSabrina@gmail.com', 1, 143, '2026-02-09 18:00:00', '2026-11-11 05:16:54'),
(39, 'NIP20260039', 'Senny Indayani, S.Pd', 'SennyIndayani@gmail.com', 1, 144, '2026-03-10 18:00:00', '2026-11-11 05:16:54'),
(40, 'NIP20260040', 'Ilma Amalia, S.Pd', 'IlmaAmalia@gmail.com', 1, 145, '2026-04-11 18:00:00', '2026-11-11 05:16:54'),
(41, 'NIP20260041', 'Alvira Sofwatunnisa, S.Pd', 'AlviraSofwatunnisa@gmail.com', 1, 146, '2026-05-12 18:00:00', '2026-11-11 05:16:54'),
(42, 'NIP20260042', 'Risna Andriyani, S.Pd', 'RisnaAndriyani@gmail.com', 1, 147, '2026-06-13 18:00:00', '2026-11-11 05:16:54'),
(43, 'NIP20260043', 'Dra. Tintin Noerhayati', 'TintinNoerhayati@gmail.com', 1, 148, '2026-07-14 18:00:00', '2026-11-11 05:16:54'),
(44, 'NIP20260044', 'Rizkia Fahira, S.Pd', 'RizkiaFahira@gmail.com', 1, 149, '2026-08-15 18:00:00', '2026-11-11 05:16:54'),
(45, 'NIP20260045', 'Frasetio', 'Frasetio@gmail.com', 1, 150, '2026-09-16 18:00:00', '2026-11-11 05:16:54'),
(46, 'NIP20260046', 'Ratnaningsih Hatil Aeni, S.Pd', 'RatnaningsihHatilAeni@gmail.com', 1, 151, '2026-10-17 18:00:00', '2026-11-11 05:16:54'),
(47, 'NIP20260047', 'Dheny Darajatul Hasani, S.Pd', 'DhenyDarajatulHasani@gmail.com', 1, 152, '2026-11-18 18:00:00', '2026-11-11 05:16:54'),
(48, 'NIP20260048', 'Fahrullah, S.Pd', 'Fahrullah@gmail.com', 1, 153, '2026-12-19 18:00:00', '2026-11-11 05:16:54'),
(49, 'NIP20260049', 'Aan Andriana, S.T', 'amdmin@gmail.com', 1, 154, '2026-01-20 18:00:00', '2026-11-11 05:16:54'),
(50, 'NIP20260050', 'Fitri Fauziah, S.T', 'FitriFauziah@gmail.com', 1, 155, '2026-02-21 18:00:00', '2026-11-11 05:16:54'),
(51, 'NIP20260051', 'Nida Aulia, S.Pd', 'NidaAulia@gmail.com', 1, 156, '2026-10-21 17:45:15', '2026-11-11 05:16:54'),
(52, 'NIP20260052', 'Sri Nurrizki Putri Krisnadi, S', 'SriNurrizkiPutriKrisnad@gmail.com', 1, 157, '2026-10-21 18:00:17', '2026-11-11 05:16:54'),
(53, 'NIP20260053', 'Riesy Putri Navia, S.Kom', 'RiesyPutriNavia@gmail.com', 1, 158, '2026-11-04 19:15:47', '2026-11-11 05:16:54'),
(54, 'NIP20260054', 'Rurry Febriani Maryadi, S.Pd', 'RurryFebrianiMaryadi@gmail.com', 1, 159, '2026-11-04 19:15:47', '2026-11-11 05:16:54'),
(55, 'NIP20260055', 'Irvan Rizal, S.Pd', 'IrvanRizal@gmail.com', 1, 160, '2026-11-04 19:19:21', '2026-11-11 05:16:54'),
(58, 'NIP1234567', 'ALPA', 'mcok30324@gmail.com', 1, 161, '2026-11-09 20:14:13', '2026-11-11 05:16:54'),
(59, 'NIP12345678', 'Ari Sandriana, S.Kom', 'AriSandriana@gmail.com', 1, 162, '2026-11-09 20:29:17', '2026-11-16 01:50:29');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_kelas`
--

INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `tahun_ajaran_id`, `status_aktif`, `created_at`, `updated_at`) VALUES
(11, 'XII RPL 1', 'XII', 'RPL', 25, 1, '2026-09-27 23:01:44', '2026-11-03 23:17:13'),
(26, 'XII RPL 2', 'XII', 'RPL', 25, 1, '2026-09-27 23:01:44', '2026-09-27 23:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(132, 88, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-14 05:28:43', '2026-11-14 05:28:43'),
(134, 89, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-14 11:17:50', '2026-11-14 11:17:50'),
(136, 90, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:40:36', '2026-11-16 01:40:36'),
(137, 91, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:40:50', '2026-11-16 01:40:50'),
(138, 92, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:02', '2026-11-16 01:41:02'),
(139, 93, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:13', '2026-11-16 01:41:13'),
(140, 94, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:22', '2026-11-16 01:41:22'),
(141, 95, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:35', '2026-11-16 01:41:35'),
(142, 96, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:46', '2026-11-16 01:41:46'),
(143, 97, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:41:57', '2026-11-16 01:41:57'),
(144, 98, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:42:06', '2026-11-16 01:42:06'),
(145, 99, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:42:17', '2026-11-16 01:42:17'),
(146, 100, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:42:30', '2026-11-16 01:42:30'),
(147, 101, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:42:43', '2026-11-16 01:42:43'),
(148, 102, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:43:05', '2026-11-16 01:43:05'),
(149, 103, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:43:21', '2026-11-16 01:43:21'),
(150, 104, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:43:33', '2026-11-16 01:43:33'),
(151, 105, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:43:44', '2026-11-16 01:43:44'),
(152, 106, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:00', '2026-11-16 01:44:00'),
(153, 107, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:09', '2026-11-16 01:44:09'),
(154, 108, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:20', '2026-11-16 01:44:20'),
(155, 109, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:29', '2026-11-16 01:44:29'),
(156, 110, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:36', '2026-11-16 01:44:36'),
(157, 111, 25, 11, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:44:45', '2026-11-16 01:44:45'),
(158, 57, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:45:07', '2026-11-16 01:45:07'),
(159, 58, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:45:13', '2026-11-16 01:45:13'),
(160, 59, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:45:17', '2026-11-16 01:45:17'),
(161, 60, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:45:25', '2026-11-16 01:45:25'),
(162, 61, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:46:09', '2026-11-16 01:46:09'),
(163, 62, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:46:25', '2026-11-16 01:46:25'),
(164, 63, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:46:36', '2026-11-16 01:46:36'),
(165, 64, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:46:52', '2026-11-16 01:46:52'),
(166, 65, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:47:10', '2026-11-16 01:47:10'),
(167, 66, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:47:34', '2026-11-16 01:47:34'),
(168, 67, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:47:48', '2026-11-16 01:47:48'),
(169, 68, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:47:57', '2026-11-16 01:47:57'),
(170, 69, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:48:53', '2026-11-16 01:48:53'),
(171, 70, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:49:06', '2026-11-16 01:49:06'),
(172, 71, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:49:20', '2026-11-16 01:49:20'),
(173, 72, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:49:26', '2026-11-16 01:49:26'),
(174, 73, 25, 26, '2026-07-01', '2027-06-30', 1, '2026-11-16 01:49:33', '2026-11-16 01:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `kode` varchar(30) DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'PG-001', 'Terlambat datang ke sekolah #001', 2, 'Deskripsi pelanggaran nomor 1.', 1, '2026-06-30 11:00:00', '2026-06-30 11:00:00'),
(2, 2, 'PG-002', 'Tidak memakai atribut lengkap #002', 3, 'Deskripsi pelanggaran nomor 2.', 1, '2026-07-01 11:00:00', '2026-07-01 11:00:00'),
(3, 3, 'PG-003', 'Tidak memakai seragam sesuai ketentuan #003', 4, 'Deskripsi pelanggaran nomor 3.', 1, '2026-07-02 11:00:00', '2026-07-02 11:00:00'),
(4, 4, 'PG-004', 'Meninggalkan kelas tanpa izin #004', 5, 'Deskripsi pelanggaran nomor 4.', 1, '2026-07-03 11:00:00', '2026-07-03 11:00:00'),
(5, 5, 'PG-005', 'Tidak mengerjakan tugas #005', 6, 'Deskripsi pelanggaran nomor 5.', 1, '2026-07-04 11:00:00', '2026-07-04 11:00:00'),
(6, 6, 'PG-006', 'Rambut tidak sesuai aturan #006', 7, 'Deskripsi pelanggaran nomor 6.', 1, '2026-07-05 11:00:00', '2026-07-05 11:00:00'),
(7, 7, 'PG-007', 'Membawa barang terlarang #007', 8, 'Deskripsi pelanggaran nomor 7.', 1, '2026-07-06 11:00:00', '2026-07-06 11:00:00'),
(8, 8, 'PG-008', 'Berkata tidak sopan #008', 9, 'Deskripsi pelanggaran nomor 8.', 1, '2026-07-07 11:00:00', '2026-07-07 11:00:00'),
(9, 9, 'PG-009', 'Membuang sampah sembarangan #009', 10, 'Deskripsi pelanggaran nomor 9.', 1, '2026-07-08 11:00:00', '2026-07-08 11:00:00'),
(10, 10, 'PG-010', 'Tidak mengikuti upacara #010', 1, 'Deskripsi pelanggaran nomor 10.', 1, '2026-07-09 11:00:00', '2026-07-09 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran_kategori`
--

INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Kerapian 1', 'Deskripsi kategori untuk pelanggaran kerapian berpakaian dan atribut.', 1, '2026-06-30 18:00:00', '2026-06-30 18:00:00'),
(2, 'Kehadiran 1', 'Deskripsi kategori untuk pelanggaran kehadiran dan ketepatan waktu.', 1, '2026-07-01 18:00:00', '2026-07-01 18:00:00'),
(3, 'Ketertiban 1', 'Deskripsi kategori untuk pelanggaran ketertiban lingkungan sekolah.', 1, '2026-07-02 18:00:00', '2026-07-02 18:00:00'),
(4, 'Perlengkapan 1', 'Deskripsi kategori untuk pelanggaran kelengkapan belajar siswa.', 1, '2026-07-03 18:00:00', '2026-07-03 18:00:00'),
(5, 'Sikap 1', 'Deskripsi kategori untuk pelanggaran sikap dan etika siswa.', 1, '2026-07-04 18:00:00', '2026-07-04 18:00:00'),
(6, 'Kebersihan 1', 'Deskripsi kategori untuk pelanggaran kebersihan kelas dan sekolah.', 1, '2026-07-05 18:00:00', '2026-07-05 18:00:00'),
(7, 'Keamanan 1', 'Deskripsi kategori untuk pelanggaran aturan keamanan sekolah.', 1, '2026-07-06 18:00:00', '2026-07-06 18:00:00'),
(8, 'Akademik 1', 'Deskripsi kategori untuk pelanggaran kegiatan belajar dan tugas.', 1, '2026-07-07 18:00:00', '2026-07-07 18:00:00'),
(9, 'Kerapian 2', 'Deskripsi kategori untuk pelanggaran baju sekolah[cite: 1].', 1, '2026-07-08 18:00:00', '2026-07-08 18:00:00'),
(10, 'Kedisiplinan', 'Memakai sepatu bebas[cite: 1].', 1, '2026-07-09 18:00:00', '2026-07-09 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) DEFAULT NULL,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `kelas_id`, `nama_kelas`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`) VALUES
(107, 25, 88, 'Alfin Rahadian', 11, 'XII RPL 1', 1, '0', 1, 59, '0', '2026-11-16', 'bgg', 2, 'Teguran Lisan', 'selesai', '2026-11-16 02:02:26', '2026-11-16 02:02:37'),
(108, 25, 57, 'Adi Bayu Maulana Putra', 26, 'XII RPL 2', 7, '0', 7, 32, '0', '2026-11-16', 's', 8, 'Peringatan Tertulis (SP 1)', 'selesai', '2026-11-16 02:20:59', '2026-11-16 04:46:02'),
(109, 25, 89, 'Alisa Ramadhani', 11, 'XII RPL 1', 6, '0', 6, 59, '0', '2026-11-16', 'ss', 7, 'Teguran Lisan', 'selesai', '2026-11-16 02:57:46', '2026-11-16 02:57:58'),
(110, 25, 58, 'Agan Basyri Sa\'ady', 26, 'XII RPL 2', 8, '0', 8, 32, '0', '2026-11-16', 'kk', 9, 'Teguran Lisan', 'selesai', '2026-11-16 03:01:11', '2026-11-16 04:46:05'),
(111, 25, 90, 'Alma Aura Livia', 11, 'XII RPL 1', 7, '0', 7, 59, '0', '2026-11-16', 'sad', 8, 'Teguran Lisan', 'selesai', '2026-11-16 03:02:05', '2026-11-16 03:28:02'),
(119, 25, 109, 'Risqi Muhamad Ihwani', 11, 'XII RPL 1', 8, '0', 8, 59, '0', '2026-11-16', 'd', 9, 'Teguran Lisan', 'selesai', '2026-11-16 04:29:07', '2026-11-16 04:32:55'),
(120, 25, 100, 'Muhamad Alfarizi', 11, 'XII RPL 1', 6, '0', 6, 59, '0', '2026-11-16', 'd', 7, 'Skorsing', 'selesai', '2026-11-16 04:33:29', '2026-11-16 04:34:06'),
(121, 25, 102, 'Muhamad Zidane Maulidan', 11, 'XII RPL 1', 1, 'Terlambat datang ke sekolah #001', 1, 59, 'Ari Sandriana, S.Kom', '2026-11-16', 'd', 2, 'Bimbingan Konseling (BK)', 'selesai', '2026-11-16 04:35:24', '2026-11-16 04:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(30) DEFAULT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '102410544', '0000000001', 'Adittia Nur Lantip', 'L', '2010-01-15', 'Jl. Pendidikan No. 1, Tasikmalaya', 1, '2025-12-31 11:00:00', '2026-11-09 10:59:48'),
(2, '102410545', '0000000002', 'Agnia Rosya Noerillah', 'P', '2010-02-20', 'Jl. Pendidikan No. 2, Tasikmalaya', 1, '2026-02-01 11:00:00', '2026-02-01 11:00:00'),
(3, '102410546', '0000000003', 'Anisa Amirulla', 'P', '2010-03-12', 'Jl. Pendidikan No. 3, Tasikmalaya', 1, '2026-03-02 11:00:00', '2026-03-02 11:00:00'),
(4, '102410547', '0000000004', 'Aradea Kandi', 'L', '2010-04-25', 'Jl. Pendidikan No. 4, Tasikmalaya', 1, '2026-04-03 11:00:00', '2026-04-03 11:00:00'),
(5, '102410548', '0000000005', 'Arfa Diesa Okmaleliza', 'P', '2010-05-18', 'Jl. Pendidikan No. 5, Tasikmalaya', 1, '2026-05-04 11:00:00', '2026-05-04 11:00:00'),
(6, '102410549', '0000000006', 'Fitri Nur Syami', 'P', '2010-06-07', 'Jl. Pendidikan No. 6, Tasikmalaya', 1, '2026-06-05 11:00:00', '2026-06-05 11:00:00'),
(7, '102410550', '0000000007', 'Junita Putri Nur Rahman', 'P', '2010-07-22', 'Jl. Pendidikan No. 7, Tasikmalaya', 1, '2026-07-06 11:00:00', '2026-07-06 11:00:00'),
(8, '102410551', '0000000008', 'Mohammad Atalarik Ali Syahbana', 'L', '2010-08-14', 'Jl. Pendidikan No. 8, Tasikmalaya', 1, '2026-08-07 11:00:00', '2026-08-07 11:00:00'),
(9, '102410552', '0000000009', 'Muhammad Fahril Fahrurozi', 'L', '2010-09-30', 'Jl. Pendidikan No. 9, Tasikmalaya', 1, '2026-09-08 11:00:00', '2026-09-08 11:00:00'),
(10, '102410750', '0000000010', 'Muhammad Adnan Syaeful Islami', 'L', '2010-10-11', 'Jl. Pendidikan No. 10, Tasikmalaya', 1, '2026-10-09 11:00:00', '2026-10-09 11:00:00'),
(11, '102410553', '0000000011', 'Muhammad Zahru Aqila Fikri', 'L', '2010-11-05', 'Jl. Pendidikan No. 11, Tasikmalaya', 1, '2026-11-10 11:00:00', '2026-11-10 11:00:00'),
(12, '102410554', '0000000012', 'Nadia Zulfa Soraya', 'P', '2010-12-19', 'Jl. Pendidikan No. 12, Tasikmalaya', 1, '2026-12-11 11:00:00', '2026-12-11 11:00:00'),
(13, '102410555', '0000000013', 'Nazril Ilham', 'L', '2011-01-08', 'Jl. Pendidikan No. 13, Tasikmalaya', 1, '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(14, '102410556', '0000000014', 'Puspita Dewi', 'P', '2011-02-17', 'Jl. Pendidikan No. 14, Tasikmalaya', 1, '2026-02-13 11:00:00', '2026-02-13 11:00:00'),
(15, '102410558', '0000000015', 'Rakha Muhammad Naykal', 'L', '2011-03-29', 'Jl. Pendidikan No. 15, Tasikmalaya', 1, '2026-03-14 11:00:00', '2026-03-14 11:00:00'),
(16, '102410559', '0000000016', 'Rani Melani', 'P', '2011-04-13', 'Jl. Pendidikan No. 16, Tasikmalaya', 1, '2026-04-15 11:00:00', '2026-04-15 11:00:00'),
(17, '102410560', '0000000017', 'Reihan Muzaki', 'L', '2011-05-21', 'Jl. Pendidikan No. 17, Tasikmalaya', 1, '2026-05-16 11:00:00', '2026-05-16 11:00:00'),
(18, '102410561', '0000000018', 'Resa Juliansah', 'L', '2011-06-09', 'Jl. Pendidikan No. 18, Tasikmalaya', 1, '2026-06-17 11:00:00', '2026-06-17 11:00:00'),
(19, '102410562', '0000000019', 'Rizki Permata Sugianto', 'L', '2011-07-27', 'Jl. Pendidikan No. 19, Tasikmalaya', 1, '2026-07-18 11:00:00', '2026-07-18 11:00:00'),
(20, '102410563', '0000000020', 'Sahira Firaj', 'P', '2011-08-16', 'Jl. Pendidikan No. 20, Tasikmalaya', 1, '2026-08-19 11:00:00', '2026-08-19 11:00:00'),
(21, '102410564', '0000000021', 'Sendi Mardian', 'L', '2011-09-04', 'Jl. Pendidikan No. 21, Tasikmalaya', 1, '2026-09-20 11:00:00', '0000-00-00 00:00:00'),
(22, '102410755', '0000000022', 'Shahaidan Nizzar', 'L', '2011-10-23', 'Jl. Pendidikan No. 22, Tasikmalaya', 1, '2026-10-21 11:00:00', '2026-10-21 11:00:00'),
(23, '102410565', '0000000023', 'Sherly Satriani', 'P', '2011-11-14', 'Jl. Pendidikan No. 23, Tasikmalaya', 1, '2026-11-22 11:00:00', '2026-11-22 11:00:00'),
(24, '102410566', '0000000024', 'Sofinurjanah', 'P', '2011-12-02', 'Jl. Pendidikan No. 24, Tasikmalaya', 1, '2026-12-23 11:00:00', '2026-12-23 11:00:00'),
(25, '102410567', '0000000025', 'Suraiya Salsabila Azzahira', 'P', '2010-01-28', 'Jl. Pendidikan No. 25, Tasikmalaya', 1, '2026-01-24 11:00:00', '2026-01-24 11:00:00'),
(26, '102410568', '0000000026', 'Toni Antonio', 'L', '2010-02-11', 'Jl. Pendidikan No. 26, Tasikmalaya', 1, '2026-02-25 11:00:00', '2026-02-25 11:00:00'),
(27, '102611149', '0000000027', 'Abdul Malik Fajar', 'L', '2009-01-12', 'Jl. Contoh Melati No. 1, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(28, '102611150', '0000000028', 'Agin Ginanjar Saputra', 'L', '2009-03-21', 'Jl. Contoh Mawar No. 2, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(29, '102611151', '0000000029', 'Algi Athari', 'L', '2009-05-14', 'Jl. Contoh Anggrek No. 3, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(30, '102611152', '0000000030', 'Arystya Apriliandi Franki', 'L', '2009-07-09', 'Jl. Contoh Kenanga No. 4, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(31, '102611153', '0000000031', 'Billah Abdillah', 'L', '2009-02-18', 'Jl. Contoh Dahlia No. 5, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(32, '102611154', '0000000032', 'Dhimas Dwi Ramadhan', 'L', '2009-04-25', 'Jl. Contoh Teratai No. 6, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(33, '102611155', '0000000033', 'Fadil Ramdhani', 'L', '2009-06-11', 'Jl. Contoh Flamboyan No. 7, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(34, '102611478', '0000000034', 'Faraz Fachri Muharram', 'L', '2009-08-17', 'Jl. Contoh Cempaka No. 8, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(35, '102611157', '0000000035', 'Haikal Ilham Suhanda', 'L', '2009-09-23', 'Jl. Contoh Kamboja No. 9, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(36, '102611158', '0000000036', 'Laodra Roba Ahfiana', 'L', '2009-10-05', 'Jl. Contoh Cemara No. 10, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(37, '102611159', '0000000037', 'Lintang Febrian Maulana', 'L', '2009-11-19', 'Jl. Contoh Pinus No. 11, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(38, '102611160', '0000000038', 'Lisan Dzakir Ramadhan', 'L', '2009-12-08', 'Jl. Contoh Merpati No. 12, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(39, '102611161', '0000000039', 'Muhamad Abylah Azam', 'L', '2010-01-16', 'Jl. Contoh Rajawali No. 13, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(40, '102611162', '0000000040', 'Muhamad Algiansyah', 'L', '2010-02-22', 'Jl. Contoh Garuda No. 14, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(41, '102611163', '0000000041', 'Muhamad Azril Yasin', 'L', '2010-03-13', 'Jl. Contoh Merak No. 15, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(42, '102611164', '0000000042', 'Muhamad Itshar', 'L', '2010-04-27', 'Jl. Contoh Elang No. 16, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(43, '102611165', '0000000043', 'Muhamad Nasabandy', 'L', '2010-05-09', 'Jl. Contoh Rajin No. 17, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(44, '102611166', '0000000044', 'Muhammad Adam Efathurrohman', 'L', '2010-06-15', 'Jl. Contoh Sukamaju No. 18, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(45, '102611167', '0000000045', 'Muhammad Dzikri Abdul Jalil', 'L', '2010-07-20', 'Jl. Contoh Sukaraja No. 19, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(46, '102611476', '0000000046', 'Muhammad Rafa Firmansyah', 'L', '2010-08-11', 'Jl. Contoh Sukasari No. 20, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(47, '102611169', '0000000047', 'Muhammad Rizky Hidayatullah', 'L', '2010-09-24', 'Jl. Contoh Cibereum No. 21, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(48, '102611170', '0000000048', 'Nasta Pratama Supriatna', 'L', '2010-10-06', 'Jl. Contoh Cihideung No. 22, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(49, '102611171', '0000000049', 'Raditia Purnama', 'L', '2010-11-18', 'Jl. Contoh Indihiang No. 23, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(50, '102611172', '0000000050', 'Rahmat Suparman', 'L', '2010-12-12', 'Jl. Contoh Kawalu No. 24, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(51, '102611173', '0000000051', 'Rama Manramsyah Gustian Pratama', 'L', '2009-02-05', 'Jl. Contoh Mangkubumi No. 25, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(52, '102611174', '0000000052', 'Rehan Nadi Saputra', 'L', '2009-04-19', 'Jl. Contoh Tamansari No. 26, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(53, '102611175', '0000000053', 'Rof Rof Al Arof Subul Amin', 'L', '2009-06-23', 'Jl. Contoh Singaparna No. 27, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(54, '102611176', '0000000054', 'Sandi Permana', 'L', '2009-08-08', 'Jl. Contoh Manonjaya No. 28, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(55, '102611177', '0000000055', 'Tubagus Rizqi', 'L', '2009-10-14', 'Jl. Contoh Rajapolah No. 29, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(56, '102611178', '0000000056', 'Wildan Nurjaman', 'L', '2009-12-20', 'Jl. Contoh Cisayong No. 30, Tasikmalaya', 1, '2026-11-05 18:18:37', '2026-11-05 18:18:37'),
(57, '102410341', '0000000057', 'Adi Bayu Maulana Putra', 'L', '2008-01-12', 'Jl. Melati, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(58, '102410343', '0000000058', 'Agan Basyri Sa\'ady', 'L', '2008-02-15', 'Jl. Mawar, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(59, '102410344', '0000000059', 'Ali Rahmat Mutaqin', 'L', '2008-03-21', 'Jl. Anggrek, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(60, '102410345', '0000000060', 'Alzi Kusnendi', 'L', '2008-04-09', 'Jl. Kenanga, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(61, '102410346', '0000000061', 'Andika Nugraha', 'L', '2008-05-17', 'Jl. Dahlia, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(62, '102410347', '0000000062', 'Andri Mabrur', 'L', '2008-06-11', 'Jl. Teratai, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(63, '102410349', '0000000063', 'Dafa Ezar Ramadan', 'L', '2008-07-23', 'Jl. Cempaka, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(64, '102410350', '0000000064', 'Defri Muhamad Ramadhan', 'L', '2008-08-14', 'Jl. Cemara, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(65, '102410351', '0000000065', 'Deva Al Fauzan', 'L', '2008-09-05', 'Jl. Merpati, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(66, '102410352', '0000000066', 'Dika Ardiansyah Putra', 'L', '2008-10-19', 'Jl. Rajawali, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(67, '102410353', '0000000067', 'Fadli Muhammad Ramdhan', 'L', '2008-11-08', 'Jl. Garuda, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(68, '102410354', '0000000068', 'Febi Danil Gunawan', 'L', '2008-12-16', 'Jl. Merak, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(69, '102410355', '0000000069', 'Gabriel Alhazmi Adiyasa', 'L', '2008-01-25', 'Jl. Elang, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(70, '102410356', '0000000070', 'Gisna Sufi Ginanjar', 'L', '2008-02-27', 'Jl. Pinus, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(71, '102410358', '0000000071', 'Lutfi Indriawan', 'L', '2008-03-13', 'Jl. Flamboyan, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(72, '102410359', '0000000072', 'Moch Alif Sayyandra Abipraya', 'L', '2008-04-22', 'Jl. Sukamaju, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(73, '102410360', '0000000073', 'Muhamad Al Mu\'min Akbar', 'L', '2008-05-06', 'Jl. Sukaraja, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(74, '102410361', '0000000074', 'Muhamad Rafi Ramdani', 'L', '2008-06-18', 'Jl. Sukasari, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(75, '102410362', '0000000075', 'Muhamad Zulpi Zulkipli', 'L', '2008-07-29', 'Jl. Cibeureum, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(76, '102410364', '0000000076', 'Muhammad Faisal Arrizal', 'L', '2008-08-12', 'Jl. Cihideung, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(77, '102410366', '0000000077', 'Muhammad Rafliyatu Rizqi Nur Rohman', 'L', '2008-09-24', 'Jl. Indihiang, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(78, '102410367', '0000000078', 'Muhammad Ridho Qushoiri', 'L', '2008-10-07', 'Jl. Kawalu, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(79, '102410368', '0000000079', 'Raditia Nugraha', 'L', '2008-11-15', 'Jl. Tamansari, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(80, '102410369', '0000000080', 'Raeval Legya Rachman', 'L', '2008-12-03', 'Jl. Mangkubumi, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(81, '102410370', '0000000081', 'Rafa Azkia', 'L', '2008-01-19', 'Jl. Singaparna, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(82, '102410371', '0000000082', 'Refan Sultansah', 'L', '2008-02-08', 'Jl. Rajapolah, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(83, '102410372', '0000000083', 'Reihan Al Azhar', 'L', '2008-03-26', 'Jl. Cisayong, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(84, '112510757', '0000000084', 'Sani Achmad Fikri', 'L', '2008-04-16', 'Jl. Leuwisari, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(85, '102410373', '0000000085', 'Sendy Ramdhani', 'L', '2008-05-28', 'Jl. Manonjaya, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(86, '102410374', '0000000086', 'Surahman Wahdan Andika', 'L', '2008-06-09', 'Jl. Salawu, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(87, '102410375', '0000000087', 'Tahmid Hidayatulloh', 'L', '2008-07-17', 'Jl. Sukaratu, Tasikmalaya', 1, '2026-11-05 18:30:14', '2026-11-05 18:30:14'),
(88, '102410520', '0091000001', 'Alfin Rahadian', 'L', '2008-01-12', 'Jl. Pendidikan No. 1', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(89, '102410521', '0091000002', 'Alisa Ramadhani', 'P', '2008-02-15', 'Jl. Pendidikan No. 2', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(90, '102410522', '0091000003', 'Alma Aura Livia', 'P', '2008-03-20', 'Jl. Pendidikan No. 3', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(91, '102410523', '0091000004', 'Anis Khoerunnisa Nur Ramadani', 'P', '2008-04-18', 'Jl. Pendidikan No. 4', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(92, '102410524', '0091000005', 'Azmil Amira', 'P', '2008-05-22', 'Jl. Pendidikan No. 5', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(93, '102410525', '0091000006', 'Azril Akbar Fawji Sedillah', 'L', '2008-06-10', 'Jl. Pendidikan No. 6', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(94, '102410527', '0091000007', 'Erlangga Saputra Hidayat', 'L', '2008-07-14', 'Jl. Pendidikan No. 7', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(95, '102410528', '0091000008', 'Hilma Humma Tunnajah', 'P', '2008-08-19', 'Jl. Pendidikan No. 8', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(96, '102410529', '0091000009', 'Ilma Najya', 'P', '2008-09-12', 'Jl. Pendidikan No. 9', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(97, '102410531', '0091000010', 'Julianda Dharma Pangestu', 'L', '2008-10-16', 'Jl. Pendidikan No. 10', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(98, '102410532', '0091000011', 'Kayla Nur Fadilah', 'P', '2008-11-21', 'Jl. Pendidikan No. 11', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(99, '102410533', '0091000012', 'Moza Dina Nabillah', 'P', '2008-12-09', 'Jl. Pendidikan No. 12', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(100, '102410534', '0091000013', 'Muhamad Alfarizi', 'L', '2008-01-25', 'Jl. Pendidikan No. 13', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(101, '102410535', '0091000014', 'Muhamad Fahrin Adam', 'L', '2008-02-27', 'Jl. Pendidikan No. 14', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(102, '102410536', '0091000015', 'Muhamad Zidane Maulidan', 'L', '2008-03-16', 'Jl. Pendidikan No. 15', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(103, '102410537', '0091000016', 'Muhamad Zulfan Nuralfansyah', 'L', '2008-04-24', 'Jl. Pendidikan No. 16', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(104, '102410538', '0091000017', 'Natiya Nur Azkiya', 'P', '2008-05-13', 'Jl. Pendidikan No. 17', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(105, '102410539', '0091000018', 'Novita Puspitasari', 'P', '2008-06-29', 'Jl. Pendidikan No. 18', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(106, '102410540', '0091000019', 'Nucky Andreas Febrian', 'L', '2008-07-08', 'Jl. Pendidikan No. 19', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(107, '102410543', '0091000020', 'Raditya Jeti Prasetya', 'L', '2008-08-26', 'Jl. Pendidikan No. 20', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(108, '102410741', '0091000021', 'Rangga Ramadiyanto Nurhakim', 'L', '2008-09-17', 'Jl. Pendidikan No. 21', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(109, '102410541', '0091000022', 'Risqi Muhamad Ihwani', 'L', '2008-10-23', 'Jl. Pendidikan No. 22', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(110, '102410542', '0091000023', 'Salwa Sabilatul Barakah', 'P', '2008-11-11', 'Jl. Pendidikan No. 23', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08'),
(111, '102410753', '0091000024', 'Tazkia Zahra', 'P', '2008-12-20', 'Jl. Pendidikan No. 24', 1, '2026-11-05 18:57:08', '2026-11-05 18:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-07-01 01:00:00', '2026-07-01 01:00:00'),
(2, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-07-02 01:00:00', '2026-07-02 01:00:00'),
(3, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-07-03 01:00:00', '2026-07-03 01:00:00'),
(4, '2020/2021', '2020-07-01', '2021-06-30', 0, '2026-07-04 01:00:00', '2026-07-04 01:00:00'),
(5, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-07-05 01:00:00', '2026-07-05 01:00:00'),
(6, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-07-06 01:00:00', '2026-07-06 01:00:00'),
(7, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-07-07 01:00:00', '2026-07-07 01:00:00'),
(8, '2021/2022', '2021-07-01', '2022-06-30', 0, '2026-07-08 01:00:00', '2026-07-08 01:00:00'),
(9, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-07-09 01:00:00', '2026-07-09 01:00:00'),
(10, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-07-10 01:00:00', '2026-07-10 01:00:00'),
(11, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-07-11 01:00:00', '2026-07-11 01:00:00'),
(12, '2022/2023', '2022-07-01', '2023-06-30', 0, '2026-07-12 01:00:00', '2026-07-12 01:00:00'),
(13, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-07-13 01:00:00', '2026-07-13 01:00:00'),
(14, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-07-14 01:00:00', '2026-07-14 01:00:00'),
(15, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-07-15 01:00:00', '2026-07-15 01:00:00'),
(16, '2023/2024', '2023-07-01', '2024-06-30', 0, '2026-07-16 01:00:00', '2026-07-16 01:00:00'),
(17, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-07-17 01:00:00', '2026-07-17 01:00:00'),
(18, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-07-18 01:00:00', '2026-07-18 01:00:00'),
(19, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-07-19 01:00:00', '2026-07-19 01:00:00'),
(20, '2024/2025', '2024-07-01', '2025-06-30', 0, '2026-07-20 01:00:00', '2026-07-20 01:00:00'),
(21, '2025/2026', '2025-07-01', '2026-06-30', 0, '2026-07-21 01:00:00', '2026-07-21 01:00:00'),
(22, '2025/2026', '2025-07-01', '2026-06-30', 0, '2026-07-22 01:00:00', '2026-07-22 01:00:00'),
(23, '2025/2026', '2025-07-01', '2026-06-30', 0, '2026-07-23 01:00:00', '2026-07-23 01:00:00'),
(24, '2025/2026', '2025-07-01', '2026-06-30', 0, '2026-07-24 01:00:00', '2026-07-24 01:00:00'),
(25, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-07-25 01:00:00', '2026-07-25 01:00:00'),
(29, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-07-29 01:00:00', '2026-07-29 01:00:00'),
(30, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-07-30 01:00:00', '2026-07-30 01:00:00'),
(31, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-07-31 01:00:00', '2026-07-31 01:00:00'),
(32, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-07-01 01:00:00', '2026-07-01 01:00:00'),
(33, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-07-02 01:00:00', '2026-07-02 01:00:00'),
(34, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-07-03 01:00:00', '2026-07-03 01:00:00'),
(35, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-07-04 01:00:00', '2026-07-04 01:00:00'),
(36, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-07-05 01:00:00', '2026-07-05 01:00:00'),
(37, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-07-06 01:00:00', '2026-07-06 01:00:00'),
(38, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-07-07 01:00:00', '2026-07-07 01:00:00'),
(39, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-07-08 01:00:00', '2026-07-08 01:00:00'),
(40, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-07-09 01:00:00', '2026-07-09 01:00:00'),
(41, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-07-10 01:00:00', '2026-07-10 01:00:00'),
(42, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-07-11 01:00:00', '2026-07-11 01:00:00'),
(43, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-07-12 01:00:00', '2026-07-12 01:00:00'),
(44, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-07-13 01:00:00', '2026-07-13 01:00:00'),
(45, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-07-14 01:00:00', '2026-07-14 01:00:00'),
(46, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-07-15 01:00:00', '2026-07-15 01:00:00'),
(47, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-07-16 01:00:00', '2026-07-16 01:00:00'),
(48, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-07-17 01:00:00', '2026-07-17 01:00:00'),
(49, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-07-18 01:00:00', '2026-07-18 01:00:00'),
(50, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-07-19 01:00:00', '2026-07-19 01:00:00'),
(51, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-07-20 01:00:00', '2026-07-20 01:00:00'),
(52, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-07-21 01:00:00', '2026-07-21 01:00:00'),
(53, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-07-22 01:00:00', '2026-07-22 01:00:00'),
(54, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-07-23 01:00:00', '2026-07-23 01:00:00'),
(55, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-07-24 01:00:00', '2026-07-24 01:00:00'),
(56, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-07-25 01:00:00', '2026-07-25 01:00:00'),
(57, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-07-26 01:00:00', '2026-07-26 01:00:00'),
(58, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-07-27 01:00:00', '2026-07-27 01:00:00'),
(59, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-07-28 01:00:00', '2026-07-28 01:00:00'),
(60, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-07-29 01:00:00', '2026-07-29 01:00:00'),
(61, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-07-30 01:00:00', '2026-07-30 01:00:00'),
(62, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-07-31 01:00:00', '2026-07-31 01:00:00'),
(63, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-07-01 01:00:00', '2026-07-01 01:00:00'),
(64, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-07-02 01:00:00', '2026-07-02 01:00:00'),
(65, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-07-03 01:00:00', '2026-07-03 01:00:00'),
(66, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-07-04 01:00:00', '2026-07-04 01:00:00'),
(67, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-07-05 01:00:00', '2026-07-05 01:00:00'),
(68, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-07-06 01:00:00', '2026-07-06 01:00:00'),
(69, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-07-07 01:00:00', '2026-07-07 01:00:00'),
(70, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-07-08 01:00:00', '2026-07-08 01:00:00'),
(71, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-07-09 01:00:00', '2026-07-09 01:00:00'),
(72, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-07-10 01:00:00', '2026-07-10 01:00:00'),
(73, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-07-11 01:00:00', '2026-07-11 01:00:00'),
(74, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-07-12 01:00:00', '2026-07-12 01:00:00'),
(75, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-07-13 01:00:00', '2026-07-13 01:00:00'),
(76, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-07-14 01:00:00', '2026-07-14 01:00:00'),
(77, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-07-15 01:00:00', '2026-07-15 01:00:00'),
(78, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-07-16 01:00:00', '2026-07-16 01:00:00'),
(79, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-07-17 01:00:00', '2026-07-17 01:00:00'),
(80, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-07-18 01:00:00', '2026-07-18 01:00:00'),
(81, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-07-19 01:00:00', '2026-07-19 01:00:00'),
(82, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-07-20 01:00:00', '2026-07-20 01:00:00'),
(83, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-07-21 01:00:00', '2026-07-21 01:00:00'),
(84, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-07-22 01:00:00', '2026-07-22 01:00:00'),
(85, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-07-23 01:00:00', '2026-07-23 01:00:00'),
(86, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-07-24 01:00:00', '2026-07-24 01:00:00'),
(87, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-07-25 01:00:00', '2026-07-25 01:00:00'),
(88, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-07-26 01:00:00', '2026-07-26 01:00:00'),
(89, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-07-27 01:00:00', '2026-07-27 01:00:00'),
(90, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-07-28 01:00:00', '2026-07-28 01:00:00'),
(91, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-07-29 01:00:00', '2026-07-29 01:00:00'),
(92, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-07-30 01:00:00', '2026-07-30 01:00:00'),
(93, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-07-31 01:00:00', '2026-07-31 01:00:00'),
(94, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-07-01 01:00:00', '2026-07-01 01:00:00'),
(95, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-07-02 01:00:00', '2026-07-02 01:00:00'),
(96, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-07-03 01:00:00', '2026-07-03 01:00:00'),
(97, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-07-04 01:00:00', '2026-07-04 01:00:00'),
(98, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-07-05 01:00:00', '2026-07-05 01:00:00'),
(99, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-07-06 01:00:00', '2026-07-06 01:00:00'),
(100, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-07-07 01:00:00', '2026-07-07 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(80) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(104, 'administrator', 'admin', 'admin@gmail.com', '2026-10-02 01:14:07', '$2y$10$jBUNIkbpmaWi7C3ZgBeiD.ju6Np/LGvb/roqxrkheA6.TRpteGL1m', '', 'admin', '2026-10-02 01:14:07', '2026-10-02 01:14:07'),
(106, 'guru', 'guru', 'guru@gmail.com', '2026-10-02 03:22:57', '$2y$10$NJtQ5MEq5d.4PsusaHO9xu3kkxeaR6g1M7S8T6VB900fHauavKh5K', '', 'guru', '2026-10-02 03:22:57', '2026-10-02 03:22:57'),
(107, 'Wawan Kurniawan, S.Pd.I', 'Wawan Kurniawan, S.Pd.I', 'WawanKurniawan@gmail.com', '2026-11-11 05:16:54', '$2y$10$jBUNIkbpmaWi7C3ZgBeiD.ju6Np/LGvb/roqxrkheA6.TRpteGL1m', '', 'guru', '2026-11-11 05:16:54', '2026-11-14 10:14:07'),
(108, 'Ai Nuryaidah, S.Pd', 'Ai Nuryaidah, S.Pd', 'ai.nuryaidah.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(109, 'Maya Permata Endah, S.Pd', 'Maya Permata Endah, S.Pd', 'maya.permata.endah.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(110, 'Dody Jaenudin, S.Pd', 'Dody Jaenudin, S.Pd', 'dody.jaenudin.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(111, 'Dedi Kardiman, S.Pd', 'Dedi Kardiman, S.Pd', 'dedi.kardiman.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(112, 'Uca Musa Yusenda, S.Pd', 'Uca Musa Yusenda, S.Pd', 'uca.musa.yusenda.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(113, 'Drs. Ahmad Nur Mukhlis', 'Drs. Ahmad Nur Mukhlis', 'drs.ahmad.nur.mukhlis@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(114, 'Drs. Heri', 'Drs. Heri', 'drs.heri@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(115, 'Asep Bastaman', 'Asep Bastaman', 'asep.bastaman@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(116, 'H. Encep Maemun, S.T., M.Pd', 'H. Encep Maemun, S.T., M.Pd', 'h.encep.maemun.s.t.m.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(117, 'Opik', 'Opik', 'opik@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(118, 'Ida Farida, S.Pd', 'Ida Farida, S.Pd', 'ida.farida.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(119, 'Idah Mahmudah, S.Pd', 'Idah Mahmudah, S.Pd', 'idah.mahmudah.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(120, 'Drs. Muhamad Ali', 'Drs. Muhamad Ali', 'drs.muhamad.ali@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(121, 'Helmi Ali Akbar, S.Pd.I', 'Helmi Ali Akbar, S.Pd.I', 'helmi.ali.akbar.s.pd.i@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(122, 'Syeni Suswarini, S.Pd', 'Syeni Suswarini, S.Pd', 'syeni.suswarini.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(123, 'Wiwid Yulianti, S.Pd', 'Wiwid Yulianti, S.Pd', 'wiwid.yulianti.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(124, 'Ansor Muhajir, S.T', 'Ansor Muhajir, S.T', 'ansor.muhajir.s.t@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(125, 'Ogi Gaosun Gais, S.Pd.I', 'Ogi Gaosun Gais, S.Pd.I', 'ogi.gaosun.gais.s.pd.i@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(126, 'Sandi Mulyana, S.Pd', 'Sandi Mulyana, S.Pd', 'sandi.mulyana.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(127, 'Erna Suryani, S.Pd', 'Erna Suryani, S.Pd', 'erna.suryani.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(128, 'Ai Wahidah, S.Ag', 'Ai Wahidah, S.Ag', 'ai.wahidah.s.ag@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(129, 'Chandra Aditya Yusup, S.Pd', 'Chandra Aditya Yusup, S.Pd', 'chandra.aditya.yusup.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(130, 'Arif Kusmawan, S.Pd', 'Arif Kusmawan, S.Pd', 'arif.kusmawan.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(131, 'Asep Taufiq Hidayat, S.Kom', 'Asep Taufiq Hidayat, S.Kom', 'asep.taufiq.hidayat.s.kom@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(132, 'Rendy Ichsan Kusumah, S.T', 'Rendy Ichsan Kusumah, S.T', 'rendy.ichsan.kusumah.s.t@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(133, 'Feri Rahmawan, S.Pd', 'Feri Rahmawan, S.Pd', 'feri.rahmawan.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(134, 'Yaneu Nurdian, S.Pd, M.Pd', 'Yaneu Nurdian, S.Pd, M.Pd', 'yaneu.nurdian.s.pd.m.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(135, 'Ucu Muhammad Khoer N, S.Pd', 'Ucu Muhammad Khoer N, S.Pd', 'ucu.muhammad.khoer.n.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(136, 'Mira Dwi Rahmawati, S.Ag', 'Mira Dwi Rahmawati, S.Ag', 'mira.dwi.rahmawati.s.ag@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(137, 'Aldi Maulana, S.Pd', 'Aldi Maulana, S.Pd', 'aldimaulana@gmail.com', '2026-11-11 05:16:54', '$2y$10$hhAnprlodJLDZT576HstQeE.dgGaRD6zy02s7LhQOXmIgfKziyGLi', '', 'Wali_Kelas', '2026-11-11 05:16:54', '2026-11-16 01:50:49'),
(138, 'Eli Rosliah, S.Pd', 'Eli Rosliah, S.Pd', 'eli.rosliah.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(139, 'Isni Nur Fitriani, S.Pd', 'Isni Nur Fitriani, S.Pd', 'isni.nur.fitriani.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(140, 'Aanisah Putri Nur Aini, S.Pd', 'Aanisah Putri Nur Aini, S.Pd', 'aanisah.putri.nur.aini.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(141, 'Yusi Luthfiani, S.Sos.I', 'Yusi Luthfiani, S.Sos.I', 'yusi.luthfiani.s.sos.i@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(142, 'Lilis Rustandy Mawardi, S.Pd.,', 'Lilis Rustandy Mawardi, S.Pd.,', 'lilis.rustandy.mawardi.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(143, 'Yusrin Noer Sabrina, S.Pd', 'Yusrin Noer Sabrina, S.Pd', 'yusrin.noer.sabrina.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(144, 'Senny Indayani, S.Pd', 'Senny Indayani, S.Pd', 'senny.indayani.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(145, 'Ilma Amalia, S.Pd', 'Ilma Amalia, S.Pd', 'ilma.amalia.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(146, 'Alvira Sofwatunnisa, S.Pd', 'Alvira Sofwatunnisa, S.Pd', 'alvira.sofwatunnisa.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(147, 'Risna Andriyani, S.Pd', 'Risna Andriyani, S.Pd', 'risna.andriyani.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(148, 'Dra. Tintin Noerhayati', 'Dra. Tintin Noerhayati', 'dra.tintin.noerhayati@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(149, 'Rizkia Fahira, S.Pd', 'Rizkia Fahira, S.Pd', 'rizkia.fahira.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(150, 'Frasetio', 'Frasetio', 'frasetio@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(151, 'Ratnaningsih Hatil Aeni, S.Pd', 'Ratnaningsih Hatil Aeni, S.Pd', 'ratnaningsih.hatil.aeni.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(152, 'Dheny Darajatul Hasani, S.Pd', 'Dheny Darajatul Hasani, S.Pd', 'dheny.darajatul.hasani.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(153, 'Fahrullah, S.Pd', 'Fahrullah, S.Pd', 'fahrullah.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(154, 'Aan Andriana, S.T', 'Aan Andriana, S.T', 'aan.andriana.s.t@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(155, 'Fitri Fauziah, S.T', 'Fitri Fauziah, S.T', 'fitri.fauziah.s.t@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(156, 'Nida Aulia, S.Pd', 'Nida Aulia, S.Pd', 'nida.aulia.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(157, 'Sri Nurrizki Putri Krisnadi, S', 'Sri Nurrizki Putri Krisnadi, S', 'sri.nurrizki.putri.krisnadi.s@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(158, 'Riesy Putri Navia, S.Kom', 'Riesy Putri Navia, S.Kom', 'riesy.putri.navia.s.kom@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(159, 'Rurry Febriani Maryadi, S.Pd', 'Rurry Febriani Maryadi, S.Pd', 'rurry.febriani.maryadi.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(160, 'Irvan Rizal, S.Pd', 'Irvan Rizal, S.Pd', 'irvan.rizal.s.pd@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(161, 'ALPA', 'ALPA', 'alpa@gmail.com', '2026-11-11 05:16:54', '$2y$12$XVFV7RDWOV8CARg70T71MOv8bYQeAClr0Vdg2QiP5x9jrkt5UlQHq', '', 'guru', '2026-11-11 05:16:54', '2026-11-11 05:16:54'),
(162, 'Ari Sandriana, S.Kom', 'Ari Sandriana, S.Kom', 'AriSandriana@gmail.com', '2026-11-11 05:16:54', '$2y$10$kVB5z5zvMrSPQaTBmK6ZpeLw9mr8aD7KzQuzRvRlzGcAqTifmhzGa', '', 'Wali_Kelas', '2026-11-11 05:16:54', '2026-11-16 01:50:30'),
(170, 'Pak rahmat', NULL, 'rahmat@gmail.com', '2026-11-14 08:25:21', '$2y$10$RnnvktoRmmhObOu0F//jvOqwff.0wd4WdQWm5WoBMXzLXqcpZyuBO', '', 'guru', '2026-11-14 08:25:21', '2026-11-14 08:52:21'),
(171, 'Mamat hasep', NULL, 'aep@gmail.com', '2026-11-14 09:30:13', '$2y$10$lFj7Nb.znCOnrj5lr5DJjeI85skVYcWn7.Wcp7caCqzZlU.W/5kt.', '', 'guru', '2026-11-14 09:30:13', '2026-11-14 09:30:13'),
(172, 'Pak rahmat abdul', NULL, 'rahmat@gmail.com', '2026-11-14 09:45:39', '$2y$10$zuMwT7wB0mjickxp.P2dx.PKK216twO2Mmoh3qVDb0omtllNo4Xfm', '', 'guru', '2026-11-14 09:45:39', '2026-11-14 09:45:55'),
(173, 'Pak rahmat1111', NULL, 'rahmat@gmail.com', '2026-11-14 10:15:26', '$2y$10$RwHG7C0eX9zjRRt/nYb9wOyo8l4N.frDx/dsUd7cwnZFz.Ps.sauK', '', 'guru', '2026-11-14 10:15:26', '2026-11-14 10:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_wali_kelas`
--

INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(103, 25, 11, 59, '2026-11-11', '2027-11-11', 1, '2026-11-11 05:23:32', '2026-11-11 05:23:32'),
(104, 25, 26, 32, '2026-11-11', '2027-11-11', 1, '2026-11-11 07:38:18', '2026-11-11 07:38:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_t_kelas_tahun_ajaran_id` (`tahun_ajaran_id`);

--
-- Indexes for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indexes for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`);

--
-- Indexes for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `pelanggaran_id` (`pelanggaran_id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_t_users_username` (`username`);

--
-- Indexes for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD CONSTRAINT `t_kelas_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_kelas_siswa_ibfk_3` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD CONSTRAINT `t_pelanggaran_ibfk_1` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_4` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_6` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_wali_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_wali_kelas_ibfk_3` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

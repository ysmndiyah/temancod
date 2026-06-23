-- =========================================================================
-- Database Schema for TemanCOD
-- =========================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `teman cod`;
USE `teman cod`;

-- 1. Table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(50) DEFAULT NULL,
  `role` ENUM('user', 'companion', 'admin') NOT NULL DEFAULT 'user',
  `foto` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table Companions
CREATE TABLE IF NOT EXISTS `Companions` (
  `Id` INT AUTO_INCREMENT PRIMARY KEY,
  `User_id` INT NOT NULL,
  `Deskripsi` TEXT DEFAULT NULL,
  `Harga_per_jam` DECIMAL(10,2) NOT NULL DEFAULT 50000.00,
  `Lokasi` VARCHAR(255) DEFAULT NULL,
  `Kota` VARCHAR(100) DEFAULT NULL,
  `Status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
  `Rating` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `Total_Order` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`User_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table Pesanan
CREATE TABLE IF NOT EXISTS `Pesanan` (
  `Id` INT AUTO_INCREMENT PRIMARY KEY,
  `User_id` INT NOT NULL,
  `Companion_id` INT NOT NULL,
  `Tanggal_jemput` DATE NOT NULL,
  `Jam_mulai` TIME NOT NULL,
  `Durasi_jam` INT NOT NULL DEFAULT 1,
  `Lokasi_jemput` VARCHAR(255) NOT NULL,
  `Tujuan` VARCHAR(255) DEFAULT NULL,
  `Keperluan` TEXT DEFAULT NULL,
  `Total_harga` DECIMAL(10,2) NOT NULL,
  `Status` ENUM('menunggu_pembayaran', 'menunggu_verifikasi_admin', 'companion_sedang_dihubungi', 'diterima_companion', 'berjalan', 'selesai', 'dibatalkan', 'menunggu', 'diterima') NOT NULL DEFAULT 'menunggu_pembayaran',
  `Created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`User_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`Companion_id`) REFERENCES `Companions` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.5. Table Pembayaran
CREATE TABLE IF NOT EXISTS `Pembayaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pesanan_id` INT NOT NULL,
  `bukti_transfer` VARCHAR(255) NOT NULL,
  `status` ENUM('Menunggu Verifikasi', 'Terverifikasi', 'Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pesanan_id`) REFERENCES `Pesanan` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table Reviews
CREATE TABLE IF NOT EXISTS `Reviews` (
  `Id` INT AUTO_INCREMENT PRIMARY KEY,
  `Pesanan_id` INT NOT NULL,
  `User_id` INT NOT NULL,
  `Companion_id` INT NOT NULL,
  `Rating` INT NOT NULL DEFAULT 5,
  `Komentar` TEXT DEFAULT NULL,
  `Created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`Pesanan_id`) REFERENCES `Pesanan` (`Id`) ON DELETE CASCADE,
  FOREIGN KEY (`User_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`Companion_id`) REFERENCES `Companions` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- Insert Sample Data
-- =========================================================================

-- Hashed password for 'password123': $2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `role`, `foto`, `created_at`) VALUES
(1, 'Budi Santoso', 'budi@example.com', '$2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe', '081234567890', 'user', NULL, CURRENT_TIMESTAMP),
(2, 'Sari Dewi', 'sari@example.com', '$2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe', '089876543210', 'companion', NULL, CURRENT_TIMESTAMP),
(3, 'Agus Setiawan', 'agus@example.com', '$2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe', '085211223344', 'companion', NULL, CURRENT_TIMESTAMP),
(4, 'Admin TemanCOD', 'admin@example.com', '$2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe', '081122334455', 'admin', NULL, CURRENT_TIMESTAMP);

INSERT INTO `Companions` (`Id`, `User_id`, `Deskripsi`, `Harga_per_jam`, `Lokasi`, `Kota`, `Status`, `Rating`, `Total_Order`) VALUES
(1, 2, 'Halo! Saya Sari, siap menemani Anda melakukan transaksi COD dengan aman. Berpengalaman dalam pengecekan gadget dan laptop.', 50000.00, 'Bandung Kota', 'Bandung', 'aktif', 4.80, 12),
(2, 3, 'Siap menemani COD wilayah Jakarta. Fokus pada keamanan perjalanan pergi-pulang dan pengecekan barang.', 50000.00, 'Jakarta Selatan', 'Jakarta', 'aktif', 5.00, 8);

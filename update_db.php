<?php
require_once 'includes/config.php';

echo "<h2>Menjalankan Update Database...</h2>";

$queries = [
    "ALTER TABLE `Pesanan` MODIFY COLUMN `Status` ENUM('menunggu_pembayaran', 'menunggu_verifikasi_admin', 'companion_sedang_dihubungi', 'diterima_companion', 'berjalan', 'selesai', 'dibatalkan', 'menunggu', 'diterima') NOT NULL DEFAULT 'menunggu_pembayaran'",
    
    "CREATE TABLE IF NOT EXISTS `Pembayaran` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `pesanan_id` INT NOT NULL,
      `bukti_transfer` VARCHAR(255) NOT NULL,
      `status` ENUM('Menunggu Verifikasi', 'Terverifikasi', 'Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`pesanan_id`) REFERENCES `Pesanan` (`Id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "INSERT IGNORE INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `role`) VALUES (4, 'Admin TemanCOD', 'admin@example.com', '$2y$10$JbmHhh/jqXDUl2VBBEDjmu5eQhesKG.iBkHeYlFHTrVuOtoXXeIQe', '081122334455', 'admin')"
];

foreach ($queries as $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color:green'>SUCCESS: " . htmlspecialchars($sql) . "</p>";
    } else {
        echo "<p style='color:red'>ERROR: " . $conn->error . " <br> SQL: " . htmlspecialchars($sql) . "</p>";
    }
}

echo "<p>Selesai! Silakan hapus file ini jika sudah tidak digunakan.</p>";
echo "<a href='index.php'>Kembali ke Home</a>";
?>

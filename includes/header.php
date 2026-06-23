<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
<nav class="navbar">

    <a href="<?= BASE_URL ?>" class="nav-logo">
        <span class="logo-icon">🤝</span>
        <span class="logo-text">Teman<strong>COD</strong></span>
    </a>

    <ul class="nav-links">
        <li><a href="<?= BASE_URL ?>">Beranda</a></li>
        <li><a href="<?= BASE_URL ?>pages/tentang.php">Tentang Kami</a></li>
        <li><a href="<?= BASE_URL ?>pages/layanan.php">Layanan</a></li>
        <li><a href="<?= BASE_URL ?>pages/companions.php">Booking</a></li>
        <li><a href="<?= BASE_URL ?>pages/tracking.php">Tracking</a></li>
        <li><a href="<?= BASE_URL ?>pages/harga.php">Harga</a></li>
    </ul>

    <div class="nav-actions">

        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>

        <?php if (isLoggedIn()): ?>

            <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>pages/admin_dashboard.php" class="btn-outline-sm">
                    👑 Admin
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>pages/dashboard.php" class="btn-outline-sm">
                    👤 <?= $_SESSION['nama'] ?>
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>pages/logout.php" class="btn-danger-sm">
                Keluar
            </a>

        <?php else: ?>

            <?php if ($currentPage != 'login.php'): ?>
                <a href="<?= BASE_URL ?>pages/login.php" class="btn-outline-sm">
                    Masuk
                </a>
            <?php endif; ?>

            <?php if ($currentPage != 'register.php'): ?>
                <a href="<?= BASE_URL ?>pages/register.php" class="btn-primary-sm">
                    Daftar
                </a>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <button class="hamburger" id="hamburger">&#9776;</button>

</nav>
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= BASE_URL ?>">🏠 Beranda</a>
    <a href="<?= BASE_URL ?>pages/tentang.php">👥 Tentang Kami</a>
    <a href="<?= BASE_URL ?>pages/layanan.php">⚡ Layanan</a>
    <a href="<?= BASE_URL ?>pages/companions.php">📅 Booking</a>
    <a href="<?= BASE_URL ?>pages/tracking.php">📍 Tracking</a>
    <a href="<?= BASE_URL ?>pages/harga.php">💰 Harga</a>
    <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
            <a href="<?= BASE_URL ?>pages/admin_dashboard.php">Dashboard</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>pages/dashboard.php">Dashboard</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>pages/logout.php">Keluar</a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>pages/login.php">Masuk</a>
        <a href="<?= BASE_URL ?>pages/register.php">Daftar</a>
    <?php endif; ?>
</div>
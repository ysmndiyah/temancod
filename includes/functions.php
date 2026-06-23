<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isCompanion() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'companion';
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format date as "23 Juni 2026"
function formatIndonesianDate($dateStr) {
    $timestamp = strtotime($dateStr);
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $day = date('d', $timestamp);
    $month = $months[intval(date('n', $timestamp))];
    $year = date('Y', $timestamp);
    return ltrim($day, '0') . ' ' . $month . ' ' . $year;
}

// Map status to badge CSS class
function getBadgeClass($status) {
    $map = [
        'menunggu_pembayaran' => 'badge-yellow',
        'menunggu_verifikasi_admin' => 'badge-orange',
        'companion_sedang_dihubungi' => 'badge-blue',
        'diterima_companion' => 'badge-green',
        'berjalan' => 'badge-purple',
        'selesai' => 'badge-darkgreen',
        'dibatalkan' => 'badge-red',
        // fallback
    ];
    return $map[$status] ?? 'badge';
}
function getStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}
function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return $diff . ' detik lalu';
    if ($diff < 3600) return round($diff/60) . ' menit lalu';
    if ($diff < 86400) return round($diff/3600) . ' jam lalu';
    return round($diff/86400) . ' hari lalu';
}
?>
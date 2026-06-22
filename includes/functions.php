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
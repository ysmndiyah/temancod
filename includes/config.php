<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'teman cod');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
session_start();
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if (strpos($base_dir, '/pages') !== false) {
    $base_path = str_replace('/pages', '', $base_dir);
} else {
    $base_path = $base_dir;
}
$base_path = rtrim($base_path, '/') . '/';

define('BASE_URL', $protocol . $host . $base_path);
define('SITE_NAME', 'TemanCOD');
?>
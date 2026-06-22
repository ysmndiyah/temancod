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
define('BASE_URL', 'https://uneaten-garnet-implement.ngrok-free.dev/temanCOD/');
define('SITE_NAME', 'TemanCOD');
?>
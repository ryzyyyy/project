<?php
// Konfigurasi koneksi database
$host = 'localhost'; // Ganti jika port berbeda, misal 'localhost:3307'
$user = 'root';
$pass = '';
$db   = 'sistem_surat';

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($koneksi, 'utf8');

// Session start jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cekLogin() {
    // Auto logout jika tidak aktif selama 30 menit (1800 detik)
    $timeout = 1800; // 30 menit dalam detik
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/modules/auth/login.php");
        exit();
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Hapus session dan redirect ke logout
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/logout.php?timeout=1");
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function cekRole($role) {
    if ($_SESSION['role'] !== $role && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user') {
        header("Location: " . BASE_URL . "/modules/dashboard/index.php");
        exit();
    }
}

define('BASE_URL', 'http://localhost/sistem_surat');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

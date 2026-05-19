<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/modules/auth/login.php");
    exit();
}

$username = trim(mysqli_real_escape_string($koneksi, $_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header("Location: " . BASE_URL . "/modules/auth/login.php");
    exit();
}

$sql = "SELECT * FROM users WHERE username = '$username' AND is_active = 1 LIMIT 1";
$result = mysqli_query($koneksi, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['login_error'] = 'Username tidak ditemukan atau akun tidak aktif.';
    header("Location: " . BASE_URL . "/modules/auth/login.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Verifikasi password (mendukung hash dan plain text untuk kemudahan setup awal)
$valid = password_verify($password, $user['password']);
if (!$valid && $password === $user['password']) {
    // Jika plain text, hash dan update
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($koneksi, "UPDATE users SET password = '$hashed' WHERE id = {$user['id']}");
    $valid = true;
}

if (!$valid) {
    $_SESSION['login_error'] = 'Password salah. Silakan coba lagi.';
    header("Location: " . BASE_URL . "/modules/auth/login.php");
    exit();
}

// Set session
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['nama']     = $user['nama'];
$_SESSION['role']     = $user['role'];
$_SESSION['bidang_id'] = $user['bidang_id'] ?? null;

// Set flash message untuk popup selamat datang
$_SESSION['welcome_popup'] = 'Selamat datang kembali, ' . htmlspecialchars($user['nama']) . '!';

// Update last login
mysqli_query($koneksi, "UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");

header("Location: " . BASE_URL . "/modules/dashboard/index.php");
exit();

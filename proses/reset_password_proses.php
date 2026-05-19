<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: ../modules/auth/lupa_password.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/auth/reset_password.php');
    exit();
}

$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password2'] ?? '';

if (empty($pass1) || empty($pass2)) {
    $_SESSION['reset_error'] = 'Password baru dan verifikasi wajib diisi.';
    header('Location: ../modules/auth/reset_password.php');
    exit();
}
if ($pass1 !== $pass2) {
    $_SESSION['reset_error'] = 'Password baru dan verifikasi tidak sama.';
    header('Location: ../modules/auth/reset_password.php');
    exit();
}
if (strlen($pass1) < 6) {
    $_SESSION['reset_error'] = 'Password minimal 6 karakter.';
    header('Location: ../modules/auth/reset_password.php');
    exit();
}

$hashed = password_hash($pass1, PASSWORD_DEFAULT);
$userId = (int)$_SESSION['reset_user_id'];

mysqli_query($koneksi, "UPDATE users SET password = '$hashed' WHERE id = $userId");
unset($_SESSION['reset_user_id']);
$_SESSION['login_error'] = 'Password berhasil direset. Silakan login dengan password baru.';
header('Location: ../modules/auth/login.php');
exit();

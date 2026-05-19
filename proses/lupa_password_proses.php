<?php
// Proses lupa password: cek user/email, generate password baru, update DB, dan tampilkan pesan
require_once '../config/koneksi.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/auth/lupa_password.php');
    exit();
}

$user = trim($_POST['user'] ?? '');
if (empty($user)) {
    $_SESSION['reset_error'] = 'Username atau email wajib diisi.';
    header('Location: ../modules/auth/lupa_password.php');
    exit();
}

// Cek user berdasarkan username atau email
$sql = "SELECT * FROM users WHERE username = '$user' OR email = '$user' LIMIT 1";
$result = mysqli_query($koneksi, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['reset_error'] = 'Akun tidak ditemukan.';
    header('Location: ../modules/auth/lupa_password.php');
    exit();
}

$userData = mysqli_fetch_assoc($result);
// Simpan user id ke session untuk proses reset password
$_SESSION['reset_user_id'] = $userData['id'];
header('Location: ../modules/auth/reset_password.php');
exit();

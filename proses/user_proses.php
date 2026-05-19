<?php
require_once '../config/koneksi.php';
cekLogin();
cekRole('admin');

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/master/user.php';


if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $username  = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $email     = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
    $jabatan   = mysqli_real_escape_string($koneksi, trim($_POST['jabatan'] ?? ''));
    $role      = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'user');
    $isActive  = (int)($_POST['is_active'] ?? 1);
    $bidang_id = (int)($_POST['bidang_id'] ?? 0);

    // Cek username unik
    $cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT id FROM users WHERE username='$username'"));
    if ($cek) {
        $_SESSION['error'] = 'Username sudah digunakan.';
        header("Location: $redirect"); exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO users (nama, username, password, email, jabatan, role, is_active, bidang_id, created_at)
            VALUES ('$nama', '$username', '$hashed', '$email', '$jabatan', '$role', $isActive, $bidang_id, NOW())";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Pengguna berhasil ditambahkan.'
        : $_SESSION['error'] = 'Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = (int)$_POST['id'];
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
    $jabatan  = mysqli_real_escape_string($koneksi, trim($_POST['jabatan'] ?? ''));
    $role     = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'user');
    $isActive = (int)($_POST['is_active'] ?? 1);
    $bidang_id = (int)($_POST['bidang_id'] ?? 0);
    $password = $_POST['password'] ?? '';

    // Cek username unik (kecuali diri sendiri)
    $cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT id FROM users WHERE username='$username' AND id != $id"));
    if ($cek) {
        $_SESSION['error'] = 'Username sudah digunakan.';
        header("Location: $redirect"); exit();
    }

    $pwUpdate = '';
    if (!empty($password) && strlen($password) >= 6) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $pwUpdate = ", password = '$hashed'";
    }


        $sql = "UPDATE users SET nama='$nama', username='$username', email='$email', jabatan='$jabatan',
            role='$role', is_active=$isActive, bidang_id=$bidang_id$pwUpdate WHERE id=$id";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Pengguna berhasil diperbarui.'
        : $_SESSION['error'] = 'Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = 'Tidak bisa menghapus akun sendiri.';
        header("Location: $redirect"); exit();
    }
    mysqli_query($koneksi, "DELETE FROM users WHERE id = $id")
        ? $_SESSION['notif'] = 'Pengguna berhasil dihapus.'
        : $_SESSION['error'] = 'Gagal menghapus.';
}

header("Location: $redirect");
exit();

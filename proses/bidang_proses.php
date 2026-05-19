<?php
require_once '../config/koneksi.php';
cekLogin();
cekRole('admin');

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/master/bidang.php';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = mysqli_real_escape_string($koneksi, trim($_POST['kode'] ?? ''));
    $nama    = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $kepala  = mysqli_real_escape_string($koneksi, trim($_POST['kepala_bidang'] ?? ''));
    $ket     = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));

    $sql = "INSERT INTO bidang (kode, nama, kepala_bidang, keterangan, created_at)
            VALUES ('$kode', '$nama', '$kepala', '$ket', NOW())";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Bidang berhasil ditambahkan.'
        : $_SESSION['error'] = 'Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)$_POST['id'];
    $kode   = mysqli_real_escape_string($koneksi, trim($_POST['kode'] ?? ''));
    $nama   = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $kepala = mysqli_real_escape_string($koneksi, trim($_POST['kepala_bidang'] ?? ''));
    $ket    = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));

    $sql = "UPDATE bidang SET kode='$kode', nama='$nama', kepala_bidang='$kepala', keterangan='$ket' WHERE id = $id";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Bidang berhasil diperbarui.'
        : $_SESSION['error'] = 'Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    // Cek apakah masih ada user di bidang ini
    $cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE bidang_id = $id"));
    if ($cek[0] > 0) {
        $_SESSION['error'] = 'Bidang tidak bisa dihapus karena masih memiliki pengguna.';
        header("Location: $redirect"); exit();
    }
    mysqli_query($koneksi, "DELETE FROM bidang WHERE id = $id")
        ? $_SESSION['notif'] = 'Bidang berhasil dihapus.'
        : $_SESSION['error'] = 'Gagal menghapus.';
}

header("Location: $redirect");
exit();

<?php
require_once '../config/koneksi.php';
cekLogin();

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/surat_keluar/index.php';

function uploadFileSK($fieldName) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) return null;
    $file    = $_FILES[$fieldName];
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed))          return ['error' => 'Format file tidak diizinkan.'];
    if ($file['size'] > 10 * 1024 * 1024)   return ['error' => 'Ukuran file maksimal 10MB.'];
    $newName = 'SK_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $newName)) return ['error' => 'Gagal upload.'];
    return $newName;
}

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor       = mysqli_real_escape_string($koneksi, trim($_POST['nomor_surat'] ?? ''));
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $tujuan      = mysqli_real_escape_string($koneksi, trim($_POST['tujuan'] ?? ''));
    $pengolah    = mysqli_real_escape_string($koneksi, trim($_POST['pengolah'] ?? ''));
    $perihal     = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    $isi         = mysqli_real_escape_string($koneksi, trim($_POST['isi_surat'] ?? ''));
    $klasifikasi = mysqli_real_escape_string($koneksi, $_POST['klasifikasi'] ?? 'biasa');
    $userId      = (int)$_SESSION['user_id'];

    $upload = uploadFileSK('file_surat');
    if (is_array($upload) && isset($upload['error'])) {
        $_SESSION['notif'] = '❌ ' . $upload['error'];
        header("Location: $redirect"); exit();
    }
    $fileSurat = $upload ? "'$upload'" : 'NULL';

        $sql = "INSERT INTO surat_keluar (nomor_surat, tanggal_surat, tujuan, pengolah, perihal, isi_surat, klasifikasi, file_surat, dibuat_oleh, created_at)
            VALUES ('$nomor', '$tanggal', '$tujuan', '$pengolah', '$perihal', '$isi', '$klasifikasi', $fileSurat, $userId, NOW())";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Surat keluar berhasil ditambahkan.'
        : $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)$_POST['id'];
    $nomor       = mysqli_real_escape_string($koneksi, trim($_POST['nomor_surat'] ?? ''));
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $tujuan      = mysqli_real_escape_string($koneksi, trim($_POST['tujuan'] ?? ''));
    $pengolah    = mysqli_real_escape_string($koneksi, trim($_POST['pengolah'] ?? ''));
    $perihal     = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    $isi         = mysqli_real_escape_string($koneksi, trim($_POST['isi_surat'] ?? ''));
    $klasifikasi = mysqli_real_escape_string($koneksi, $_POST['klasifikasi'] ?? 'biasa');

    $fileUpdate = '';
    $upload = uploadFileSK('file_surat');
    if (is_array($upload) && isset($upload['error'])) {
        $_SESSION['notif'] = '❌ ' . $upload['error'];
        header("Location: $redirect"); exit();
    }
    if ($upload) {
        $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT file_surat FROM surat_keluar WHERE id = $id"));
        if ($old && $old['file_surat'] && file_exists(UPLOAD_PATH . $old['file_surat'])) unlink(UPLOAD_PATH . $old['file_surat']);
        $fileUpdate = ", file_surat = '$upload'";
    }

        $sql = "UPDATE surat_keluar SET nomor_surat='$nomor', tanggal_surat='$tanggal', tujuan='$tujuan', pengolah='$pengolah',
            perihal='$perihal', isi_surat='$isi', klasifikasi='$klasifikasi'$fileUpdate WHERE id = $id";

    mysqli_query($koneksi, $sql)
        ? $_SESSION['notif'] = 'Surat keluar berhasil diperbarui.'
        : $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT file_surat FROM surat_keluar WHERE id = $id"));
    if ($old && $old['file_surat'] && file_exists(UPLOAD_PATH . $old['file_surat'])) unlink(UPLOAD_PATH . $old['file_surat']);
    mysqli_query($koneksi, "DELETE FROM surat_keluar WHERE id = $id")
        ? $_SESSION['notif'] = 'Surat keluar berhasil dihapus.'
        : $_SESSION['notif'] = '❌ Gagal menghapus.';
}

header("Location: $redirect");
exit();

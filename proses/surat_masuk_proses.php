<?php
require_once '../config/koneksi.php';
cekLogin();

$aksi = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/surat_masuk/index.php';

function uploadFile($fieldName) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) return null;

    $file     = $_FILES[$fieldName];
    $allowed  = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $maxSize  = 10 * 1024 * 1024; // 10MB

    if (!in_array($ext, $allowed))   return ['error' => 'Format file tidak diizinkan (PDF/JPG/PNG).'];
    if ($file['size'] > $maxSize)    return ['error' => 'Ukuran file maksimal 10MB.'];

    $newName = 'SM_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
    $dest    = UPLOAD_PATH . $newName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return ['error' => 'Gagal mengupload file.'];

    return $newName;
}

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor       = mysqli_real_escape_string($koneksi, trim($_POST['nomor_surat'] ?? ''));
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $pengirim    = mysqli_real_escape_string($koneksi, trim($_POST['pengirim'] ?? ''));
    $perihal     = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    $lampiran    = mysqli_real_escape_string($koneksi, trim($_POST['lampiran'] ?? ''));
    $pengolah    = mysqli_real_escape_string($koneksi, trim($_POST['pengolah'] ?? ''));
    $catatan     = mysqli_real_escape_string($koneksi, trim($_POST['catatan'] ?? ''));
    $userId      = (int)$_SESSION['user_id'];

    $upload = uploadFile('file_surat');
    if (is_array($upload) && isset($upload['error'])) {
        $_SESSION['notif'] = '❌ ' . $upload['error'];
        header("Location: $redirect"); exit();
    }
    $fileSurat = $upload ? "'$upload'" : 'NULL';

        $sql = "INSERT INTO surat_masuk (nomor_surat, tanggal_surat, pengirim, perihal, lampiran, pengolah, catatan, file_surat, penerima_id, created_at)
            VALUES ('$nomor', '$tanggal', '$pengirim', '$perihal', '$lampiran', '$pengolah', '$catatan', $fileSurat, $userId, NOW())";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['notif'] = 'Surat masuk berhasil ditambahkan.';
    } else {
        $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);
    }

} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)$_POST['id'];
    $nomor       = mysqli_real_escape_string($koneksi, trim($_POST['nomor_surat'] ?? ''));
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $pengirim    = mysqli_real_escape_string($koneksi, trim($_POST['pengirim'] ?? ''));
    $perihal     = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    // $disposisi dihapus karena kolom tidak ada
    $catatan     = mysqli_real_escape_string($koneksi, trim($_POST['catatan'] ?? ''));

    $fileUpdate = '';
    $upload = uploadFile('file_surat');
    if (is_array($upload) && isset($upload['error'])) {
        $_SESSION['notif'] = '❌ ' . $upload['error'];
        header("Location: $redirect"); exit();
    }
    if ($upload) {
        // Hapus file lama
        $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT file_surat FROM surat_masuk WHERE id = $id"));
        if ($old && $old['file_surat'] && file_exists(UPLOAD_PATH . $old['file_surat'])) {
            unlink(UPLOAD_PATH . $old['file_surat']);
        }
        $fileUpdate = ", file_surat = '$upload'";
    }

        $sql = "UPDATE surat_masuk SET nomor_surat='$nomor', tanggal_surat='$tanggal', pengirim='$pengirim',
            perihal='$perihal', catatan='$catatan'$fileUpdate
            WHERE id = $id";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['notif'] = 'Surat masuk berhasil diperbarui.';
    } else {
        $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);
    }

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    // Hapus file fisik
    $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT file_surat FROM surat_masuk WHERE id = $id"));
    if ($old && $old['file_surat'] && file_exists(UPLOAD_PATH . $old['file_surat'])) {
        unlink(UPLOAD_PATH . $old['file_surat']);
    }
    if (mysqli_query($koneksi, "DELETE FROM surat_masuk WHERE id = $id")) {
        $_SESSION['notif'] = 'Surat masuk berhasil dihapus.';
    } else {
        $_SESSION['notif'] = '❌ Gagal menghapus.';
    }
}

header("Location: $redirect");
exit();

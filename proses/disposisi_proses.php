<?php
require_once '../config/koneksi.php';
cekLogin();

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/disposisi/index.php';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $suratMasukId   = (int)$_POST['surat_masuk_id'];
    $kepada         = mysqli_real_escape_string($koneksi, $_POST['kepada']);
    $index          = mysqli_real_escape_string($koneksi, $_POST['index'] ?? '');
    $kode           = mysqli_real_escape_string($koneksi, $_POST['kode'] ?? '');
    // nomor_urut akan diisi setelah insert dari id
        // isi_ringkas diisi dari perihal surat_masuk
        $isiRingkas = '';
        $getPerihal = mysqli_query($koneksi, "SELECT perihal FROM surat_masuk WHERE id = $suratMasukId LIMIT 1");
        if ($row = mysqli_fetch_assoc($getPerihal)) {
            $isiRingkas = mysqli_real_escape_string($koneksi, $row['perihal']);
        }
    $dariRaw        = $_POST['dari'] ?? '';
    $dari           = is_numeric($dariRaw) ? (int)$dariRaw : 'NULL';
    $tanggalSurat   = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $nomorSurat     = mysqli_real_escape_string($koneksi, $_POST['nomor_surat'] ?? '');
    $lampiran       = mysqli_real_escape_string($koneksi, $_POST['lampiran'] ?? '');
    $pengolah       = mysqli_real_escape_string($koneksi, $_POST['pengolah'] ?? '');
    $isi            = mysqli_real_escape_string($koneksi, trim($_POST['isi_disposisi'] ?? ''));
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal_disposisi'] ?? '');
    $batas          = mysqli_real_escape_string($koneksi, $_POST['batas_waktu'] ?? '');
    $catatan        = mysqli_real_escape_string($koneksi, trim($_POST['catatan'] ?? ''));

    $batasVal = $batas ? "'$batas'" : 'NULL';

        $sql = "INSERT INTO disposisi (surat_masuk_id, kepada, dari, isi_disposisi, tanggal_disposisi, batas_waktu, catatan, created_at)
            VALUES ($suratMasukId, '$kepada', $dari, '$isi', '$tanggal', $batasVal, '$catatan', NOW())";

        if (mysqli_query($koneksi, $sql)) {
            // $insertId = mysqli_insert_id($koneksi);
            // Field nomor_urut tidak ada di tabel disposisi, baris ini dihapus
            $_SESSION['notif'] = 'Disposisi berhasil dibuat.';
        } else {
            $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);
        }
} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)$_POST['id'];
    $kepada       = mysqli_real_escape_string($koneksi, $_POST['kepada']);
    $index        = mysqli_real_escape_string($koneksi, $_POST['index'] ?? '');
    $kode         = mysqli_real_escape_string($koneksi, $_POST['kode'] ?? '');
    $nomorUrut    = mysqli_real_escape_string($koneksi, $_POST['nomor_urut'] ?? '');
        // isi_ringkas diisi dari perihal surat_masuk
        $isiRingkas = '';
        $getPerihal = mysqli_query($koneksi, "SELECT perihal FROM surat_masuk WHERE id = $suratMasukId LIMIT 1");
        if ($row = mysqli_fetch_assoc($getPerihal)) {
            $isiRingkas = mysqli_real_escape_string($koneksi, $row['perihal']);
        }
    $dari         = mysqli_real_escape_string($koneksi, $_POST['dari'] ?? '');
    $tanggalSurat = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $nomorSurat   = mysqli_real_escape_string($koneksi, $_POST['nomor_surat'] ?? '');
    $lampiran     = mysqli_real_escape_string($koneksi, $_POST['lampiran'] ?? '');
    $pengolah     = mysqli_real_escape_string($koneksi, $_POST['pengolah'] ?? '');
    $isi          = mysqli_real_escape_string($koneksi, trim($_POST['isi_disposisi'] ?? ''));
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal_disposisi'] ?? '');
    $batas        = mysqli_real_escape_string($koneksi, $_POST['batas_waktu'] ?? '');
    $catatan      = mysqli_real_escape_string($koneksi, trim($_POST['catatan'] ?? ''));

    $batasVal = $batas ? "'$batas'" : 'NULL';

    $sql = "UPDATE disposisi SET kepada='$kepada', `index`='$index', kode='$kode', nomor_urut='$nomorUrut', isi_ringkas='$isiRingkas', dari='$dari', tanggal_surat='$tanggalSurat', nomor_surat='$nomorSurat', lampiran='$lampiran', pengolah='$pengolah', isi_disposisi='$isi', tanggal_disposisi='$tanggal', batas_waktu=$batasVal, catatan='$catatan' WHERE id = $id";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['notif'] = 'Disposisi berhasil diperbarui.';
    } else {
        $_SESSION['notif'] = '❌ Gagal: ' . mysqli_error($koneksi);
    }

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    mysqli_query($koneksi, "DELETE FROM disposisi WHERE id = $id")
        ? $_SESSION['notif'] = 'Disposisi berhasil dihapus.'
        : $_SESSION['notif'] = '❌ Gagal menghapus.';
}

header("Location: $redirect");
exit();

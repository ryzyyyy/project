<?php
require_once '../config/koneksi.php';
cekLogin();

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/arsip/index.php';

if ($aksi === 'kode_arsip' && isset($_GET['jenis'])) {
    header('Content-Type: text/plain');
    $jenis = mysqli_real_escape_string($koneksi, $_GET['jenis']);
    $tahun = date('Y');
    $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM arsip WHERE jenis_surat='$jenis' AND YEAR(created_at)='$tahun'");
    $d = mysqli_fetch_assoc($q);
    $urut = (int)$d['jml'] + 1;
    echo $urut;
    exit();
}

$aksi     = $_REQUEST['aksi'] ?? '';
$redirect = '../modules/arsip/index.php';

function uploadFileArsip($f) {
    if (!isset($_FILES[$f]) || $_FILES[$f]['error'] !== UPLOAD_ERR_OK) return null;
    $file = $_FILES[$f];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf','jpg','jpeg','png'])) return ['error' => 'Format tidak diizinkan.'];
    if ($file['size'] > 5*1024*1024) return ['error' => 'Maks 5MB.'];
    $n = 'ARS_'.date('YmdHis').'_'.uniqid().'.'.$ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH.$n)) return ['error' => 'Gagal upload.'];
    return $n;
}

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {


    $jenis   = mysqli_real_escape_string($koneksi, $_POST['jenis_surat'] ?? 'masuk');
    $tahun   = date('Y');
    $nomor   = mysqli_real_escape_string($koneksi, trim($_POST['nomor_surat'] ?? ''));
    // Cek apakah surat sudah diarsipkan
    $cek = mysqli_query($koneksi, "SELECT id FROM arsip WHERE nomor_surat='$nomor' AND jenis_surat='$jenis'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['notif'] = '❌ Surat ini sudah diarsipkan, tidak bisa diarsipkan lagi.';
        header("Location:$redirect");
        exit();
    }
    // Generate kode arsip jika kosong
    if (empty($_POST['kode_arsip'])) {
        $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM arsip WHERE jenis_surat='$jenis' AND YEAR(created_at)='$tahun'");
        $d = mysqli_fetch_assoc($q);
        $urut = (int)$d['jml'] + 1;
        $kode = 'ARS/' . str_pad($urut,3,'0',STR_PAD_LEFT) . '/' . $tahun . '/' . strtoupper($jenis);
    } else {
        $kode = mysqli_real_escape_string($koneksi, trim($_POST['kode_arsip']));
    }
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat'] ?? '');
    $perihal = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    $asal    = mysqli_real_escape_string($koneksi, trim($_POST['asal_instansi'] ?? ''));
    $lokasi  = mysqli_real_escape_string($koneksi, trim($_POST['lokasi_fisik'] ?? ''));
    $ket     = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));

    $up = uploadFileArsip('file_arsip');
    if (is_array($up) && isset($up['error'])) { $_SESSION['notif'] = '❌ '.$up['error']; header("Location:$redirect"); exit(); }
    $file = $up ? "'$up'" : 'NULL';

    $sql = "INSERT INTO arsip (kode_arsip, jenis_surat, nomor_surat, tanggal_surat, perihal, asal_instansi, lokasi_fisik, keterangan, file_arsip, created_at)
            VALUES ('$kode','$jenis','$nomor','$tanggal','$perihal','$asal','$lokasi','$ket',$file,NOW())";
    mysqli_query($koneksi,$sql) ? $_SESSION['notif']='Arsip berhasil ditambahkan.' : $_SESSION['notif']='❌ '.mysqli_error($koneksi);

} elseif ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)$_POST['id'];
    $kode    = mysqli_real_escape_string($koneksi, trim($_POST['kode_arsip'] ?? ''));
    $jenis   = mysqli_real_escape_string($koneksi, $_POST['jenis_surat'] ?? 'masuk');
    $perihal = mysqli_real_escape_string($koneksi, trim($_POST['perihal'] ?? ''));
    $lokasi  = mysqli_real_escape_string($koneksi, trim($_POST['lokasi_fisik'] ?? ''));
    $ket     = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));

    $fu = '';
    $up = uploadFileArsip('file_arsip');
    if (is_array($up) && isset($up['error'])) { $_SESSION['notif'] = '❌ '.$up['error']; header("Location:$redirect"); exit(); }
    if ($up) {
        $old = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT file_arsip FROM arsip WHERE id=$id"));
        if ($old && $old['file_arsip'] && file_exists(UPLOAD_PATH.$old['file_arsip'])) unlink(UPLOAD_PATH.$old['file_arsip']);
        $fu = ", file_arsip='$up'";
    }
    $sql = "UPDATE arsip SET kode_arsip='$kode',jenis_surat='$jenis',perihal='$perihal',lokasi_fisik='$lokasi',keterangan='$ket'$fu WHERE id=$id";
    mysqli_query($koneksi,$sql) ? $_SESSION['notif']='Arsip berhasil diperbarui.' : $_SESSION['notif']='❌ '.mysqli_error($koneksi);

} elseif ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    $old = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT file_arsip FROM arsip WHERE id=$id"));
    if ($old && $old['file_arsip'] && file_exists(UPLOAD_PATH.$old['file_arsip'])) unlink(UPLOAD_PATH.$old['file_arsip']);
    mysqli_query($koneksi,"DELETE FROM arsip WHERE id=$id")
        ? $_SESSION['notif']='Arsip berhasil dihapus.'
        : $_SESSION['notif']='❌ Gagal menghapus.';
}

header("Location: $redirect");
exit();
?>
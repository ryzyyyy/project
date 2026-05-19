<?php
require_once '../config/koneksi.php';
header('Content-Type: application/json');
$jenis = isset($_GET['jenis']) ? mysqli_real_escape_string($koneksi, $_GET['jenis']) : '';
if ($jenis === 'masuk') {
    $result = mysqli_query($koneksi, "SELECT id, nomor_surat, perihal, tanggal_surat, pengirim FROM surat_masuk ORDER BY created_at DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}
if ($jenis === 'keluar') {
    $result = mysqli_query($koneksi, "SELECT id, nomor_surat, perihal, tanggal_surat, tujuan FROM surat_keluar ORDER BY created_at DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}
echo json_encode([]);
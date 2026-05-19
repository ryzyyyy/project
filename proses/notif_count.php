<?php
// Ambil jumlah surat masuk/keluar yang belum dilihat oleh user
require_once '../config/koneksi.php';

$userId = $_SESSION['user_id'] ?? 0;

// Surat Masuk: hanya yang penerima_id = user ini dan dilihat IS NULL
$masuk = mysqli_query($koneksi, "SELECT id FROM surat_masuk WHERE penerima_id = $userId AND (dilihat IS NULL OR dilihat = '') ORDER BY created_at DESC LIMIT 1");
$masukCount = mysqli_num_rows($masuk);
$masukId = $masukCount ? mysqli_fetch_assoc($masuk)['id'] : 0;

// Surat Keluar: hanya yang dibuat_oleh = user ini dan belum pernah dibuka (misal, tambahkan kolom dilihat di surat_keluar jika ingin tracking)
$keluar = mysqli_query($koneksi, "SELECT id FROM surat_keluar WHERE dibuat_oleh = $userId AND (dilihat IS NULL OR dilihat = '') ORDER BY created_at DESC LIMIT 1");
$keluarCount = mysqli_num_rows($keluar);
$keluarId = $keluarCount ? mysqli_fetch_assoc($keluar)['id'] : 0;

header('Content-Type: application/json');
echo json_encode([
    'masuk' => $masukCount,
    'masuk_id' => $masukId,
    'keluar' => $keluarCount,
    'keluar_id' => $keluarId
]);

<?php
require_once '../../config/koneksi.php';
cekLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $_SESSION['error'] = "ID surat tidak valid.";
    header("Location: surat_keluar.php");
    exit;
}

// Update kolom 'dilihat' jika ada
$hasDilihat = false;
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM surat_keluar LIKE 'dilihat'");
if ($result && mysqli_num_rows($result) > 0) {
    $hasDilihat = true;
}

if ($hasDilihat) {
    $cek = mysqli_query($koneksi, "SELECT dilihat FROM surat_keluar WHERE id = $id LIMIT 1");
    $cekRow = mysqli_fetch_assoc($cek);
    if ($cekRow && empty($cekRow['dilihat'])) {
        mysqli_query($koneksi, "UPDATE surat_keluar SET dilihat = NOW() WHERE id = $id");
    }
}

// Ambil data surat keluar
$data = mysqli_query($koneksi, "SELECT sk.*, u.nama as pembuat_nama 
    FROM surat_keluar sk 
    LEFT JOIN users u ON sk.dibuat_oleh = u.id 
    WHERE sk.id = $id LIMIT 1");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    $_SESSION['error'] = "Surat tidak ditemukan.";
    header("Location: surat_keluar.php");
    exit;
}

// Ambil riwayat disposisi (jika ada tabel disposisi untuk surat keluar)
$riwayat = false; // Tidak ada riwayat disposisi untuk surat keluar

$pageTitle = 'Detail Surat Keluar';
$activeMenu = 'surat-keluar';

include '../../assets/layout_header.php';
?>

<div class="space-y-6 p-6">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="dashboard.php" class="hover:text-gray-700">Dashboard</a>
        <span>›</span>
        <a href="surat_keluar.php" class="hover:text-gray-700">Surat Keluar</a>
        <span>›</span>
        <span class="text-gray-800 font-medium">Detail Surat #<?= htmlspecialchars($row['nomor_surat']) ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card Utama Detail Surat Keluar (Custom Field) -->
            <div class="bg-white rounded-3xl shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Detail Surat Keluar</h2>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">No</span>
                        <span class="font-medium"><?= $row['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Index</span>
                        <span class="font-medium"><?= htmlspecialchars($row['index_surat'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nomor Urut</span>
                        <span class="font-medium"><?= htmlspecialchars($row['nomor_urut'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Isi Ringkas</span>
                        <span class="font-medium"><?= htmlspecialchars($row['perihal'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dari</span>
                        <span class="font-medium"><?= htmlspecialchars($row['pengolah'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal Surat</span>
                        <span class="font-medium"><?= date('d F Y', strtotime($row['tanggal_surat'])) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nomor Surat</span>
                        <span class="font-medium"><?= htmlspecialchars($row['nomor_surat'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lampiran</span>
                        <span class="font-medium"><?= htmlspecialchars($row['lampiran'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pengolah</span>
                        <span class="font-medium"><?= htmlspecialchars($row['pengolah'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Catatan</span>
                        <span class="font-medium"><?= htmlspecialchars($row['catatan'] ?? '-') ?></span>
                    </div>
                </div>
                <!-- Lampiran File -->
                <div class="mt-8">
                    <p class="text-gray-500 mb-3">File Surat</p>
                    <?php if (!empty($row['file_surat'])): ?>
                        <a href="<?= UPLOAD_URL . $row['file_surat'] ?>" target="_blank"
                           class="inline-flex items-center gap-4 bg-white border border-gray-300 hover:border-blue-400 px-6 py-4 rounded-2xl transition w-full max-w-md">
                            <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            <div>
                                <p class="font-medium">Lampiran Surat</p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($row['lampiran'] ?? 'file_surat.pdf') ?></p>
                            </div>
                        </a>
                    <?php else: ?>
                        <p class="text-gray-400 italic">Tidak ada file lampiran</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ==================== SIDEBAR ==================== -->
        <div class="space-y-6">

            <!-- Informasi Surat -->
            <div class="bg-white rounded-3xl shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Informasi Surat</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-4 py-1 bg-emerald-100 text-emerald-700 rounded-2xl text-xs font-medium">Terkirim</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat Oleh</span>
                        <span class="font-medium"><?= htmlspecialchars($row['pembuat_nama'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dilihat</span>
                        <span class="font-medium">
                            <?php
                            if (isset($row['dilihat']) && $row['dilihat']) {
                                echo date('d/m/Y H:i', strtotime($row['dilihat']));
                            } else {
                                echo 'Belum dilihat';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>



            <!-- Action Buttons -->
            <div class="bg-white rounded-3xl shadow p-6 space-y-3">
                <a href="../arsip/index.php?id=<?= $row['id'] ?>" 
                   class="w-full bg-white border border-gray-300 hover:bg-gray-50 py-4 rounded-2xl font-medium flex items-center justify-center gap-2 transition">
                    <i class="fas fa-archive"></i> Arsipkan
                </a>

                <button type="button" onclick="history.back()"
                   class="block text-center w-full bg-gray-100 hover:bg-gray-200 py-4 rounded-2xl font-medium transition">
                    ← Kembali
                </button>
            </div>
        </div>
    </div>
</div>



<script>
function openDisposisiModal() {
    document.getElementById('modalDisposisi').classList.remove('hidden');
}

function closeDisposisiModal() {
    document.getElementById('modalDisposisi').classList.add('hidden');
}



function prosesDisposisi() {
    alert('Disposisi berhasil dikirim!');
    closeDisposisiModal();
}
</script>

<?php include '../../assets/layout_footer.php'; ?>
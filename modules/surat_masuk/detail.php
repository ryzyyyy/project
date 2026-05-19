<?php
require_once '../../config/koneksi.php';
cekLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $_SESSION['error'] = "ID surat tidak valid.";
    header("Location: surat_masuk.php");
    exit;
}

// Update kolom 'dilihat' jika ada
$hasDilihat = false;
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM surat_masuk LIKE 'dilihat'");
if ($result && mysqli_num_rows($result) > 0) {
    $hasDilihat = true;
}

if ($hasDilihat) {
    $cek = mysqli_query($koneksi, "SELECT dilihat FROM surat_masuk WHERE id = $id LIMIT 1");
    $cekRow = mysqli_fetch_assoc($cek);
    if ($cekRow && empty($cekRow['dilihat'])) {
        mysqli_query($koneksi, "UPDATE surat_masuk SET dilihat = NOW() WHERE id = $id");
    }
}

// Ambil data surat utama
$data = mysqli_query($koneksi, "SELECT sm.*, u.nama as penerima_nama 
    FROM surat_masuk sm 
    LEFT JOIN users u ON sm.penerima_id = u.id 
    WHERE sm.id = $id LIMIT 1");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    $_SESSION['error'] = "Surat tidak ditemukan.";
    header("Location: surat_masuk.php");
    exit;
}

// Ambil riwayat disposisi (sesuaikan nama tabel jika berbeda)
$riwayat = mysqli_query($koneksi, "SELECT d.*, u.nama as nama_pengirim, u2.nama as nama_penerima 
    FROM disposisi d 
    LEFT JOIN users u ON d.dari = u.id 
    LEFT JOIN users u2 ON d.kepada = u2.id 
    WHERE d.surat_masuk_id = $id 
    ORDER BY d.created_at DESC");

$pageTitle = 'Detail Surat Masuk';
$activeMenu = 'surat-masuk';

include '../../assets/layout_header.php';
?>

<div class="space-y-6 p-6">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="dashboard.php" class="hover:text-gray-700">Dashboard</a>
        <span>›</span>
        <a href="surat_masuk.php" class="hover:text-gray-700">Surat Masuk</a>
        <span>›</span>
        <span class="text-gray-800 font-medium">Detail Surat #<?= htmlspecialchars($row['nomor_surat']) ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card Utama Detail Surat (Custom Field) -->
            <div class="bg-white rounded-3xl shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Detail Surat Masuk</h2>
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
                        <span class="font-medium"><?= htmlspecialchars($row['pengirim'] ?? '-') ?></span>
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
                        <span class="text-gray-500">Pengolah</span>
                        <span class="font-medium"><?= htmlspecialchars($row['pengolah'] ?? '-') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-4 py-1 bg-blue-100 text-blue-700 rounded-2xl text-xs font-medium">Diterima</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dilihat</span>
                        <span class="font-medium">
                            <?= $row['dilihat'] ? date('d/m/Y H:i', strtotime($row['dilihat'])) : 'Belum dilihat' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Disposisi -->
            <div class="bg-white rounded-3xl shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Riwayat Disposisi</h3>
                
                <?php if (mysqli_num_rows($riwayat) > 0): ?>
                    <div class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
                        <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                        <div class="flex gap-4 relative">
                            <div class="w-6 h-6 bg-[#003087] text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 z-10">
                                D
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">
                                    Disposisi ke <?= htmlspecialchars($r['nama_penerima'] ?? 'Bagian terkait') ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= date('d F Y H:i', strtotime($r['created_at'])) ?> • Oleh <?= htmlspecialchars($r['nama_pengirim']) ?>
                                </p>
                                <?php if (!empty($r['catatan'])): ?>
                                <p class="text-xs text-gray-600 mt-2 italic">
                                    "<?= htmlspecialchars($r['catatan']) ?>"
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400 text-sm italic py-4">Belum ada riwayat disposisi</p>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-3xl shadow p-6 space-y-3">
                <a href="../../modules/disposisi/index.php?id=<?= $id ?>" 
                   class="w-full bg-[#003087] hover:bg-blue-900 text-white py-4 rounded-2xl font-medium flex items-center justify-center gap-2 transition">
                    <i class="fas fa-share"></i> Disposisi Surat
                </a>

                <a href="../../modules/arsip/index.php?id=<?= $id ?>" 
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

<!-- Modal Disposisi Sederhana -->
<div id="modalDisposisi" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-md mx-4 p-8">
        <h3 class="text-xl font-semibold mb-6">Disposisi Surat</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Disposisi</label>
                <select class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                    <option value="">Pilih Bidang / Penerima</option>
                    <!-- Isi dengan data bidang / user via PHP jika diperlukan -->
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Disposisi</label>
                <textarea class="w-full border border-gray-300 rounded-2xl px-4 py-3" rows="3" placeholder="Tambahkan catatan..."></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-8">
            <button onclick="closeDisposisiModal()" 
                    class="flex-1 py-3 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium">Batal</button>
            <button onclick="prosesDisposisi()" 
                    class="flex-1 py-3 bg-[#003087] text-white rounded-2xl font-medium">Kirim Disposisi</button>
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

function arsipkanSurat() {
    if (confirm('Arsipkan surat ini sekarang?')) {
        alert('Surat berhasil diarsipkan');
        // window.location = `../../proses/surat_proses.php?aksi=arsip&id=<?= $id ?>`;
    }
}

function prosesDisposisi() {
    alert('Disposisi berhasil dikirim!');
    closeDisposisiModal();
    // Refresh halaman atau tambahkan riwayat baru via AJAX nanti
}
</script>

<?php include '../../assets/layout_footer.php'; ?>
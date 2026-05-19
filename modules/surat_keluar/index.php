<?php
require_once '../../config/koneksi.php';
cekLogin();

$pageTitle = 'Surat Keluar';
$activeMenu = 'surat_keluar';

// Notifikasi
$notif = isset($_SESSION['notif']) ? $_SESSION['notif'] : '';
unset($_SESSION['notif']);

// Pagination & Search
$limit = 10;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (nomor_surat LIKE '%$search%' OR tujuan LIKE '%$search%' OR perihal LIKE '%$search%')";
}

$totalData = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_keluar $where"))[0];
$totalPage = ceil($totalData / $limit);

$data = mysqli_query($koneksi, "SELECT sk.*, u.nama as pembuat_nama 
    FROM surat_keluar sk 
    LEFT JOIN users u ON sk.dibuat_oleh = u.id 
    $where 
    ORDER BY sk.created_at DESC LIMIT $limit OFFSET $offset");

include '../../assets/layout_header.php';
?>

<!-- FIX: CSS modal menggunakan class 'show' sesuai main.js -->
<style>
    .modal-overlay { display: none; }
    .modal-overlay.show { display: flex; }
</style>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">📤 Surat Keluar</h1>
        <button onclick="openModalTambah()" 
                class="bg-[#003087] hover:bg-blue-900 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
            <i class="fas fa-plus"></i> Tambah Surat Keluar
        </button>
    </div>

    <?php if ($notif): ?>
    <div id="notif" class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($notif) ?>
    </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="bg-white p-5 rounded-2xl shadow-sm">
        <form method="GET" class="flex gap-3">
            <div class="relative flex-1 max-w-md">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500"
                       placeholder="Cari nomor surat, tujuan, atau perihal...">
            </div>
            <button type="submit" class="bg-[#003087] hover:bg-blue-900 text-white px-8 rounded-2xl transition">
                <i class="fas fa-search"></i> Cari
            </button>
            <?php if ($search): ?>
                <a href="?" class="bg-gray-200 hover:bg-gray-300 px-6 rounded-2xl flex items-center text-gray-700 transition">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-left w-12">No</th>
                        <th class="px-6 py-5 text-left">Nomor Surat</th>
                        <th class="px-6 py-5 text-left">Tanggal</th>
                        <th class="px-6 py-5 text-left">Tujuan</th>
                        <th class="px-6 py-5 text-left">Perihal</th>
                        <!-- FIX: Ganti kolom Klasifikasi → Pengolah -->
                        <th class="px-6 py-5 text-left">Pengolah</th>
                        <th class="px-6 py-5 text-left">Pembuat</th>
                        <th class="px-6 py-5 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-5"><?= $no++ ?></td>
                            <td class="px-6 py-5 font-medium"><?= htmlspecialchars($row['nomor_surat']) ?></td>
                            <td class="px-6 py-5"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['tujuan']) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars(substr($row['perihal'], 0, 45)) ?><?= strlen($row['perihal']) > 45 ? '...' : '' ?></td>
                            <!-- FIX: Tampilkan pengolah, hapus badge klasifikasi -->
                            <td class="px-6 py-5"><?= htmlspecialchars($row['pengolah'] ?? '-') ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['pembuat_nama'] ?? '-') ?></td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-2xl text-sm transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="editSurat(<?= htmlspecialchars(json_encode($row)) ?>)" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-2xl text-sm transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="../../proses/surat_keluar_proses.php?aksi=hapus&id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus surat ini?')" 
                                       class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-2xl text-sm transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-20 text-gray-500">
                                📭 Belum ada data surat keluar
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPage > 1): ?>
        <div class="px-6 py-5 border-t flex items-center justify-between bg-gray-50">
            <p class="text-sm text-gray-600">Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $totalData) ?> dari <?= $totalData ?> data</p>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                       class="px-4 py-2 rounded-2xl <?= $i == $page ? 'bg-[#003087] text-white' : 'bg-white border hover:bg-gray-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== MODAL TAMBAH & EDIT ==================== -->
<!-- FIX: Ganti class 'hidden' → 'modal-overlay' agar kompatibel dengan main.js -->
<div id="modalSurat" class="modal-overlay fixed inset-0 bg-black/70 items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 shadow-2xl max-h-[92vh] overflow-hidden">
        
        <div class="px-6 py-5 border-b flex items-center justify-between bg-gray-50 rounded-t-3xl">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Surat Keluar</h3>
            <!-- FIX: closeModal('modalSurat') sesuai fungsi di main.js -->
            <button type="button" onclick="closeModal('modalSurat')" class="text-3xl leading-none text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <form id="formSurat" action="../../proses/surat_keluar_proses.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" id="formAksi" value="tambah">
            <input type="hidden" name="id" id="editId">

            <div class="p-6 space-y-5 overflow-y-auto" style="max-height: calc(92vh - 180px);">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_surat" id="nomor_surat" required class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" required class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan <span class="text-red-500">*</span></label>
                    <input type="text" name="tujuan" id="tujuan" required class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal <span class="text-red-500">*</span></label>
                    <input type="text" name="perihal" id="perihal" required class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>

                <!-- FIX: Hapus field klasifikasi, pengolah dipindah ke grid bersama upload file -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengolah / Pembuat Surat</label>
                        <input type="text" name="pengolah" id="pengolah" class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                        <input type="file" name="file_surat" id="file_surat" class="w-full border border-gray-300 rounded-2xl px-4 py-3" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi / Ringkasan Surat</label>
                    <textarea name="isi_surat" id="isi_surat" rows="4" class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-5 border-t flex justify-end gap-3 bg-gray-50 rounded-b-3xl">
                <!-- FIX: closeModal('modalSurat') sesuai fungsi di main.js -->
                <button type="button" onclick="closeModal('modalSurat')" 
                        class="px-7 py-3 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-8 py-3 bg-[#003087] hover:bg-blue-900 text-white rounded-2xl font-medium transition">
                    💾 Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambah() {
    document.getElementById('modalTitle').textContent = 'Tambah Surat Keluar';
    document.getElementById('formAksi').value = 'tambah';
    document.getElementById('editId').value = '';
    document.getElementById('formSurat').reset();
    document.getElementById('tanggal_surat').value = '<?= date('Y-m-d') ?>';
    // FIX: pakai openModal() dari main.js
    openModal('modalSurat');
}

function editSurat(data) {
    document.getElementById('modalTitle').textContent = 'Edit Surat Keluar';
    document.getElementById('formAksi').value = 'edit';
    document.getElementById('editId').value = data.id;

    document.getElementById('nomor_surat').value   = data.nomor_surat   || '';
    document.getElementById('tanggal_surat').value = data.tanggal_surat || '';
    document.getElementById('tujuan').value        = data.tujuan        || '';
    document.getElementById('perihal').value       = data.perihal       || '';
    document.getElementById('pengolah').value      = data.pengolah      || '';
    document.getElementById('isi_surat').value     = data.isi_surat     || '';
    // FIX: hapus baris klasifikasi, pakai openModal() dari main.js
    openModal('modalSurat');
}

// Tombol ESC untuk menutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal('modalSurat');
});

<?php if ($notif): ?>
setTimeout(() => {
    const notif = document.getElementById('notif');
    if (notif) {
        notif.style.transition = 'opacity 0.5s';
        notif.style.opacity = '0';
        setTimeout(() => notif.remove(), 500);
    }
}, 5000);
<?php endif; ?>
</script>

<?php include '../../assets/layout_footer.php'; ?>
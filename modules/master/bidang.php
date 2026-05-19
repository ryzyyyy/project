<?php
require_once '../../config/koneksi.php';
cekLogin();
cekRole('admin');

$pageTitle = 'Manajemen Bidang';
$activeMenu = 'bidang';

$data = mysqli_query($koneksi, "SELECT b.*, COUNT(u.id) as jumlah_user 
    FROM bidang b 
    LEFT JOIN users u ON u.bidang_id = b.id 
    GROUP BY b.id 
    ORDER BY b.nama ASC");

$totalBidang = mysqli_num_rows($data);

$notif = '';
if (isset($_SESSION['notif'])) { 
    $notif = $_SESSION['notif']; 
    unset($_SESSION['notif']); 
}
$error = '';
if (isset($_SESSION['error'])) { 
    $error = $_SESSION['error']; 
    unset($_SESSION['error']); 
}

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
        <h1 class="text-2xl font-semibold text-gray-800">🏢 Manajemen Bidang</h1>
        <button onclick="openModalTambah()" 
                class="bg-[#003087] hover:bg-blue-900 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
            <i class="fas fa-plus"></i> Tambah Bidang
        </button>
    </div>

    <?php if ($notif): ?>
    <div id="notif" class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($notif) ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div id="error" class="bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Stats Card -->
    <div class="bg-white p-6 rounded-3xl shadow-sm flex items-center gap-5">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-4xl">
            🏢
        </div>
        <div>
            <h3 class="text-4xl font-bold text-gray-800"><?= $totalBidang ?></h3>
            <p class="text-gray-500 text-lg">Total Bidang</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-5 rounded-3xl shadow-sm">
        <div class="relative max-w-md">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" id="searchInput" 
                   class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500"
                   placeholder="Cari nama bidang atau kode..." onkeyup="filterTable()">
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="bidangTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-left w-12">No</th>
                        <th class="px-6 py-5 text-left">Kode Bidang</th>
                        <th class="px-6 py-5 text-left">Nama Bidang</th>
                        <th class="px-6 py-5 text-left">Kepala Bidang</th>
                        <th class="px-6 py-5 text-left">Jumlah Staf</th>
                        <th class="px-6 py-5 text-left">Status</th>
                        <th class="px-6 py-5 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-5"><?= $no++ ?></td>
                            <td class="px-6 py-5 font-medium">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    <?= htmlspecialchars($row['kode'] ?? 'BID-00') ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 font-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['kepala_bidang'] ?? '-') ?></td>
                            <td class="px-6 py-5">
                                <span class="px-4 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-medium">
                                    <?= $row['jumlah_user'] ?> orang
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editBidang(<?= htmlspecialchars(json_encode($row)) ?>)" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-2xl text-sm transition"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($row['jumlah_user'] == 0): ?>
                                    <a href="../../proses/bidang_proses.php?aksi=hapus&id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus bidang <?= addslashes($row['nama']) ?>?')"
                                       class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-2xl text-sm transition"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <button class="bg-gray-300 text-gray-500 p-3 rounded-2xl text-sm cursor-not-allowed" 
                                            title="Tidak dapat dihapus (masih ada user)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-20 text-gray-500">
                                📭 Belum ada data bidang
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====================== MODAL TAMBAH & EDIT ====================== -->
<!-- FIX: Ganti class 'hidden' → 'modal-overlay' agar kompatibel dengan main.js -->
<div id="modalBidang" class="modal-overlay fixed inset-0 bg-black/70 items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-lg mx-4 shadow-2xl overflow-hidden">
        
        <div class="px-6 py-5 border-b flex items-center justify-between bg-gray-50 rounded-t-3xl">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Bidang</h3>
            <!-- FIX: closeModal('modalBidang') sesuai fungsi di main.js -->
            <button type="button" onclick="closeModal('modalBidang')" class="text-3xl leading-none text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <form action="../../proses/bidang_proses.php" method="POST">
            <input type="hidden" name="aksi" id="formAksi" value="tambah">
            <input type="hidden" name="id" id="editId">

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Bidang</label>
                        <input type="text" name="kode" id="kode" 
                               class="w-full border border-gray-300 rounded-2xl px-4 py-3" 
                               placeholder="BID-01" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bidang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" 
                               class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kepala Bidang</label>
                    <input type="text" name="kepala_bidang" id="kepala_bidang" 
                           class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" 
                              class="w-full border border-gray-300 rounded-2xl px-4 py-3" rows="3"></textarea>
                </div>
            </div>

            <div class="px-6 py-5 border-t flex justify-end gap-3 bg-gray-50 rounded-b-3xl">
                <!-- FIX: closeModal('modalBidang') sesuai fungsi di main.js -->
                <button type="button" onclick="closeModal('modalBidang')" 
                        class="px-7 py-3 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-8 py-3 bg-[#003087] hover:bg-blue-900 text-white rounded-2xl font-medium transition">
                    💾 Simpan Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambah() {
    document.getElementById('modalTitle').textContent = 'Tambah Bidang Baru';
    document.getElementById('formAksi').value = 'tambah';
    document.getElementById('editId').value = '';
    document.getElementById('kode').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('kepala_bidang').value = '';
    document.getElementById('keterangan').value = '';
    // FIX: pakai openModal() dari main.js
    openModal('modalBidang');
}

function editBidang(data) {
    document.getElementById('modalTitle').textContent = 'Edit Bidang';
    document.getElementById('formAksi').value = 'edit';
    document.getElementById('editId').value = data.id;
    document.getElementById('kode').value = data.kode || '';
    document.getElementById('nama').value = data.nama || '';
    document.getElementById('kepala_bidang').value = data.kepala_bidang || '';
    document.getElementById('keterangan').value = data.keterangan || '';
    // FIX: pakai openModal() dari main.js
    openModal('modalBidang');
}

function filterTable() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#bidangTable tbody tr");
    rows.forEach(row => {
        row.style.display = row.textContent.toUpperCase().includes(input) ? "" : "none";
    });
}

// Tombol ESC untuk menutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal('modalBidang');
});
</script>

<?php include '../../assets/layout_footer.php'; ?>
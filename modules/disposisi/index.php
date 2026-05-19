<?php
require_once '../../config/koneksi.php';
cekLogin();

$pageTitle = 'Disposisi';
$activeMenu = 'disposisi';

// Notifikasi
$notif = isset($_SESSION['notif']) ? $_SESSION['notif'] : '';
unset($_SESSION['notif']);

// Pagination & Filter
$limit = 10;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (sm.nomor_surat LIKE '%$search%' OR sm.perihal LIKE '%$search%' OR sm.pengirim LIKE '%$search%')";
}

$totalData = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM disposisi d 
     LEFT JOIN surat_masuk sm ON d.surat_masuk_id = sm.id 
     $where"))[0];

$totalPage = ceil($totalData / $limit);

$data = mysqli_query($koneksi,
    "SELECT d.*, sm.nomor_surat, sm.perihal, sm.pengirim, sm.tanggal_surat, sm.pengolah 
     FROM disposisi d 
     LEFT JOIN surat_masuk sm ON d.surat_masuk_id = sm.id 
     $where 
     ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset");

// Data untuk modal
$listSuratMasuk = mysqli_query($koneksi, "SELECT id, nomor_surat, perihal, pengirim, tanggal_surat, lampiran, pengolah 
    FROM surat_masuk ORDER BY created_at DESC");

$suratMasukData = [];
while ($sm = mysqli_fetch_assoc($listSuratMasuk)) {
    $suratMasukData[$sm['id']] = $sm;
}

include '../../assets/layout_header.php';
?>

<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">📋 Disposisi Surat</h1>
        <button onclick="openModalTambah()" 
                class="bg-[#003087] hover:bg-blue-900 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
            <i class="fas fa-plus"></i> Tambah Disposisi
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
                       placeholder="Cari nomor surat, perihal, atau pengirim...">
            </div>
            <button type="submit" class="bg-[#003087] hover:bg-blue-900 text-white px-8 rounded-2xl transition">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="?" class="bg-gray-200 hover:bg-gray-300 px-6 rounded-2xl flex items-center text-gray-700 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-left w-12">No</th>
                        <th class="px-6 py-5 text-left">No. Surat</th>
                        <th class="px-6 py-5 text-left">Tanggal Surat</th>
                        <th class="px-6 py-5 text-left">Perihal</th>
                        <th class="px-6 py-5 text-left">Pengirim</th>
                        <th class="px-6 py-5 text-left">Ditujukan Kepada</th>
                        <th class="px-6 py-5 text-center">Tanggal Disposisi</th>
                        <th class="px-6 py-5 text-center">Print</th>
                        <!-- <th class="px-6 py-5 text-center w-44">Aksi</th> -->
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-5 text-center"><?= $no++ ?></td>
                            <td class="px-6 py-5 font-medium"><?= htmlspecialchars($row['nomor_surat'] ?? '-') ?></td>
                            <td class="px-6 py-5"><?= $row['tanggal_surat'] ? date('d/m/Y', strtotime($row['tanggal_surat'])) : '-' ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars(substr($row['perihal'] ?? '', 0, 45)) ?>...</td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['pengirim'] ?? '-') ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['kepada'] ?? '-') ?></td>
                            <td class="px-6 py-5 text-center"><?= $row['tanggal_disposisi'] ? date('d/m/Y', strtotime($row['tanggal_disposisi'])) : '-' ?></td>
                            <td class="px-6 py-5 text-center">
                                <a href="print.php?id=<?= $row['id'] ?>" target="_blank" 
                                   class="bg-teal-600 hover:bg-teal-700 text-white p-3 rounded-2xl text-sm inline-flex items-center gap-1">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                            <!-- <td class="px-6 py-5">Aksi</td> -->
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-20 text-gray-500">
                                📭 Belum ada data disposisi
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== MODAL DISPOSISI ==================== -->
<div id="modalDisposisi" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 shadow-2xl max-h-[92vh] overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b flex items-center justify-between bg-gray-50 rounded-t-3xl">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Disposisi</h3>
            <button type="button" id="btnCloseX" 
                    class="text-4xl leading-none text-gray-500 hover:text-red-600 transition-colors">
                ×
            </button>
        </div>

        <form action="../../proses/disposisi_proses.php" method="POST">
            <input type="hidden" name="aksi" id="formAksi" value="tambah">
            <input type="hidden" name="id" id="editId">

            <div class="p-6 space-y-5 overflow-y-auto" style="max-height: calc(92vh - 180px);">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Surat Masuk *</label>
                    <select name="surat_masuk_id" id="surat_masuk_id" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                        <option value="">-- Pilih Surat Masuk --</option>
                        <?php foreach ($suratMasukData as $id => $sm): ?>
                        <option value="<?= $id ?>"><?= htmlspecialchars($sm['nomor_surat']) ?> - <?= htmlspecialchars(substr($sm['perihal'], 0, 40)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kepada *</label>
                    <select name="kepada" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                        <option value="">-- Pilih Penerima --</option>
                        <option value="sekretaris">Sekretaris</option>
                        <option value="kepala_bidang_pendidikan_dasar">Kepala Bidang Pendidikan Dasar</option>
                        <option value="kepala_bidang_paud_dikmas">Kepala Bidang PAUD dan Dikmas</option>
                        <option value="kepala_bidang_olahraga">Kepala Bidang Olahraga</option>
                        <option value="kepala_bidang_kepemudaan">Kepala Bidang Kepemudaan</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Index</label>
                        <input type="text" name="index" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode *</label>
                        <input type="text" name="kode" id="kode" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut *</label>
                        <input type="text" name="nomor_urut" id="nomor_urut" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat *</label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Ringkas *</label>
                    <input type="text" name="isi_ringkas" id="isi_ringkas" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari *</label>
                    <input type="text" name="dari" id="dari" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat *</label>
                    <input type="text" name="nomor_surat" id="nomor_surat" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran *</label>
                    <input type="text" name="lampiran" id="lampiran" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengolah *</label>
                    <input type="text" name="pengolah" id="pengolah" class="w-full border border-gray-300 rounded-2xl px-4 py-3" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Diteruskan *</label>
                    <input type="date" name="tanggal_disposisi" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" class="w-full border border-gray-300 rounded-2xl px-4 py-3" rows="3"></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-5 border-t flex justify-end gap-3 bg-gray-50 rounded-b-3xl">
                <button type="button" id="btnBatal" 
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
// Modal Script - Versi Simpel & Kuat
const modal = document.getElementById('modalDisposisi');

function openModalTambah() {
    console.log('✅ Modal dibuka');
    document.getElementById('modalTitle').textContent = 'Tambah Disposisi';
    document.getElementById('formAksi').value = 'tambah';
    document.getElementById('editId').value = '';
    resetFormDisposisi();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    console.log('✅ Modal ditutup');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    resetFormDisposisi();
}

function resetFormDisposisi() {
    const form = modal.querySelector('form');
    if (form) form.reset();

    document.getElementById('kode').value = '';
    document.getElementById('nomor_urut').value = '';
    document.getElementById('isi_ringkas').value = '';
    document.getElementById('dari').value = '';
    document.getElementById('tanggal_surat').value = '';
    document.getElementById('nomor_surat').value = '';
    document.getElementById('lampiran').value = '';
    document.getElementById('pengolah').value = '';
    document.getElementById('surat_masuk_id').value = '';
}

// Event Listeners
document.getElementById('btnCloseX').addEventListener('click', closeModal);
document.getElementById('btnBatal').addEventListener('click', closeModal);

// Klik luar modal
modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
});

// Autofill Surat Masuk
var suratMasukData = <?= json_encode($suratMasukData) ?>;

document.getElementById('surat_masuk_id').addEventListener('change', function() {
    var id = this.value;
    var data = suratMasukData[id] || {};
    
    document.getElementById('nomor_urut').value     = data.id || '';
    document.getElementById('isi_ringkas').value    = data.perihal || '';
    document.getElementById('dari').value           = data.pengirim || '';
    document.getElementById('tanggal_surat').value  = data.tanggal_surat || '';
    document.getElementById('nomor_surat').value    = data.nomor_surat || '';
    document.getElementById('lampiran').value       = data.lampiran || '';
    document.getElementById('pengolah').value       = data.pengolah || '';
    
    var kode = (data.nomor_surat || '').split('/')[0] || '';
    document.getElementById('kode').value = kode;
});

console.log('Modal script loaded successfully');
</script>

<?php include '../../assets/layout_footer.php'; ?>
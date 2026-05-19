<?php
require_once '../../config/koneksi.php';
cekLogin();

$pageTitle = 'Arsip';
$activeMenu = 'arsip';

$limit = 10;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search    = isset($_GET['search'])    ? mysqli_real_escape_string($koneksi, $_GET['search'])    : '';
$jenis     = isset($_GET['jenis'])     ? mysqli_real_escape_string($koneksi, $_GET['jenis'])     : '';
$tahun     = isset($_GET['tahun'])     ? mysqli_real_escape_string($koneksi, $_GET['tahun'])     : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (nomor_surat LIKE '%$search%' OR perihal LIKE '%$search%' OR asal_instansi LIKE '%$search%')";
if ($jenis)  $where .= " AND jenis_surat = '$jenis'";
if ($tahun)  $where .= " AND YEAR(tanggal_surat) = '$tahun'";

$totalData = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM arsip $where"))[0];
$totalPage = ceil($totalData / $limit);

$data = mysqli_query($koneksi, "SELECT * FROM arsip $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

// Ambil data surat masuk dan keluar untuk dropdown
$suratMasukList = mysqli_query($koneksi, "SELECT id, nomor_surat, perihal FROM surat_masuk ORDER BY created_at DESC");
$suratKeluarList = mysqli_query($koneksi, "SELECT id, nomor_surat, perihal FROM surat_keluar ORDER BY created_at DESC");

$notif = '';
if (isset($_SESSION['notif'])) { $notif = $_SESSION['notif']; unset($_SESSION['notif']); }

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
        <h1 class="text-2xl font-semibold text-gray-800">🗄️ Arsip Surat</h1>
        <button onclick="openModalTambah()" 
                class="bg-[#003087] hover:bg-blue-900 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
            <i class="fas fa-plus"></i> Tambah Arsip
        </button>
    </div>

    <?php if ($notif): ?>
    <div id="notif" class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($notif) ?>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="relative flex-1 max-w-md">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500"
                       placeholder="Cari nomor surat, perihal, atau instansi...">
            </div>
            <select name="jenis" class="border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                <option value="">Semua Jenis</option>
                <option value="masuk"  <?= $jenis === 'masuk'  ? 'selected' : '' ?>>📥 Surat Masuk</option>
                <option value="keluar" <?= $jenis === 'keluar' ? 'selected' : '' ?>>📤 Surat Keluar</option>
            </select>
            <select name="tahun" class="border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500">
                <option value="">Semua Tahun</option>
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="bg-[#003087] hover:bg-blue-900 text-white px-6 rounded-2xl transition">Filter</button>
            <a href="?" class="bg-gray-200 hover:bg-gray-300 px-6 rounded-2xl flex items-center text-gray-700 transition">Reset</a>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-left w-12">No</th>
                        <th class="px-6 py-5 text-left">Kode Arsip</th>
                        <th class="px-6 py-5 text-left">Jenis</th>
                        <th class="px-6 py-5 text-left">No. Surat</th>
                        <th class="px-6 py-5 text-left">Perihal</th>
                        <th class="px-6 py-5 text-left">Asal Instansi</th>
                        <th class="px-6 py-5 text-left">Tanggal Surat</th>
                        <th class="px-6 py-5 text-left">Lokasi</th>
                        <th class="px-6 py-5 text-center">File</th>
                        <th class="px-6 py-5 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-5"><?= $no++ ?></td>
                            <td class="px-6 py-5 font-medium"><?= htmlspecialchars($row['kode_arsip']) ?></td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?= $row['jenis_surat'] === 'masuk' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= $row['jenis_surat'] === 'masuk' ? '📥 Masuk' : '📤 Keluar' ?>
                                </span>
                            </td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['nomor_surat']) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars(substr($row['perihal'], 0, 40)) ?>...</td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['asal_instansi']) ?></td>
                            <td class="px-6 py-5"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['lokasi_fisik'] ?? '-') ?></td>
                            <td class="px-6 py-5 text-center">
                                <?php if ($row['file_arsip']): ?>
                                    <a href="<?= UPLOAD_URL . $row['file_arsip'] ?>" target="_blank" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm">📄 Lihat</a>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editArsip(<?= htmlspecialchars(json_encode($row)) ?>)" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-2xl text-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="../../proses/arsip_proses.php?aksi=hapus&id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus arsip ini?')" 
                                       class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-2xl text-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-20 text-gray-500">📭 Tidak ada data arsip</td>
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
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&jenis=<?= urlencode($jenis) ?>&tahun=<?= urlencode($tahun) ?>" 
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
<div id="modalArsip" class="modal-overlay fixed inset-0 bg-black/70 items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-3xl mx-4 shadow-2xl max-h-[92vh] overflow-hidden">
        <div class="px-6 py-5 border-b flex items-center justify-between bg-gray-50 rounded-t-3xl">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Arsip</h3>
            <!-- FIX: closeModal('modalArsip') sesuai fungsi di main.js -->
            <button type="button" onclick="closeModal('modalArsip')" class="text-3xl leading-none text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <form action="../../proses/arsip_proses.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" id="formAksi" value="tambah">
            <input type="hidden" name="id" id="editId">

            <div class="p-6 space-y-5 overflow-y-auto" style="max-height: calc(92vh - 180px);">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Arsip *</label>
                        <input type="text" name="kode_arsip" id="kodeArsipAuto" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat *</label>
                        <select name="jenis_surat" id="jenisSuratAuto" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required onchange="generateKodeArsip(); showSuratDropdown(this.value)">
                            <option value="masuk">📥 Surat Masuk</option>
                            <option value="keluar">📤 Surat Keluar</option>
                        </select>
                    </div>
                </div>

                <!-- Surat Masuk Group -->
                <div class="form-group" id="groupSuratMasuk">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Surat Masuk *</label>
                    <select id="pilihSuratMasuk" name="surat_masuk_id" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                        <option value="">-- Pilih Surat Masuk --</option>
                        <?php if (isset($suratMasukList) && mysqli_num_rows($suratMasukList) > 0): ?>
                            <?php while ($sm = mysqli_fetch_assoc($suratMasukList)): ?>
                                <option value="<?= $sm['id'] ?>"><?= htmlspecialchars($sm['nomor_surat']) ?> - <?= htmlspecialchars(substr($sm['perihal'],0,40)) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <div id="infoSuratMasuk" class="text-xs text-gray-500 mt-1"></div>
                </div>

                <!-- Surat Keluar Group -->
                <div class="form-group" id="groupSuratKeluar" style="display:none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Surat Keluar *</label>
                    <select id="pilihSuratKeluar" name="surat_keluar_id" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                        <option value="">-- Pilih Surat Keluar --</option>
                        <?php if (isset($suratKeluarList) && mysqli_num_rows($suratKeluarList) > 0): ?>
                            <?php while ($sk = mysqli_fetch_assoc($suratKeluarList)): ?>
                                <option value="<?= $sk['id'] ?>"><?= htmlspecialchars($sk['nomor_surat']) ?> - <?= htmlspecialchars(substr($sk['perihal'],0,40)) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <div id="infoSuratKeluar" class="text-xs text-gray-500 mt-1"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat *</label>
                        <input type="text" name="nomor_surat" id="nomorSuratInput" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat *</label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal *</label>
                    <input type="text" name="perihal" id="perihalInput" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Instansi</label>
                    <input type="text" name="asal_instansi" id="asal_instansi" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Fisik</label>
                        <input type="text" name="lokasi_fisik" id="lokasi_fisik" class="w-full border border-gray-300 rounded-2xl px-4 py-3" placeholder="Rak / Box / Ordner">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                        <input type="file" name="file_arsip" class="w-full border border-gray-300 rounded-2xl px-4 py-3" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" class="w-full border border-gray-300 rounded-2xl px-4 py-3" rows="3"></textarea>
                </div>
            </div>

            <div class="px-6 py-5 border-t flex justify-end gap-3 bg-gray-50 rounded-b-3xl">
                <!-- FIX: closeModal('modalArsip') sesuai fungsi di main.js -->
                <button type="button" onclick="closeModal('modalArsip')" class="px-7 py-3 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium transition">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#003087] hover:bg-blue-900 text-white rounded-2xl font-medium transition">💾 Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>

<script>
function pad(num, size) {
    let s = num + "";
    while (s.length < size) s = "0" + s;
    return s;
}

function generateKodeArsip() {
    var tahun = new Date().getFullYear();
    var jenis = document.getElementById('jenisSuratAuto').value;
    var kode = 'ARS/' + pad(<?= rand(1,999) ?>, 3) + '/' + tahun + '/' + (jenis ? jenis.toUpperCase() : '');
    document.getElementById('kodeArsipAuto').value = kode;
}

function showSuratDropdown(type) {
    document.getElementById('groupSuratMasuk').style.display = (type === 'masuk') ? 'block' : 'none';
    document.getElementById('groupSuratKeluar').style.display = (type === 'keluar') ? 'block' : 'none';
}

function openModalTambah() {
    document.getElementById('modalTitle').textContent = 'Tambah Arsip';
    document.getElementById('formAksi').value = 'tambah';
    document.getElementById('editId').value = '';
    generateKodeArsip();
    // FIX: pakai openModal() dari main.js
    openModal('modalArsip');
}

function editArsip(data) {
    document.getElementById('modalTitle').textContent = 'Edit Arsip';
    document.getElementById('formAksi').value = 'edit';
    document.getElementById('editId').value = data.id;
    document.getElementById('kodeArsipAuto').value = data.kode_arsip;
    document.getElementById('jenisSuratAuto').value = data.jenis_surat;
    document.getElementById('nomorSuratInput').value = data.nomor_surat;
    document.getElementById('perihalInput').value = data.perihal;
    document.getElementById('tanggal_surat').value = data.tanggal_surat;
    document.getElementById('asal_instansi').value = data.asal_instansi || '';
    document.getElementById('lokasi_fisik').value = data.lokasi_fisik || '';
    showSuratDropdown(data.jenis_surat);
    // FIX: pakai openModal() dari main.js
    openModal('modalArsip');
}

// Tombol ESC untuk menutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal('modalArsip');
});
</script>

<?php include '../../assets/layout_footer.php'; ?>
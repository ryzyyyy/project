<?php
require_once '../../config/koneksi.php';
cekLogin();
cekRole('admin');

$pageTitle = 'Manajemen Pengguna';
$activeMenu = 'pengguna';

$data = mysqli_query($koneksi, "SELECT * FROM users ORDER BY nama ASC");
$bidangList = mysqli_query($koneksi, "SELECT id, nama FROM bidang ORDER BY nama ASC");

$totalPengguna = mysqli_num_rows($data);

$notif = $_SESSION['notif'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['notif'], $_SESSION['error']);

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
        <h1 class="text-2xl font-semibold text-gray-800">👥 Manajemen Pengguna</h1>
        <button onclick="openModalTambah()" 
                class="bg-[#003087] hover:bg-blue-900 text-white px-6 py-3 rounded-2xl flex items-center gap-2 font-medium transition">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>

    <?php if ($notif): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($notif) ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-2xl">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Total Pengguna Card -->
    <div class="bg-white p-6 rounded-3xl shadow-sm flex items-center gap-5">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-4xl">
            👤
        </div>
        <div>
            <h3 class="text-4xl font-bold text-gray-800"><?= $totalPengguna ?></h3>
            <p class="text-gray-500 text-lg">Total Pengguna</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-5 rounded-3xl shadow-sm">
        <div class="relative max-w-md">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" id="searchInput" 
                   class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500"
                   placeholder="Cari nama, username, atau email..." 
                   onkeyup="filterTable()">
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="penggunaTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-left w-12">No</th>
                        <th class="px-6 py-5 text-left">Pengguna</th>
                        <th class="px-6 py-5 text-left">Username</th>
                        <th class="px-6 py-5 text-left">Email</th>
                        <th class="px-6 py-5 text-left">Jabatan</th>
                        <th class="px-6 py-5 text-left">Role</th>
                        <th class="px-6 py-5 text-left">Status</th>
                        <th class="px-6 py-5 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-5"><?= $no++ ?></td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-[#003087] text-white rounded-2xl flex items-center justify-center font-semibold text-lg shadow-inner">
                                        <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                </div>
                            </td>
                            <td class="px-6 py-5 font-medium"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="px-6 py-5 text-gray-600"><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                            <td class="px-6 py-5 text-gray-600"><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                            <td class="px-6 py-5">
                                <span class="px-4 py-1.5 text-xs font-medium rounded-2xl 
                                    <?= $row['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                    <?= $row['role'] === 'admin' ? '👑 Admin' : '👤 User' ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-4 py-1.5 text-xs font-medium rounded-2xl 
                                    <?= $row['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= $row['is_active'] ? '✅ Aktif' : '❌ Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editUser(<?= htmlspecialchars(json_encode($row)) ?>)" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-2xl transition"
                                            title="Edit">
                                        ✏️
                                    </button>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="../../proses/user_proses.php?aksi=hapus&id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus <?= addslashes($row['nama']) ?>?')"
                                       class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-2xl transition"
                                       title="Hapus">
                                        🗑
                                    </a>
                                    <?php else: ?>
                                    <button class="bg-gray-300 text-gray-500 p-3 rounded-2xl cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">🗑</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-16 text-gray-500">
                                📭 Belum ada data pengguna
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
<div id="modalPengguna" class="modal-overlay fixed inset-0 bg-black/70 items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-lg mx-4 shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b flex items-center justify-between bg-gray-50 rounded-t-3xl">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Pengguna</h3>
            <!-- FIX: closeModal('modalPengguna') sesuai fungsi di main.js -->
            <button type="button" onclick="closeModal('modalPengguna')" class="text-3xl leading-none text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <form action="../../proses/user_proses.php" method="POST">
            <input type="hidden" name="aksi" id="formAksi" value="tambah">
            <input type="hidden" name="id" id="editId">

            <div class="p-6 space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" id="username" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bidang <span class="text-red-500">*</span></label>
                        <select name="bidang_id" id="bidang_id" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                            <option value="">-- Pilih Bidang --</option>
                            <?php if (isset($bidangList) && mysqli_num_rows($bidangList) > 0): ?>
                                <?php while ($b = mysqli_fetch_assoc($bidangList)): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama']) ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="passLabel">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-2xl px-4 py-3" minlength="6">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                        <select name="role" id="role" class="w-full border border-gray-300 rounded-2xl px-4 py-3" required>
                            <option value="user">👤 User</option>
                            <option value="admin">👑 Admin</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" id="is_active" class="w-full border border-gray-300 rounded-2xl px-4 py-3">
                        <option value="1">✅ Aktif</option>
                        <option value="0">❌ Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="px-6 py-5 border-t bg-gray-50 rounded-b-3xl flex justify-end gap-3">
                <!-- FIX: closeModal('modalPengguna') sesuai fungsi di main.js -->
                <button type="button" onclick="closeModal('modalPengguna')" class="px-7 py-3 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium transition">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#003087] hover:bg-blue-900 text-white rounded-2xl font-medium transition">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambah() {
    document.getElementById('modalTitle').textContent = 'Tambah Pengguna Baru';
    document.getElementById('formAksi').value = 'tambah';
    document.getElementById('editId').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('email').value = '';
    document.getElementById('jabatan').value = '';
    document.getElementById('role').value = 'user';
    document.getElementById('is_active').value = '1';
    document.getElementById('bidang_id').value = '';
    document.getElementById('passLabel').innerHTML = 'Password <span class="text-red-500">*</span>';
    // FIX: pakai openModal() dari main.js
    openModal('modalPengguna');
}

function editUser(data) {
    document.getElementById('modalTitle').textContent = 'Edit Pengguna';
    document.getElementById('formAksi').value = 'edit';
    document.getElementById('editId').value = data.id;
    document.getElementById('nama').value = data.nama;
    document.getElementById('username').value = data.username;
    document.getElementById('email').value = data.email || '';
    document.getElementById('jabatan').value = data.jabatan || '';
    document.getElementById('role').value = data.role;
    document.getElementById('is_active').value = data.is_active;
    document.getElementById('bidang_id').value = data.bidang_id || '';
    document.getElementById('passLabel').innerHTML = 'Password Baru (kosongkan jika tidak diubah)';
    // FIX: pakai openModal() dari main.js
    openModal('modalPengguna');
}

function filterTable() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#penggunaTable tbody tr");
    rows.forEach(row => {
        row.style.display = row.textContent.toUpperCase().includes(input) ? "" : "none";
    });
}

// Tombol ESC untuk menutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal('modalPengguna');
});
</script>

<?php include '../../assets/layout_footer.php'; ?>
<?php
require_once '../../config/koneksi.php';
cekLogin();


$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';

// Inisialisasi variabel rekap
$rekapMasuk = null;
$rekapKeluar = null;
$periodeAwal = $_GET['periode_awal'] ?? '';
$periodeAkhir = $_GET['periode_akhir'] ?? '';
if (isset($_GET['tampilkan_laporan'])) {
    // Validasi tanggal
    if ($periodeAwal && $periodeAkhir) {
        $awal = mysqli_real_escape_string($koneksi, $periodeAwal);
        $akhir = mysqli_real_escape_string($koneksi, $periodeAkhir);
        $qMasuk = mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_masuk WHERE tanggal_surat BETWEEN '$awal' AND '$akhir'");
        $qKeluar = mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_keluar WHERE tanggal_surat BETWEEN '$awal' AND '$akhir'");
        $rekapMasuk = (int)mysqli_fetch_row($qMasuk)[0];
        $rekapKeluar = (int)mysqli_fetch_row($qKeluar)[0];
    }
}


// Statistik
$totalMasuk      = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_masuk"))[0] ?? 0;
$totalKeluar     = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_keluar"))[0] ?? 0;
$totalDisposisi  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM disposisi"))[0] ?? 0;
$totalArsip      = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM arsip"))[0] ?? 0;

// Jumlah disposisi menunggu (belum diproses)
$countDisposisiBaru = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM disposisi WHERE status = 'menunggu'"))[0] ?? 0;

// Untuk notifikasi bell
$notifCount = $countDisposisiBaru;

// Surat Masuk Terbaru (hanya hari ini)
$today = date('Y-m-d');
$smTerbaru = mysqli_query($koneksi, "SELECT * FROM surat_masuk WHERE DATE(created_at) = '$today' ORDER BY created_at DESC");

// Surat Keluar Terbaru (hanya hari ini)
$skTerbaru = mysqli_query($koneksi, "SELECT * FROM surat_keluar WHERE DATE(created_at) = '$today' ORDER BY created_at DESC");

include '../../assets/layout_header.php';
?>

<style>
    .glass-card {
        background: rgba(255,255,255,0.75);
        box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10);
        backdrop-filter: blur(6px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255,255,255,0.18);
        transition: box-shadow .2s, transform .2s;
    }
    .glass-card:hover {
        box-shadow: 0 16px 40px 0 rgba(31,38,135,0.13);
        transform: translateY(-2px) scale(1.015);
    }
    .modern-table th, .modern-table td {
        border: none !important;
        padding-top: 1.1rem;
        padding-bottom: 1.1rem;
    }
    .modern-table thead {
        background: #f7fafc;
        font-weight: 600;
        letter-spacing: .01em;
    }
    .modern-table tbody tr {
        transition: background .15s;
    }
    .modern-table tbody tr:hover {
        background: #f1f5f9;
    }

    /* Chart Styling */
    .chart-container {
        position: relative;
        height: 340px;
        width: 100%;
    }
    #trendSuratChart {
        border-radius: 1rem;
        background: #f8fafc;
    }
</style>

<!-- Main Content -->
<div class="flex-1 p-4 md:p-8 overflow-auto bg-gradient-to-tr from-blue-50 via-white to-emerald-50 min-h-screen">
    <!-- Header Greeting -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 tracking-tight mb-1">Selamat Datang, <span class="text-blue-700"><?= $_SESSION['nama'] ?></span></h1>
            <p class="text-gray-500 text-lg"><?= date('l, d F Y') ?></p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 uppercase tracking-widest">Hari ini</p>
            <p class="text-2xl font-semibold text-gray-700 mt-1"><?= date('d M Y') ?></p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="glass-card p-6 flex items-center gap-5 group cursor-pointer hover:scale-105">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-400/20 to-blue-600/20 text-blue-600 rounded-2xl flex items-center justify-center text-3xl group-hover:bg-blue-600 group-hover:text-white transition">📥</div>
            <div>
                <h3 class="text-4xl font-extrabold text-gray-800 group-hover:text-blue-700 transition"><?= number_format($totalMasuk) ?></h3>
                <p class="text-gray-500 font-medium">Surat Masuk</p>
            </div>
        </div>
        <div class="glass-card p-6 flex items-center gap-5 group cursor-pointer hover:scale-105">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-400/20 to-emerald-600/20 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl group-hover:bg-emerald-600 group-hover:text-white transition">📤</div>
            <div>
                <h3 class="text-4xl font-extrabold text-gray-800 group-hover:text-emerald-700 transition"><?= number_format($totalKeluar) ?></h3>
                <p class="text-gray-500 font-medium">Surat Keluar</p>
            </div>
        </div>
        <div class="glass-card p-6 flex items-center gap-5 group cursor-pointer hover:scale-105">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400/20 to-orange-600/20 text-orange-600 rounded-2xl flex items-center justify-center text-3xl group-hover:bg-orange-600 group-hover:text-white transition">📋</div>
            <div>
                <h3 class="text-4xl font-extrabold text-gray-800 group-hover:text-orange-700 transition"><?= number_format($totalDisposisi) ?></h3>
                <p class="text-gray-500 font-medium">Disposisi</p>
                <?php if ($countDisposisiBaru > 0): ?>
                    <span class="inline-block mt-2 text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full font-semibold">Menunggu <?= $countDisposisiBaru ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="glass-card p-6 flex items-center gap-5 group cursor-pointer hover:scale-105">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-400/20 to-purple-600/20 text-purple-600 rounded-2xl flex items-center justify-center text-3xl group-hover:bg-purple-600 group-hover:text-white transition">🗄️</div>
            <div>
                <h3 class="text-4xl font-extrabold text-gray-800 group-hover:text-purple-700 transition"><?= number_format($totalArsip) ?></h3>
                <p class="text-gray-500 font-medium">Arsip</p>
            </div>
        </div>
    </div>

<?php
require_once '../../config/koneksi.php';
cekLogin();
cekRole('admin');

$pageTitle = 'Laporan Rekapan Surat';
$activeMenu = 'laporan';

$tgl_awal  = $_GET['tgl_awal']  ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$rekapMasuk     = null;
$rekapKeluar    = null;
$rekapDisposisi = null;

if (isset($_GET['tampilkan_laporan']) && $tgl_awal && $tgl_akhir) {
    $awal  = mysqli_real_escape_string($koneksi, $tgl_awal);
    $akhir = mysqli_real_escape_string($koneksi, $tgl_akhir);

    // Total surat masuk
    $rekapMasuk = (int)mysqli_fetch_row(mysqli_query($koneksi,
        "SELECT COUNT(*) FROM surat_masuk
         WHERE tanggal_surat BETWEEN '$awal' AND '$akhir'"))[0];

    // Sudah didisposisi
    $rekapDisposisi = (int)mysqli_fetch_row(mysqli_query($koneksi,
        "SELECT COUNT(DISTINCT sm.id) FROM surat_masuk sm
         JOIN disposisi d ON d.surat_masuk_id = sm.id
         WHERE sm.tanggal_surat BETWEEN '$awal' AND '$akhir'"))[0];

    // Total surat keluar
    $rekapKeluar = (int)mysqli_fetch_row(mysqli_query($koneksi,
        "SELECT COUNT(*) FROM surat_keluar
         WHERE tanggal_surat BETWEEN '$awal' AND '$akhir'"))[0];
}


?>

<div class="space-y-6 p-6">

    <h1 class="text-2xl font-semibold text-gray-800">📊 Laporan Rekapan Surat</h1>

    <!-- Filter -->
    <div class="bg-white rounded-3xl shadow p-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                <input type="date" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal) ?>"
                       class="border border-gray-300 rounded-2xl px-4 py-3">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>"
                       class="border border-gray-300 rounded-2xl px-4 py-3">
            </div>
            <button type="submit" name="tampilkan_laporan" value="1"
                    class="bg-[#003087] hover:bg-blue-900 text-white px-8 py-3 rounded-2xl font-medium transition">
                📊 Tampilkan Laporan
            </button>
        </form>
    </div>

    <?php if (isset($_GET['tampilkan_laporan'])): ?>
        <?php if (!$tgl_awal || !$tgl_akhir): ?>
            <!-- Validasi: tanggal belum diisi -->
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4">
                ⚠️ Harap isi tanggal awal dan tanggal akhir terlebih dahulu.
            </div>
        <?php else: ?>

            <!-- Label periode aktif -->
            <p class="text-sm text-gray-500">
                Menampilkan data periode
                <span class="font-semibold text-gray-700">
                    <?= date('d M Y', strtotime($tgl_awal)) ?> – <?= date('d M Y', strtotime($tgl_akhir)) ?>
                </span>
            </p>


            <!-- Hasil Rekapan: Tabel Surat Masuk dan Keluar -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabel Surat Masuk -->
                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-blue-700 mb-4">📥 Surat Masuk</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-4 py-2 border">No</th>
                                    <th class="px-4 py-2 border">No. Surat</th>
                                    <th class="px-4 py-2 border">Tanggal</th>
                                    <th class="px-4 py-2 border">Pengirim</th>
                                    <th class="px-4 py-2 border">Perihal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q = mysqli_query($koneksi, "SELECT * FROM surat_masuk WHERE tanggal_surat BETWEEN '$awal' AND '$akhir' ORDER BY tanggal_surat ASC");
                                if (mysqli_num_rows($q) > 0):
                                    while ($row = mysqli_fetch_assoc($q)):
                                ?>
                                <tr>
                                    <td class="px-4 py-2 border text-center"><?= $no++ ?></td>
                                    <td class="px-4 py-2 border">
                                        <a href="../surat_masuk/detail.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline" title="Lihat Detail">
                                            <?= htmlspecialchars($row['nomor_surat']) ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 border"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($row['pengirim']) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($row['perihal']) ?></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada data surat masuk</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tabel Surat Keluar -->
                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-amber-700 mb-4">📤 Surat Keluar</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border">
                            <thead class="bg-amber-50">
                                <tr>
                                    <th class="px-4 py-2 border">No</th>
                                    <th class="px-4 py-2 border">No. Surat</th>
                                    <th class="px-4 py-2 border">Tanggal</th>
                                    <th class="px-4 py-2 border">Tujuan</th>
                                    <th class="px-4 py-2 border">Perihal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q = mysqli_query($koneksi, "SELECT * FROM surat_keluar WHERE tanggal_surat BETWEEN '$awal' AND '$akhir' ORDER BY tanggal_surat ASC");
                                if (mysqli_num_rows($q) > 0):
                                    while ($row = mysqli_fetch_assoc($q)):
                                ?>
                                <tr>
                                    <td class="px-4 py-2 border text-center"><?= $no++ ?></td>
                                    <td class="px-4 py-2 border">
                                        <a href="../surat_keluar/detail.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline" title="Lihat Detail">
                                            <?= htmlspecialchars($row['nomor_surat']) ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 border"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($row['tujuan']) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($row['perihal']) ?></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada data surat keluar</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        <?php endif; ?>
    <?php endif; ?>

</div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-2 glass-card shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Tren Surat 6 Bulan Terakhir</h2>
            </div>
            <div class="chart-container">
                <canvas id="trendSuratChart"></canvas>
            </div>
        </div>

<?php
// Data tren surat 6 bulan terakhir
$bulan = [];
$masuk = [];
$keluar = [];
for ($i = 5; $i >= 0; $i--) {
    $bln = date('Y-m', strtotime("-{$i} months"));
    $bulan[] = date('M Y', strtotime($bln.'-01'));
    $qMasuk = mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_masuk WHERE DATE_FORMAT(tanggal_surat, '%Y-%m') = '$bln'");
    $qKeluar = mysqli_query($koneksi, "SELECT COUNT(*) FROM surat_keluar WHERE DATE_FORMAT(tanggal_surat, '%Y-%m') = '$bln'");
    $masuk[] = (int)mysqli_fetch_row($qMasuk)[0];
    $keluar[] = (int)mysqli_fetch_row($qKeluar)[0];
}
?>

        <!-- Disposisi Tertunda (simple) -->
        <div class="glass-card shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Disposisi Tertunda</h2>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="font-medium text-gray-700">Jumlah disposisi belum diproses: <b><?= $countDisposisiBaru ?></b></p>
                    <a href="../disposisi/index.php" class="text-blue-600 text-sm hover:underline mt-2 inline-block">
                        Lihat Semua Disposisi →
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Surat Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        
        <!-- Surat Masuk Terbaru -->
        <div class="glass-card shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h2 class="font-semibold text-lg">📥 Surat Masuk Terbaru</h2>
                <a href="../surat_masuk/index.php" class="text-blue-600 hover:underline text-sm">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full modern-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-left">No. Surat</th>
                            <th class="px-6 py-4 text-left">Pengirim</th>
                            <th class="px-6 py-4 text-left">Tanggal</th>
                            <th class="px-6 py-4 text-left">Perihal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (mysqli_num_rows($smTerbaru) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($smTerbaru)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($row['nomor_surat']) ?>
                                    <?php if (date('Y-m-d', strtotime($row['created_at'])) === date('Y-m-d')): ?>
                                        <span class="ml-2 inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full align-middle">Terbaru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['pengirim']) ?></td>
                                <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['perihal']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada surat masuk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Surat Keluar Terbaru -->
        <div class="glass-card shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h2 class="font-semibold text-lg">📤 Surat Keluar Terbaru</h2>
                <a href="../surat_keluar/index.php" class="text-blue-600 hover:underline text-sm">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full modern-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-left">No. Surat</th>
                            <th class="px-6 py-4 text-left">Tujuan</th>
                            <th class="px-6 py-4 text-left">Tanggal</th>
                            <th class="px-6 py-4 text-left">Perihal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (mysqli_num_rows($skTerbaru) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($skTerbaru)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($row['nomor_surat']) ?>
                                    <?php if (date('Y-m-d', strtotime($row['created_at'])) === date('Y-m-d')): ?>
                                        <span class="ml-2 inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full align-middle">Terbaru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['tujuan']) ?></td>
                                <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars(substr($row['perihal'], 0, 35)) ?>...</td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada surat keluar</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js Script - Minimalist Modern Style -->
<script>
// Update angka notifikasi bell jika ada elemen bell
document.addEventListener('DOMContentLoaded', function() {
    var notifBell = document.querySelector('#notifBtn .absolute');
    if (notifBell) {
        notifBell.textContent = "<?= $notifCount ?>";
        if (<?= $notifCount ?> === 0) notifBell.style.display = 'none';
        else notifBell.style.display = '';
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendSuratChart').getContext('2d');

const trendSuratChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [
            {
                label: 'Surat Masuk',
                data: <?= json_encode($masuk) ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                borderWidth: 3.5,
                tension: 0.45,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 3,
                pointHoverBorderWidth: 3
            },
            {
                label: 'Surat Keluar',
                data: <?= json_encode($keluar) ?>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                borderWidth: 3.5,
                tension: 0.45,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 3,
                pointHoverBorderWidth: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1800,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    padding: 25,
                    font: {
                        size: 13,
                        family: 'Inter, system-ui, sans-serif',
                        weight: '500'
                    },
                    color: '#374151'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                titleColor: '#e0f2fe',
                bodyColor: '#bae6fd',
                borderColor: '#64748b',
                borderWidth: 1,
                padding: 14,
                displayColors: true,
                boxPadding: 6,
                cornerRadius: 12,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 14, weight: '500' },
                callbacks: {
                    label: function(context) {
                        return ' ' + context.raw + ' surat';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(148, 163, 184, 0.12)',
                    lineWidth: 1,
                    drawBorder: false
                },
                ticks: {
                    color: '#64748b',
                    font: {
                        size: 12,
                        weight: '500'
                    },
                    padding: 12
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.12)',
                    lineWidth: 1,
                    drawBorder: false
                },
                ticks: {
                    color: '#64748b',
                    font: {
                        size: 12,
                        weight: '500'
                    },
                    padding: 15,
                    precision: 0
                }
            }
        },
        elements: {
            line: {
                borderJoinStyle: 'round'
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>

<?php include '../../assets/layout_footer.php'; ?>
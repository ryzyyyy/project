<?php
// assets/layout_header.php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/surat'); // Sesuaikan dengan folder project kamu
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> - Sistem Surat DISDIKPORA Kudus</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            transition: all 0.3s ease;
        }
        .menu-active {
            background-color: #003087;
            color: white;
            border-radius: 8px;
        }
        .card {
            transition: all 0.2s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <div class="sidebar w-72 bg-[#003087] text-white flex flex-col h-full" id="sidebar">
        <!-- Logo/Header -->
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <img src="https://ppid.kuduskab.go.id/packages/tugumuda/portal/img/logo-kudus.png" alt="Logo Kudus" class="w-12 h-12">
                <div>
                    <h1 class="font-bold text-xl">DISDIKPORA</h1>
                    <p class="text-xs text-blue-200 -mt-1">Kabupaten Kudus</p>
                </div>
            </div>
            <p class="text-blue-200 text-sm mt-4">Sistem Surat Masuk & Keluar</p>
        </div>

        <!-- Menu -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <a href="<?= BASE_URL ?>/modules/dashboard/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'dashboard' ? 'menu-active' : '' ?>">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>

            <div class="px-4 text-blue-300 text-xs font-medium mt-6 mb-2">SURAT</div>
            
            <a href="<?= BASE_URL ?>/modules/surat_masuk/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'surat_masuk' ? 'menu-active' : '' ?>">
                <i class="fas fa-inbox w-5"></i> Surat Masuk
            </a>
            
            <a href="<?= BASE_URL ?>/modules/surat_keluar/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'surat_keluar' ? 'menu-active' : '' ?>">
                <i class="fas fa-paper-plane w-5"></i> Surat Keluar
            </a>
            
            <a href="<?= BASE_URL ?>/modules/disposisi/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'disposisi' ? 'menu-active' : '' ?>">
                <i class="fas fa-clipboard-list w-5"></i> Disposisi
            </a>
            
            <a href="<?= BASE_URL ?>/modules/arsip/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'arsip' ? 'menu-active' : '' ?>">
                <i class="fas fa-archive w-5"></i> Arsip
            </a>

            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <div class="px-4 text-blue-300 text-xs font-medium mt-6 mb-2">MASTER DATA</div>
            
            <a href="<?= BASE_URL ?>/modules/master/user.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'pengguna' ? 'menu-active' : '' ?>">
                <i class="fas fa-users w-5"></i> Pengguna
            </a>
            
            <a href="<?= BASE_URL ?>/modules/master/bidang.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-blue-800 transition <?= ($activeMenu ?? '') === 'bidang' ? 'menu-active' : '' ?>">
                <i class="fas fa-building w-5"></i> Bidang
            <?php endif; ?>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-blue-800">
            <a href="<?= BASE_URL ?>/logout.php" 
               onclick="return confirm('Yakin ingin keluar dari sistem?')"
               class="flex items-center gap-3 px-4 py-3 text-sm text-red-300 hover:bg-red-900/30 hover:text-red-200 transition rounded-lg">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- NAVBAR -->
        <div class="bg-white border-b h-16 px-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-800 lg:hidden text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h2>
            </div>

            <div class="flex items-center gap-6">


                <!-- User Profile -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? '')) ?></p>
                    </div>
                    <div class="w-9 h-9 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-semibold text-lg">
                        <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </div>
        </div>

                <!-- PAGE CONTENT -->
                <div class="flex-1 overflow-auto p-6">

<?php
require_once '../../config/koneksi.php';

// Redirect jika sudah login
$timeoutMsg = '';
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
  $timeoutMsg = '<div style="color:red; margin-bottom:10px;">Sesi Anda telah berakhir karena tidak ada aktivitas selama 30 menit. Silakan login kembali.</div>';
}
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/index.php");
    exit();
}

$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Surat Masuk & Keluar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #003087 0%, #007BFF 100%);
    }
    .card {
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
    }
    .background-pattern {
      background-image: url('https://images.unsplash.com/photo-1580130684518-8c9f4e2a9b3e');
      background-size: cover;
      background-position: center;
      filter: brightness(0.6) blur(1px);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden">
  <?= $timeoutMsg ?>
  
  <!-- Background -->
  <div class="absolute inset-0 background-pattern"></div>
  
  <div class="relative z-10 max-w-5xl w-full flex items-center justify-center p-6">
    <div class="flex flex-col md:flex-row bg-white rounded-3xl overflow-hidden card w-full">
      
      <!-- Left Side -->
      <div class="hidden md:flex w-5/12 bg-[#003087] p-12 flex-col justify-between text-white">
        <div>
          <div class="flex items-center gap-3 mb-8">
              <img src="../../assets/img/logo.png" alt="Logo Kudus" class="w-16 h-16">
            <div>
              <h1 class="text-2xl font-bold">DISDIKPORA</h1>
              <p class="text-sm opacity-90">Kabupaten Kudus</p>
            </div>
          </div>
          <h2 class="text-3xl font-semibold leading-tight">Sistem Pengelolaan<br>Surat Masuk dan Keluar</h2>
          <p class="mt-6 opacity-90">Dinas Pendidikan, Kepemudaan dan Olahraga<br>Kabupaten Kudus</p>
        </div>
        
        <div class="text-sm opacity-75">
          © 2026 Dinas Pendidikan, Kepemudaan dan Olahraga Kabupaten Kudus
        </div>
      </div>
  <?php if ($error): ?>
    <div id="notif-error" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded shadow z-50 text-center">
      <?= $error ?>
    </div>
    <script>
      setTimeout(function() {
        var notif = document.getElementById('notif-error');
        if (notif) notif.style.display = 'none';
      }, 3500);
    </script>
  <?php endif; ?>
      <!-- Login Form -->
      <div class="w-full md:w-7/12 bg-white p-10 md:p-16">
        <div class="md:hidden flex items-center gap-3 mb-8">
          <img src="../assets/img/kudus.png" 
               alt="Logo Kudus" class="w-12 h-12">
          <div>
            <h1 class="text-xl font-bold text-[#003087]">DISDIKPORA KUDUS</h1>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Masuk ke Sistem</h2>
        <p class="text-gray-600 mb-8">Silakan login menggunakan akun Anda</p>

        <form class="space-y-6" action="../../proses/login_proses.php" method="POST">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Username / NIP</label>
            <div class="relative">
              <span class="absolute left-4 top-3.5 text-gray-400">
                <i class="fas fa-user"></i>
              </span>
              <input type="text" 
                     class="w-full pl-11 pr-4 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500 transition"
                     placeholder="Masukkan NIP atau Username" name="username" required>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
              <span class="absolute left-4 top-3.5 text-gray-400">
                <i class="fas fa-lock"></i>
              </span>
              <input type="password" id="password"
                     class="w-full pl-11 pr-12 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:border-blue-500 transition"
                     placeholder="••••••••" name="password" required id="password">
              <button type="button" onclick="togglePassword()" 
                      class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="eye"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
              <input type="checkbox" class="w-4 h-4 accent-blue-600">
              Ingat Saya
            </label>
            <a href="lupa_password.php" class="text-sm text-blue-600 hover:underline">Lupa Password?</a>
          </div>

          <button type="submit"
                  class="w-full bg-[#003087] hover:bg-[#002366] transition py-4 rounded-2xl text-white font-semibold text-lg shadow-lg">
            MASUK
          </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-8">
          © 2026 Dinas Pendidikan, Kepemudaan dan Olahraga Kabupaten Kudus • Versi 1.0
        </p>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordField = document.getElementById("password");
      const eye = document.getElementById("eye");
      if (passwordField.type === "password") {
        passwordField.type = "text";
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
      } else {
        passwordField.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
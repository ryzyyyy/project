<?php
// Form reset password setelah verifikasi user
require_once '../../config/koneksi.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: lupa_password.php');
    exit();
}

$error = '';
if (isset($_SESSION['reset_error'])) {
    $error = $_SESSION['reset_error'];
    unset($_SESSION['reset_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Reset Password Baru</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-center"><?= $error ?></div>
        <?php endif; ?>
        <form action="../../proses/reset_password_proses.php" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Password Baru</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required placeholder="Password baru">
            </div>
            <div>
                <label class="block mb-1 font-medium">Verifikasi Password Baru</label>
                <input type="password" name="password2" class="w-full border rounded px-3 py-2" required placeholder="Ulangi password baru">
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded font-semibold">Simpan Password Baru</button>
        </form>
    </div>
</body>
</html>

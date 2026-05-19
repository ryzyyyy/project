<?php
// Lupa Password - Form input email/username
require_once '../../config/koneksi.php';

$error = '';
$success = '';
if (isset($_SESSION['reset_success'])) {
    $success = $_SESSION['reset_success'];
    unset($_SESSION['reset_success']);
}
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
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Lupa Password</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-center"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-center"><?= $success ?></div>
        <?php endif; ?>
        <form action="../../proses/lupa_password_proses.php" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Username / Email</label>
                <input type="text" name="user" class="w-full border rounded px-3 py-2" required placeholder="Masukkan username atau email">
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded font-semibold">Kirim Permintaan Reset</button>
        </form>
        <div class="mt-4 text-center">
            <a href="login.php" class="text-blue-600 hover:underline">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>

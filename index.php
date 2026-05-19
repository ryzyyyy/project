<?php
require_once 'config/koneksi.php';

// Redirect ke dashboard jika sudah login, ke login jika belum
if (isset($_SESSION['user_id'])) {
    header("Location: modules/dashboard/index.php");
} else {
    header("Location: modules/auth/login.php");
}
exit();

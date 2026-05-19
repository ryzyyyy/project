# 📬 Sistem Manajemen Surat

Aplikasi manajemen surat dinas berbasis PHP & MySQL.

## 📁 Struktur Direktori

```
sistem_surat/
├── config/             → Konfigurasi database & fungsi global
├── assets/             → CSS, JS, dan file upload
│   ├── css/style.css   → Stylesheet utama
│   ├── js/main.js      → JavaScript utama
│   └── uploads/        → Folder file yang diupload (harus writable)
├── modules/            → Modul-modul aplikasi
│   ├── auth/login.php  → Halaman login
│   ├── dashboard/      → Dashboard statistik
│   ├── surat_masuk/    → CRUD surat masuk
│   ├── surat_keluar/   → CRUD surat keluar
│   ├── disposisi/      → Manajemen disposisi
│   ├── arsip/          → Arsip surat
│   └── master/         → Master data (user & bidang)
├── proses/             → Handler form/aksi (backend logic)
├── logout.php          → Proses logout
├── index.php           → Entry point (redirect)
└── database.sql        → Script setup database
```

## 🚀 Cara Instalasi

1. **Copy ke htdocs / www**
   ```
   htdocs/sistem_surat/
   ```

2. **Buat database**
   - Buka phpMyAdmin
   - Import file `database.sql`

3. **Konfigurasi koneksi**
   Edit `config/koneksi.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'sistem_surat');
   define('BASE_URL', 'http://localhost/sistem_surat');
   ```

4. **Set permission folder uploads**
   ```bash
   chmod 755 assets/uploads/
   ```

5. **Akses aplikasi**
   ```
   http://localhost/sistem_surat
   ```

## 🔐 Akun Default

| Username | Password | Role |
|----------|----------|------|
| admin    | password | Admin |
| staff    | (buat manual via SQL) | User |

> ⚠️ **Segera ganti password** setelah login pertama!

## ✨ Fitur

- ✅ Login dengan session & bcrypt password
- ✅ Dashboard statistik
- ✅ CRUD Surat Masuk (+ upload file PDF/JPG/PNG)
- ✅ CRUD Surat Keluar (+ upload file)
- ✅ Manajemen Disposisi
- ✅ Arsip Surat
- ✅ Master Data: Pengguna & Bidang
- ✅ Role-based access (Admin vs User)
- ✅ Pagination & pencarian/filter
- ✅ Responsive UI

## 🛠️ Teknologi

- PHP 8.x (Native, tanpa framework)
- MySQL / MariaDB
- CSS Custom (tanpa Bootstrap)
- JavaScript Vanilla

## 📝 Catatan

- Folder `assets/uploads/` harus bisa ditulis oleh web server
- Maksimal ukuran upload: 5MB per file
- Format file yang didukung: PDF, JPG, JPEG, PNG

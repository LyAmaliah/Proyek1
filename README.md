# HealthyHabit — Sistem Pembentukan Kebiasaan Hidup Sehat Berbasis Web Native

Aplikasi web **PHP Native + MySQL** bergaya **MVC sederhana** untuk membentuk kebiasaan hidup
sehat: hitung BMI, cek kandungan gizi & kalori, jelajahi resep sehat, serta direktori tempat
kuliner sehat (studi kasus **RealJuice Healthy Café**) dengan ulasan & rating.

---

## 1. Stack Teknologi

| Komponen  | Keterangan                                            |
|-----------|--------------------------------------------------------|
| Bahasa    | PHP Native (diuji pada PHP 8.2)                        |
| Database  | MySQL / MariaDB (XAMPP, phpMyAdmin)                    |
| Web Server| Apache + `mod_rewrite`                                 |
| Frontend  | HTML5, CSS3, JavaScript (vanilla), Bootstrap 5 (CDN)   |
| Font      | Poppins (Google Fonts)                                 |
| Arsitektur| MVC sederhana (Model–View–Controller)                  |

**Palet warna:** Hijau `#27824B` · Hijau muda `#E8F5E9` · Putih `#FFFFFF` · Oranye `#F5A623` · Abu-abu `#F5F5F5`

---

## 2. Struktur Direktori

```
healthyhabit/
├── index.php                     # Front Controller (semua request masuk ke sini)
├── .htaccess                     # Rewrite ke index.php + proteksi file sensitif
├── dev-router.php                # Router opsional untuk PHP built-in server
├── app/
│   ├── config/config.php         # Konfigurasi app, DB, upload, timezone
│   ├── routes.php                # Tabel rute (URL → Controller@method)
│   ├── core/                     # Inti MVC
│   │   ├── Database.php          # Koneksi PDO (singleton)
│   │   ├── Model.php             # Base Model (CRUD generik + prepared statement)
│   │   ├── Controller.php        # Base Controller (render view + layout)
│   │   ├── Router.php            # Router regex + verifikasi CSRF untuk POST
│   │   └── Auth.php              # Sesi login tunggal (admin & user via tabel `users`)
│   ├── helpers/helper.php        # e(), flash, CSRF, upload, BMI, kalori, format
│   ├── models/                   # model (User, Artikel, Gizi, Resep, Tempat, Menu,
│   │                             #   MenuCategory, KalkulatorBmi, Ulasan, LaporanStatistik,
│   │                             #   KategoriArtikel)
│   ├── controllers/              # Controller situs (Home, Auth, Artikel, Gizi, Resep,
│   │   │                         #   Tempat, User)
│   │   └── admin/                # 8 controller admin (Dashboard, Artikel, Kategori, Gizi,
│   │                             #   Resep, Tempat+Menu, Pengguna, Laporan)
│   └── views/
│       ├── layouts/              # main.php (situs), admin.php (sidebar admin), guest.php
│       ├── includes/             # header.php & footer.php (Bootstrap 5, Poppins, palet)
│       └── pages/                # View per modul (home, auth, artikel, gizi, resep,
│                                 #   tempat, user, admin/*)
├── assets/
│   ├── css/style.css             # Design system: palet, kartu, sidebar, media cetak
│   ├── js/main.js                # Kalkulator gizi, pratinjau BMI, konfirmasi hapus, sidebar
│   └── img/placeholder.svg       # Fallback gambar
├── uploads/                      # artikel/ resep/ tempat/ menu/ (hasil upload)
├── database/
│   ├── healthyhabit.sql          # Skema 11 tabel + data awal (import via phpMyAdmin)
│   └── import.php                # Impor cepat via CLI: php database/import.php
└── tests/smoke-test.php          # Uji end-to-end otomatis (74 pemeriksaan)
```

---

## 3. Cara Instalasi (XAMPP)

1. Salin folder `healthyhabit` ke `C:\xampp\htdocs\`.
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Impor database — pilih salah satu:
   - **phpMyAdmin:** buka <http://localhost/phpmyadmin> → tab *Import* → pilih
     `database/healthyhabit.sql` → *Go* (database `healthyhabit` dibuat otomatis).
   - **CLI:** `php database/import.php`
4. Pastikan `mod_rewrite` Apache aktif (default XAMPP sudah aktif).
5. Buka aplikasi: <http://localhost/healthyhabit/>

> Konfigurasi database berada di `app/config/config.php`
> (default XAMPP: host `127.0.0.1`, user `root`, password kosong).
> `BASE_URL` terdeteksi otomatis, sehingga aplikasi tetap jalan di root `htdocs` maupun subfolder.

### Akun Bawaan

| Peran | Username     | Password   |
|-------|--------------|------------|
| Admin | `admin`      | `admin123` |
| User  | `andi_sehat` | `user123`  |
| User  | `sari_dewi`  | `user123`  |

---

## 4. Pemetaan Kebutuhan Fungsional

### A. Admin
| Kode  | Modul                              | Implementasi                                                             |
|-------|------------------------------------|--------------------------------------------------------------------------|
| FR-01 | Login Admin (validasi)             | `/admin/login` → `AuthController@adminLogin/doAdminLogin` + `password_verify` |
| FR-02 | Kelola Artikel Kesehatan           | `/admin/artikel` (CRUD, upload foto, kategori, tanggal terbit)             |
| FR-03 | Kelola Kandungan Gizi & Kalori     | `/admin/gizi` (kalori, karbohidrat, protein, lemak per 100 gram)           |
| FR-04 | Kelola Resep Sehat                 | `/admin/resep` (bahan, langkah, waktu masak, kalori, status publikasi, foto) |
| FR-05 | Kelola Tempat & Menu Sehat         | `/admin/tempat` + `/admin/tempat/{id}/menu` (alamat, kontak, Google Maps, sub-menu) |
| FR-06 | Kelola Pengguna                    | `/admin/pengguna` (lihat, edit, hapus; BMI ikut dihitung ulang)            |
| FR-07 | Laporan Statistik + Cetak/PDF      | `/admin/laporan` & `/admin/laporan/cetak` (snapshot tersimpan, *Save as PDF*) |

### B. Pengguna
| Kode  | Modul                              | Implementasi                                                             |
|-------|------------------------------------|--------------------------------------------------------------------------|
| FR-08 | Registrasi & Login Pengguna        | `/auth/register`, `/auth/login` (nama, username, password, berat, tinggi, umur, jenis kelamin) |
| FR-09 | Kelola Profil & Kalkulator BMI     | `/profil` (update + BMI otomatis), `/bmi` (kalkulator, pratinjau langsung JS) |
| FR-10 | Lihat Artikel Kesehatan            | `/artikel` (pencarian + filter kategori), `/artikel/{id}`                  |
| FR-11 | Cek Gizi & Kalori                  | `/gizi` — pilih makanan & input gramasi → kalori/makro dihitung otomatis    |
| FR-12 | Lihat Resep Sehat                  | `/resep` (pencarian + filter waktu masak), `/resep/{id}`                   |
| FR-13 | Rekomendasi Tempat & Ulasan        | `/tempat`, `/tempat/{id}`, form ulasan + rating 1–5 (anti-duplikat)        |

---

## 5. Catatan Teknis

- **Keamanan:** seluruh query pakai *prepared statement* PDO, output di-escape (`e()`),
  password di-hash `password_hash()` (bcrypt), CSRF token pada semua form POST
  (request POST tanpa token ditolak `419`), validasi MIME + ukuran file upload (maks 2 MB).
- **BMI:** `berat ÷ tinggi(m)²` dengan kategori WHO Asia (Kurus < 18,5 · Normal 18,5–24,9 ·
  Kelebihan 25–29,9 · Obesitas ≥ 30).
- **Estimasi kalori:** rumus Mifflin-St Jeor × faktor aktivitas sedang (1,55).
- **Perhitungan gizi:** nilai tabel = per 100 gram → `(nilai per 100 g × gramasi) ÷ 100`.
- **Ekspor PDF (FR-07):** halaman cetak memakai CSS `@media print`; klik **Ekspor PDF** lalu pilih
  *Save as PDF* pada dialog cetak browser. Setiap pencetakan mencatat snapshot ke `laporan_statistik`.
- **Database:** 11 tabel (`users`, `kategori_artikel`, `artikel_kesehatan`,
  `kandungan_gizi`, `resep`, `tempat_sehat`, `menu`, `menu_categories`, `kalkulator_bmi`,
  `ulasan_rating`, `laporan_statistik`) dengan foreign key `ON DELETE CASCADE/SET NULL`, `utf8mb4`.
  Login memakai **satu tabel `users`** dengan kolom `role` (`admin` | `user`); kolom
  `id_admin` pada artikel/gizi/resep/tempat/laporan mengarah ke `users.id_pengguna`.
  Tabel `kategori_artikel` ditambahkan sebagai relasi untuk kolom `id_kategori_artikel`,
  dan `menu_categories` untuk kolom `menu.id_kategori_menu`.
- **Responsif:** grid Bootstrap 5 untuk Desktop/Tablet/Smartphone; sidebar admin berubah menjadi
  *off-canvas* di layar < 768 px.

---

## 6. Menjalankan Uji Otomatis (opsional)

Tanpa Apache (memakai PHP built-in server):

```bash
# Terminal 1 — dari folder proyek
php -S 127.0.0.1:8123 dev-router.php

# Terminal 2
php tests/smoke-test.php
```

Skrip menguji 74 pemeriksaan: status semua halaman, hak akses, login user & admin, update
profil + penyimpanan BMI, kalkulator BMI tamu, CRUD (gizi/artikel/menu), penolakan CSRF,
registrasi + BMI awal otomatis, ulasan & rating, snapshot laporan, serta aset desain
(Poppins, Bootstrap 5, Bootstrap Icons, palet warna).

```bash
php -l app/controllers/UserController.php   # cek sintaks satu file
```

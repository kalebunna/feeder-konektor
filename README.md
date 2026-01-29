<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Analisis Fitur dan Panduan Instalasi Project Feeder Stainas

Project ini adalah aplikasi penghubung (bridge) antara sistem lokal dengan **Neo Feeder PDDikti**. Berbasis Laravel 12, aplikasi ini memudahkan pengelolaan data mahasiswa, kurikulum, dan perkuliahan untuk disinkronisasi ke Feeder.

## 🚀 Fitur Utama

Secara keseluruhan, fitur-fitur yang ada dalam project ini meliputi:

### 1. Integrasi Neo Feeder

- **Feeder Service**: Wrapper API untuk berkomunikasi dengan Neo Feeder (GetToken, Proxy, Post, Delete).
- **Connection Test**: Fitur untuk melakukan uji coba koneksi ke Feeder.

### 2. Manajemen Data Master (Referensi)

- Sinkronisasi data referensi langsung dari Feeder ke database lokal, meliputi:
    - Agama, Wilayah (Kecamatan/Kabupaten/Provinsi), Negara.
    - Jenjang Pendidikan, Pekerjaan, Penghasilan.
    - Jenis Pendaftaran, Jalur Masuk, Pembiayaan.
    - Tahun Ajaran dan Semester.

### 3. Manajemen Mahasiswa

- **Biodata Mahasiswa**: Melihat dan sinkronisasi biodata mahasiswa.
- **Riwayat Pendidikan**: Sinkronisasi data registrasi mahasiswa ke Feeder.
- **Import Mahasiswa**:
    - Impor data mahasiswa baru dari file Excel/CSV.
    - Fitur **Smart-Mapping**: Otomatis memetakan data Excel (seperti nama wilayah, agama, pendidikan ortu) ke ID referensi Feeder.
    - Sinkronisasi batch untuk mengirim data yang telah diimpor ke Feeder.

### 4. Kurikulum dan Mata Kuliah

- **Mata Kuliah Lokal**: Manajemen data mata kuliah internal (CRUD, Import, Sync ke Feeder).
- **Kurikulum**:
    - Melihat daftar kurikulum per program studi.
    - Manajemen mata kuliah dalam kurikulum (Tambah/Hapus).
    - **Copy Kurikulum**: Memudahkan penyalinan daftar mata kuliah dari satu kurikulum ke kurikulum lain.

### 5. Akademik

- **Program Studi**: Daftar dan detail prodi.
- **Profil Perguruan Tinggi**: Informasi profil kampus dari Feeder.
    - **Tahun Ajaran & Semester**: Pengaturan periode aktif yang disinkronkan dengan Feeder.

---

## 🛠️ Langkah-Langkah Instalasi

Ikuti langkah berikut untuk memasang project di lingkungan lokal:

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (default) atau MySQL
- Akses ke Endpoint Neo Feeder PDDikti

### 1. Persiapan Awal

```bash
# Clone repository
git clone [url-repository]
cd feeder-stainas
```

### 2. Jalankan Setup Otomatis

Project ini sudah menyediakan script setup untuk memudahkan instalasi:

```bash
composer run setup
```

_Script ini akan menjalankan: composer install, copy .env, generate key, migrate database, npm install, dan build assets._

### 3. Konfigurasi Environment (.env)

Buka file `.env` dan sesuaikan pengaturan database serta **koneksi Feeder**. Gunakan variabel yang sudah disediakan di `.env.example` sebagai acuan:

```env
# Koneksi Database (Default: SQLite)
DB_CONNECTION=sqlite

# Koneksi ke Neo Feeder PDDikti (Pastikan variabel ini ada di .env)
FEEDER_URL=...
FEEDER_USER=...
FEEDER_PASS=...
```

### 4. Menjalankan Aplikasi

Anda dapat menjalankan aplikasi menggunakan server development Laravel:

```bash
php artisan serve
```

Atau menggunakan script dev yang menjalankan vite secara bersamaan:

```bash
composer run dev
```

---

## 📝 Catatan Penting

- **Database**: Jika menggunakan SQLite, pastikan file `database/database.sqlite` sudah ada (otomatis dibuat jika menjalankan `composer run setup`).
- **Sync Referensi**: Setelah instalasi berhasil, sangat disarankan untuk masuk ke menu **Reference** dan melakukan sinkronisasi semua tabel referensi agar fitur pemetaan (mapping) data mahasiswa berjalan lancar.

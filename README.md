# SIMPUS SD

Sistem Informasi Perpustakaan Sekolah Dasar. Aplikasi web untuk perpustakaan SD dengan **satu pengguna**, yaitu admin (petugas perpustakaan). Siswa dan guru hanya data anggota, bukan pengguna. Berjalan **online** di komputer perpustakaan — tidak ada aset yang dimuat dari CDN.

Stack: Laravel 13 + MariaDB/MySQL, Blade + Tailwind CSS v4 via Vite. Antarmuka, dokumen, dan istilah domain berbahasa Indonesia.

Fitur:

- Login, logout, ganti password, dan reset password darurat lewat terminal.
- Dashboard: jumlah judul, eksemplar, anggota aktif, buku dipinjam, buku terlambat, transaksi terbaru.
- Data master: kategori, buku, anggota (dengan pencarian dan filter).
- Sirkulasi: catat peminjaman, cetak slip A5, riwayat, batalkan transaksi, catat pengembalian (sebagian/penuh).
- Laporan: peminjaman per periode, buku belum kembali/terlambat, inventaris buku — bisa dicetak atau diekspor PDF/Excel.
- Pengaturan: identitas sekolah, kepala sekolah, petugas, lama pinjam, batas jumlah pinjam.

Spesifikasi adalah sumber kebenaran. Baca sebelum mengubah perilaku:


- `docs/04-Manual-Pengguna.md` — panduan instalasi, pemakaian, backup, dan reset password.

## Kebutuhan

- PHP ≥ 8.3
- MySQL 8 atau MariaDB 10.6+
- Node.js + npm (untuk build aset Vite)
- Browser modern (Chrome, Edge, atau Firefox)

Dependensi utama: `laravel/framework` 13, `barryvdh/laravel-dompdf` (PDF), `spatie/simple-excel` (Excel).

## Instalasi

```bash
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Atau jalankan sekali dengan skrip bawaan `composer setup` (menjalankan install, `.env`, key, migrate, dan build).

Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` dengan basis data lokal (`perpustakaan_sd`, user `perpus`).

## Login pertama

- Username: `admin`
- Password: `admin123`

Segera ganti password lewat menu **Ganti Password** setelah login pertama.

## Menjalankan

```bash
composer run dev
```

Alternatifnya: `php artisan serve` dan `npm run dev` di terminal terpisah.

Aplikasi dibuka di `http://localhost:8000`. Tidak butuh internet.

> Catatan: `npm run build` hanya menghasilkan class Tailwind yang ditemukan saat build. Jika memakai `php artisan serve` tanpa Vite dev server, jalankan `npm run build` lagi setelah menambah class baru di view, supaya tata letak baru benar-benar diterapkan.

## Pengujian

```bash
php artisan test
```

Menjalankan 94 test (PHPUnit, SQLite in-memory). Format kode: `vendor/bin/pint`.

## Reset password darurat

Jika admin lupa password:

```bash
php artisan admin:reset-password
```

## Backup dan restore

- MariaDB 13 ke atas: `mysqldump` masih berjalan tetapi sudah usang — gunakan `mariadb-dump`.
- MySQL (mis. Laragon): tetap `mysqldump`.

```bash
# Backup (MariaDB)
mariadb-dump -u perpus -p perpustakaan_sd > backup.sql

# Backup (MySQL/Laragon)
mysqldump -u perpus -p perpustakaan_sd > backup.sql

# Restore
mysql -u perpus -p perpustakaan_sd < backup.sql
```

## Struktur

Struktur mengikuti MVC Laravel. Tabel domain berbahasa Indonesia dan tunggal: `kategori`, `buku`, `anggota`, `peminjaman`, `detail_peminjaman`, `pengaturan` (lihat `docs/02-DPPL.md`).

# Perpustakaan UKK

Proyek ini adalah aplikasi perpustakaan sekolah berbasis web (PHP + MySQL) untuk kebutuhan UKK. Fokus utama: login admin/anggota, pengelolaan data buku dan anggota, serta transaksi peminjaman dan pengembalian. Aplikasi berjalan **offline** di localhost tanpa internet.

## Fitur Utama
- Login admin dan anggota
- CRUD data buku (admin)
- CRUD data anggota (admin)
- CRUD transaksi peminjaman (admin)
- Peminjaman buku (anggota)
- Pengembalian buku (anggota)
- Pencarian data (admin)

## Struktur Singkat
- `admin/` : halaman admin (dashboard, buku, anggota, transaksi)
- `siswa/` : halaman anggota (pinjam, kembali, riwayat)
- `auth/` : login, register, logout
- `config/` : koneksi database, kredensial admin
- `partials/` : header admin dan siswa
- `database.sql` : skema database + sample data

## Cara Menjalankan (Localhost)
1. Import database:
   - Buat database `perpustakaan_ukk`
   - Import file `database.sql`
2. Atur koneksi DB di `config/db.php` sesuai server lokal.
3. Jalankan server lokal (XAMPP/Laragon), lalu buka:
   - `http://localhost/perpustakaan_ukk`

## Akun Default
- Admin: `admin` / `admin123` (lihat `config/admin.php`)
- Anggota: tersedia di `database.sql` (password contoh: `password`)

## Catatan
Proyek ini menggunakan Bootstrap lokal (`bootstrap.min.css` dan `bootstrap.min.js`) tanpa custom CSS tambahan, sesuai kebutuhan ujian offline.

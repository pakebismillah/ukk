Rangkuman Proyek - Sistem Perpustakaan UKK (PHP Native + Bootstrap Lokal)

Ringkasan
- 2 role: admin (hardcode, tanpa tabel) dan anggota (tabel).
- Login tunggal, redirect sesuai role.
- Admin: CRUD buku, CRUD anggota, CRUD transaksi peminjaman.
- Anggota: daftar buku, pinjam, kembali, riwayat.
- Anggota bisa self-register (langsung aktif).
- 1 transaksi = 1 buku.
- Bootstrap lokal dari root project.

Struktur Database
- Tabel anggota: id_anggota, nis, nama, kelas, username, password
- Tabel buku: id_buku, judul, pengarang, penerbit, stok
- Tabel peminjaman: id_pinjam, id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, tgl_kembali, status
- File SQL: database.sql (sudah termasuk seed data)

Autentikasi & Role
- Admin hardcode di config/admin.php
  - username: admin
  - password: admin123
- Anggota login dari tabel anggota.
- Guard role: auth/guard.php

Fitur Admin
- Dashboard admin: ringkasan total buku, anggota, transaksi dipinjam.
- Buku:
  - List + search
  - Tambah/Edit/Hapus
- Anggota:
  - List + search
  - Tambah/Edit/Hapus
- Transaksi:
  - List + search + detail
  - Tambah/Edit/Hapus transaksi
  - Koreksi stok otomatis saat status berubah

Fitur Anggota
- Dashboard: tampilkan buku yang sedang dipinjam.
- Pinjam: cek stok, insert transaksi, stok -1.
- Kembali: update status, set tgl_kembali, stok +1.
- Riwayat: semua transaksi anggota.
- Register: self-register lewat auth/register.php.

Struktur Folder Utama
/config
  db.php
  admin.php
/auth
  login.php
  logout.php
  guard.php
  register.php
/admin
  index.php
  buku_list.php
  buku_form.php
  buku_delete.php
  anggota_list.php
  anggota_form.php
  anggota_delete.php
  transaksi_list.php
  transaksi_detail.php
  transaksi_form.php
  transaksi_delete.php
/siswa
  index.php
  pinjam.php
  kembali.php
  riwayat.php
/assets
  /css/app.css
  /bootstrap (placeholder, tidak dipakai jika Bootstrap root tersedia)
bootstrap.min.css (root)
bootstrap.min.js (root)
index.php
database.sql

Catatan Implementasi
- Bootstrap di-load dari root:
  - /perpustakaan_ukk/bootstrap.min.css
  - /perpustakaan_ukk/bootstrap.min.js
- Jika pakai XAMPP:
  - Proyek harus berada di C:\xampp\htdocs\perpustakaan_ukk
  - Import database.sql via phpMyAdmin
- Port MySQL disesuaikan di config/db.php (contoh: 3307).

UI/Theme
- Palet warna:
  - Primary: #2C2C2C
  - Accent: #C0724A
  - Neutral Light: #F7F4F0
  - Card White: #FDFCFB
  - Border: #E8E2DB
  - Muted: #8A8078
  - Success: #4A8C6F
  - Danger: #C0504A
  - Warning: #C9A84C
- Login page sudah custom layout (split visual + form).
- Dashboard admin, list & form sudah disesuaikan.

Testing Cepat
- Admin login: admin / admin123
- Anggota login: rina / password, budi / password
- CRUD buku & anggota berjalan
- Pinjam: stok berkurang
- Kembali: stok bertambah
- Admin CRUD transaksi berjalan

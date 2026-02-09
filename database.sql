CREATE DATABASE perpustakaan_ukk;
USE perpustakaan_ukk;

CREATE TABLE anggota (
  id_anggota INT AUTO_INCREMENT PRIMARY KEY,
  nis VARCHAR(20) UNIQUE,
  nama VARCHAR(100),
  kelas VARCHAR(20),
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255)
);

CREATE TABLE buku (
  id_buku INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150),
  pengarang VARCHAR(100),
  penerbit VARCHAR(100),
  stok INT DEFAULT 0
);

CREATE TABLE peminjaman (
  id_pinjam INT AUTO_INCREMENT PRIMARY KEY,
  id_anggota INT,
  id_buku INT,
  tgl_pinjam DATE,
  tgl_jatuh_tempo DATE,
  tgl_kembali DATE NULL,
  status ENUM('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
  FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
);

-- Sample data
INSERT INTO buku (judul, pengarang, penerbit, stok) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 5),
('Bumi', 'Tere Liye', 'Gramedia', 3),
('Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia', 4);

-- Password sample uses hash for plain "password"
INSERT INTO anggota (nis, nama, kelas, username, password) VALUES
('1001', 'Rina Putri', 'XI IPA 1', 'rina', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('1002', 'Budi Santoso', 'XI IPS 2', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

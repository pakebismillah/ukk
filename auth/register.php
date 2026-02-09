<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nis === '' || $nama === '' || $kelas === '' || $username === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id_anggota FROM anggota WHERE nis = ? OR username = ?");
        $stmt->bind_param("ss", $nis, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'NIS atau username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO anggota (nis, nama, kelas, username, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $nis, $nama, $kelas, $username, $hash);
            $stmt->execute();
            $success = 'Registrasi berhasil. Silakan login.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Anggota</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <section class="login-visual">
        <div class="eyebrow">Perpustakaan Sekolah</div>
        <h1>Buat Akun Anggota</h1>
        <p>Daftar akun anggota untuk mulai meminjam dan mengembalikan buku.</p>
        <div class="badge" style="background:#C0724A;color:#FDFCFB;padding:6px 10px;border-radius:999px;width:max-content;">
            Self Register
        </div>
    </section>

    <section class="login-card">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2">Registrasi</h3>
                <div class="text-muted mb-3">Lengkapi data di bawah ini.</div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Daftar</button>
                </form>
                <div class="login-footer">Sudah punya akun? <a href="login.php">Login</a></div>
            </div>
        </div>
    </section>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>

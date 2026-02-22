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
    <link href="/perpustakaan_ukk/assets/css/ui-bootstrap.css" rel="stylesheet">
    
</head>

<body class="bg-light">
    <main class="container-fluid px-0 app-shell">
        <div class="row g-0 min-vh-100">
            <section class="col-lg-6 d-none d-lg-flex flex-column justify-content-center p-5 text-white auth-hero-panel">
                <p class="text-uppercase small mb-2">Perpustakaan Sekolah</p>
                <h1 class="display-6 fw-bold">Buat Akun Anggota</h1>
                <p class="text-white-50 mb-4">Daftar akun anggota untuk mulai meminjam dan mengembalikan buku secara mandiri.</p>
                <span class="badge text-bg-light text-dark w-auto px-3 py-2">Self Register</span>
            </section>

            <section class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="card ui-card shadow-sm border-0 w-100" style="max-width: 520px;">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-2">Registrasi</h2>
                        <p class="text-muted mb-4">Lengkapi data di bawah ini.</p>

                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php elseif ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>

                        <form method="post" novalidate>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nis" class="form-label">NIS</label>
                                    <input id="nis" type="text" name="nis" class="form-control" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input id="nama" type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <input id="kelas" type="text" name="kelas" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input id="username" type="text" name="username" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100 mt-4" type="submit">Daftar</button>
                        </form>

                        <p class="small text-muted mt-3 mb-0">
                            Sudah punya akun?
                            <a href="login.php" class="link-primary text-decoration-none">Login</a>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="../bootstrap.min.js"></script>
</body>

</html>




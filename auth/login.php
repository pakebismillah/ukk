<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/admin.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === $ADMIN_USER && $password === $ADMIN_PASS) {
        $_SESSION['role'] = 'admin';
        header('Location: ../admin/index.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT id_anggota, password FROM anggota WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id_anggota, $password_hash);
    if ($stmt->fetch()) {
        if (password_verify($password, $password_hash)) {
            $_SESSION['role'] = 'anggota';
            $_SESSION['id_anggota'] = $id_anggota;
            header('Location: ../siswa/index.php');
            exit;
        }
    }
    $stmt->close();

    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Perpustakaan</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <section class="login-visual">
        <div class="eyebrow">Perpustakaan Sekolah</div>
        <h1>Masuk ke Sistem</h1>
        <p>Kelola data buku, anggota, dan transaksi peminjaman dengan alur yang rapi dan mudah dipahami.</p>
        <div class="badge" style="background:#C0724A;color:#FDFCFB;padding:6px 10px;border-radius:999px;width:max-content;">
            UKK Ready
        </div>
    </section>

    <section class="login-card">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2">Login</h3>
                <div class="text-muted mb-3">Silakan masuk sesuai akun kamu.</div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Masuk</button>
                </form>
                <div class="login-footer">
                    Admin default: admin / admin123 · <a href="register.php">Daftar anggota</a>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>

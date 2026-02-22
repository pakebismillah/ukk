<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/admin.php';

$error = '';
$password_hash = '';

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
    <link href="/perpustakaan_ukk/assets/css/ui-bootstrap.css" rel="stylesheet">
    
</head>

<body class="bg-light">
    <main class="container-fluid px-0 app-shell">
        <div class="row g-0 min-vh-100">
            <section class="col-lg-6 d-none d-lg-flex flex-column justify-content-center p-5 text-white auth-hero-panel">
                <p class="text-uppercase small mb-2">Perpustakaan Sekolah</p>
                <h1 class="display-6 fw-bold">Masuk ke Sistem</h1>
                <p class="text-white-50 mb-4">Kelola data buku, anggota, dan transaksi peminjaman dengan alur yang rapi dan mudah dipahami.</p>
                <span class="badge text-bg-light text-dark w-auto px-3 py-2">UKK Ready</span>
            </section>

            <section class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="card ui-card shadow-sm border-0 w-100" style="max-width: 440px;">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-2">Login</h2>
                        <p class="text-muted mb-4">Silakan masuk sesuai akun kamu.</p>

                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

                        <form method="post" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input id="username" type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Masuk</button>
                        </form>

                        <p class="small text-muted mt-3 mb-0">
                            Admin default: admin / admin123 |
                            <a href="register.php" class="link-primary text-decoration-none">Daftar anggota</a>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="../bootstrap.min.js"></script>
</body>

</html>




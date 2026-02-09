<?php
// LOGIC START
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nis = $nama = $kelas = $username = '';
$error = '';

if ($id > 0) {
    $stmt = $conn->prepare("SELECT nis, nama, kelas, username FROM anggota WHERE id_anggota=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nis, $nama, $kelas, $username);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nis === '' || $nama === '' || $kelas === '' || $username === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        if ($id > 0) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE anggota SET nis=?, nama=?, kelas=?, username=?, password=? WHERE id_anggota=?");
                $stmt->bind_param("sssssi", $nis, $nama, $kelas, $username, $hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE anggota SET nis=?, nama=?, kelas=?, username=? WHERE id_anggota=?");
                $stmt->bind_param("ssssi", $nis, $nama, $kelas, $username, $id);
            }
            $stmt->execute();
            $stmt->close();
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO anggota (nis, nama, kelas, username, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $nis, $nama, $kelas, $username, $hash);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: anggota_list.php');
        exit;
    }
}
// LOGIC END
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Anggota</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- HTML START -->
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Master Data</div>
            <h3 class="mb-0 fw-semibold"><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Anggota</h3>
        </div>
        <a class="btn btn-outline-secondary" href="anggota_list.php">Kembali</a>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="<?php echo htmlspecialchars($nis); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($nama); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?php echo htmlspecialchars($kelas); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Password <?php echo $id > 0 ? '(kosongkan jika tidak diubah)' : ''; ?></label>
                            <input type="password" name="password" class="form-control" <?php echo $id > 0 ? '' : 'required'; ?>>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline-secondary" href="anggota_list.php">Batal</a>
            </form>
        </div>
    </div>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
<!-- HTML END -->
</body>
</html>



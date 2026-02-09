<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$judul = $pengarang = $penerbit = '';
$stok = 0;
$error = '';

if ($id > 0) {
    $stmt = $conn->prepare("SELECT judul, pengarang, penerbit, stok FROM buku WHERE id_buku=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($judul, $pengarang, $penerbit, $stok);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $stok = (int)($_POST['stok'] ?? 0);

    if ($judul === '' || $pengarang === '' || $penerbit === '') {
        $error = 'Semua field wajib diisi.';
    } elseif ($stok < 0) {
        $error = 'Stok tidak boleh minus.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE buku SET judul=?, pengarang=?, penerbit=?, stok=? WHERE id_buku=?");
            $stmt->bind_param("sssii", $judul, $pengarang, $penerbit, $stok, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, penerbit, stok) VALUES (?,?,?,?)");
            $stmt->bind_param("sssi", $judul, $pengarang, $penerbit, $stok);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: buku_list.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Buku</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Master Data</div>
            <h3 class="mb-0 fw-semibold"><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Buku</h3>
        </div>
        <a class="btn btn-outline-secondary" href="buku_list.php">Kembali</a>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="judul" class="form-control" value="<?php echo htmlspecialchars($judul); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" value="<?php echo htmlspecialchars($pengarang); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Penerbit</label>
                            <input type="text" name="penerbit" class="form-control" value="<?php echo htmlspecialchars($penerbit); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?php echo (int)$stok; ?>" required>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline-secondary" href="buku_list.php">Batal</a>
            </form>
        </div>
    </div>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>



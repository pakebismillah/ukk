<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$q = trim($_GET['q'] ?? '');
$buku = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT id_buku, judul, pengarang, penerbit, stok FROM buku WHERE judul LIKE ? OR pengarang LIKE ? ORDER BY id_buku DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $buku = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query("SELECT id_buku, judul, pengarang, penerbit, stok FROM buku ORDER BY id_buku DESC");
    if ($result) {
        $buku = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Buku</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Master Data</div>
            <h3 class="mb-0 fw-semibold">Data Buku</h3>
        </div>
        <a class="btn btn-primary" href="buku_form.php">Tambah Buku</a>
    </div>

    <form class="mb-3" method="get">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari judul/pengarang" value="<?php echo htmlspecialchars($q); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($buku) === 0): ?>
            <tr><td colspan="5">Belum ada data.</td></tr>
        <?php else: ?>
            <?php foreach ($buku as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['pengarang']); ?></td>
                    <td><?php echo htmlspecialchars($row['penerbit']); ?></td>
                    <td><?php echo (int)$row['stok']; ?></td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="buku_form.php?id=<?php echo (int)$row['id_buku']; ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="buku_delete.php?id=<?php echo (int)$row['id_buku']; ?>" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>



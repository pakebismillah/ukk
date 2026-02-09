<?php
// LOGIC START
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$q = trim($_GET['q'] ?? '');
$anggota = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT id_anggota, nis, nama, kelas, username FROM anggota WHERE nama LIKE ? OR nis LIKE ? ORDER BY id_anggota DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $anggota = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query("SELECT id_anggota, nis, nama, kelas, username FROM anggota ORDER BY id_anggota DESC");
    if ($result) {
        $anggota = $result->fetch_all(MYSQLI_ASSOC);
    }
}
// LOGIC END
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Anggota</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- HTML START -->
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Master Data</div>
            <h3 class="mb-0 fw-semibold">Data Anggota</h3>
        </div>
        <a class="btn btn-primary" href="anggota_form.php">Tambah Anggota</a>
    </div>

    <form class="mb-3" method="get">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari nama/NIS" value="<?php echo htmlspecialchars($q); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($anggota) === 0): ?>
            <tr><td colspan="5">Belum ada data.</td></tr>
        <?php else: ?>
            <?php foreach ($anggota as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="anggota_form.php?id=<?php echo (int)$row['id_anggota']; ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="anggota_delete.php?id=<?php echo (int)$row['id_anggota']; ?>" onclick="return confirm('Hapus anggota ini?')">Hapus</a>
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
<!-- HTML END -->
</body>
</html>



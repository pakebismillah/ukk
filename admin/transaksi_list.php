<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$q = trim($_GET['q'] ?? '');
$rows = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare(
        "SELECT p.id_pinjam, a.nama, a.nis, b.judul, p.tgl_pinjam, p.tgl_jatuh_tempo, p.tgl_kembali, p.status
         FROM peminjaman p
         JOIN anggota a ON p.id_anggota = a.id_anggota
         JOIN buku b ON p.id_buku = b.id_buku
         WHERE a.nama LIKE ? OR a.nis LIKE ? OR b.judul LIKE ? OR p.status LIKE ?
         ORDER BY p.id_pinjam DESC"
    );
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query(
        "SELECT p.id_pinjam, a.nama, a.nis, b.judul, p.tgl_pinjam, p.tgl_jatuh_tempo, p.tgl_kembali, p.status
         FROM peminjaman p
         JOIN anggota a ON p.id_anggota = a.id_anggota
         JOIN buku b ON p.id_buku = b.id_buku
         ORDER BY p.id_pinjam DESC"
    );
    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Transaksi</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Transaksi</div>
            <h3 class="mb-0 fw-semibold">Data Peminjaman</h3>
        </div>
        <a class="btn btn-primary" href="transaksi_form.php">Tambah Transaksi</a>
    </div>
    <form class="mb-3" method="get">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari nama/nis/judul/status" value="<?php echo htmlspecialchars($q); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Anggota</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($rows) === 0): ?>
            <tr><td colspan="6">Belum ada transaksi.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama']); ?> (<?php echo htmlspecialchars($row['nis']); ?>)</td>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_pinjam']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_jatuh_tempo']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="transaksi_detail.php?id=<?php echo (int)$row['id_pinjam']; ?>">Detail</a>
                        <a class="btn btn-warning btn-sm" href="transaksi_form.php?id=<?php echo (int)$row['id_pinjam']; ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="transaksi_delete.php?id=<?php echo (int)$row['id_pinjam']; ?>" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
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



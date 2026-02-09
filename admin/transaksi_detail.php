<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = null;

if ($id > 0) {
    $stmt = $conn->prepare(
        "SELECT p.id_pinjam, a.nama, a.nis, b.judul, p.tgl_pinjam, p.tgl_jatuh_tempo, p.tgl_kembali, p.status
         FROM peminjaman p
         JOIN anggota a ON p.id_anggota = a.id_anggota
         JOIN buku b ON p.id_buku = b.id_buku
         WHERE p.id_pinjam=?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Transaksi</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Transaksi</div>
            <h3 class="mb-0 fw-semibold">Detail Transaksi</h3>
        </div>
        <a class="btn btn-outline-secondary" href="transaksi_list.php">Kembali</a>
    </div>
    <?php if (!$row): ?>
        <div class="alert alert-danger">Data tidak ditemukan.</div>
    <?php else: ?>
        <table class="table">
            <tr><th>Nama</th><td><?php echo htmlspecialchars($row['nama']); ?></td></tr>
            <tr><th>NIS</th><td><?php echo htmlspecialchars($row['nis']); ?></td></tr>
            <tr><th>Judul Buku</th><td><?php echo htmlspecialchars($row['judul']); ?></td></tr>
            <tr><th>Tgl Pinjam</th><td><?php echo htmlspecialchars($row['tgl_pinjam']); ?></td></tr>
            <tr><th>Jatuh Tempo</th><td><?php echo htmlspecialchars($row['tgl_jatuh_tempo']); ?></td></tr>
            <tr><th>Tgl Kembali</th><td><?php echo htmlspecialchars($row['tgl_kembali'] ?? '-'); ?></td></tr>
            <tr><th>Status</th><td><?php echo htmlspecialchars($row['status']); ?></td></tr>
        </table>
    <?php endif; ?>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>



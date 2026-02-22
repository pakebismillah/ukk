<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('anggota');
require_once __DIR__ . '/../config/db.php';

$id_anggota = (int)($_SESSION['id_anggota'] ?? 0);
$rows = [];

if ($id_anggota > 0) {
    $stmt = $conn->prepare(
        "SELECT p.id_pinjam, b.judul, p.tgl_pinjam, p.tgl_jatuh_tempo, p.status
         FROM peminjaman p
         JOIN buku b ON p.id_buku = b.id_buku
         WHERE p.id_anggota=? AND p.status='dipinjam'
         ORDER BY p.id_pinjam DESC"
    );
    $stmt->bind_param("i", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Anggota</title>
    <link href="/perpustakaan_ukk/assets/bootstrap/css/bbootstrap.min.css" rel="stylesheet">
    <link href="/perpustakaan_ukk/assets/css/ui-bootstrap.css" rel="stylesheet">

</head>

<body>
    <?php require_once __DIR__ . '/../partials/siswa_header.php'; ?>
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="text-muted">Dashboard</div>
                <h3 class="mb-0 fw-semibold">Buku yang Sedang Dipinjam</h3>
            </div>
            <span class="badge text-bg-primary">Anggota</span>
        </div>
        <div class="card ui-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rows) === 0): ?>
                            <tr>
                                <td colspan="4">Belum ada buku dipinjam.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo htmlspecialchars($row['tgl_pinjam']); ?></td>
                                <td><?php echo htmlspecialchars($row['tgl_jatuh_tempo']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </main>
    </div>
    </div>
    <script src="/perpustakaan_ukk/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
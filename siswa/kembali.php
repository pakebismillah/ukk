<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('anggota');
require_once __DIR__ . '/../config/db.php';

$id_anggota = (int)($_SESSION['id_anggota'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pinjam = (int)($_POST['id_pinjam'] ?? 0);
    if ($id_pinjam > 0 && $id_anggota > 0) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("SELECT id_buku, status FROM peminjaman WHERE id_pinjam=? AND id_anggota=? FOR UPDATE");
            $stmt->bind_param("ii", $id_pinjam, $id_anggota);
            $stmt->execute();
            $stmt->bind_result($id_buku, $status);
            $stmt->fetch();
            $stmt->close();

            if ($status === 'dipinjam') {
                $stmt = $conn->prepare("UPDATE peminjaman SET status='dikembalikan', tgl_kembali=CURDATE() WHERE id_pinjam=?");
                $stmt->bind_param("i", $id_pinjam);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku=?");
                $stmt->bind_param("i", $id_buku);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                $message = 'Pengembalian berhasil.';
            } else {
                $conn->rollback();
                $message = 'Transaksi sudah dikembalikan.';
            }
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Gagal mengembalikan buku.';
        }
    }
}

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
    <title>Pengembalian Buku</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
    <link href="/perpustakaan_ukk/assets/css/ui-bootstrap.css" rel="stylesheet">
    
</head>
<body>
<?php require_once __DIR__ . '/../partials/siswa_header.php'; ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Transaksi</div>
            <h3 class="mb-0 fw-semibold">Pengembalian Buku</h3>
        </div>
        <span class="badge text-bg-primary">Anggota</span>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card ui-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($rows) === 0): ?>
            <tr><td colspan="4">Tidak ada buku dipinjam.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_pinjam']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_jatuh_tempo']); ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id_pinjam" value="<?php echo (int)$row['id_pinjam']; ?>">
                            <button class="btn btn-warning btn-sm" type="submit">Kembalikan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table></div></div></div>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>







<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('anggota');
require_once __DIR__ . '/../config/db.php';

$id_anggota = (int)($_SESSION['id_anggota'] ?? 0);
$q = trim($_GET['q'] ?? '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_buku = (int)($_POST['id_buku'] ?? 0);
    if ($id_buku > 0 && $id_anggota > 0) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("SELECT stok FROM buku WHERE id_buku=? FOR UPDATE");
            $stmt->bind_param("i", $id_buku);
            $stmt->execute();
            $stmt->bind_result($stok);
            $stmt->fetch();
            $stmt->close();

            if ($stok > 0) {
                $stmt = $conn->prepare("INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'dipinjam')");
                $stmt->bind_param("ii", $id_anggota, $id_buku);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku=?");
                $stmt->bind_param("i", $id_buku);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                $message = 'Peminjaman berhasil.';
            } else {
                $conn->rollback();
                $message = 'Stok buku habis.';
            }
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Gagal meminjam buku.';
        }
    }
}

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
    <title>Pinjam Buku</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/siswa_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Transaksi</div>
            <h3 class="mb-0 fw-semibold">Pinjam Buku</h3>
        </div>
        <span class="badge">Anggota</span>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

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
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_buku" value="<?php echo (int)$row['id_buku']; ?>">
                            <button class="btn btn-success btn-sm" type="submit" <?php echo (int)$row['stok'] <= 0 ? 'disabled' : ''; ?>>
                                Pinjam
                            </button>
                        </form>
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



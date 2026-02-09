<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_anggota = 0;
$id_buku = 0;
$status = 'dipinjam';
$tgl_pinjam = '';
$tgl_jatuh_tempo = '';
$tgl_kembali = '';
$error = '';

// Data dropdown
$anggota_list = $conn->query("SELECT id_anggota, nis, nama FROM anggota ORDER BY nama ASC")->fetch_all(MYSQLI_ASSOC);
$buku_list = $conn->query("SELECT id_buku, judul, stok FROM buku ORDER BY judul ASC")->fetch_all(MYSQLI_ASSOC);

if ($id > 0) {
    $stmt = $conn->prepare("SELECT id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, tgl_kembali, status FROM peminjaman WHERE id_pinjam=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($id_anggota, $id_buku, $tgl_pinjam, $tgl_jatuh_tempo, $tgl_kembali, $status);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = (int)($_POST['id_anggota'] ?? 0);
    $id_buku = (int)($_POST['id_buku'] ?? 0);
    $status = $_POST['status'] ?? 'dipinjam';
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? date('Y-m-d');
    $tgl_jatuh_tempo = $_POST['tgl_jatuh_tempo'] ?? date('Y-m-d', strtotime('+7 days'));
    $tgl_kembali = $_POST['tgl_kembali'] ?? null;

    if ($id_anggota <= 0 || $id_buku <= 0) {
        $error = 'Anggota dan buku wajib dipilih.';
    } else {
        $conn->begin_transaction();
        try {
            if ($id > 0) {
                // Ambil data lama untuk koreksi stok
                $stmt = $conn->prepare("SELECT id_buku, status FROM peminjaman WHERE id_pinjam=? FOR UPDATE");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($old_buku, $old_status);
                $stmt->fetch();
                $stmt->close();

                // Update transaksi
                $stmt = $conn->prepare("UPDATE peminjaman SET id_anggota=?, id_buku=?, tgl_pinjam=?, tgl_jatuh_tempo=?, tgl_kembali=?, status=? WHERE id_pinjam=?");
                $stmt->bind_param("iissssi", $id_anggota, $id_buku, $tgl_pinjam, $tgl_jatuh_tempo, $tgl_kembali, $status, $id);
                $stmt->execute();
                $stmt->close();

                // Koreksi stok jika berubah
                if ($old_buku != $id_buku) {
                    if ($old_status === 'dipinjam') {
                        $stmt = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku=?");
                        $stmt->bind_param("i", $old_buku);
                        $stmt->execute();
                        $stmt->close();
                    }
                    if ($status === 'dipinjam') {
                        $stmt = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku=? AND stok > 0");
                        $stmt->bind_param("i", $id_buku);
                        $stmt->execute();
                        $stmt->close();
                    }
                } else {
                    if ($old_status === 'dipinjam' && $status === 'dikembalikan') {
                        $stmt = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku=?");
                        $stmt->bind_param("i", $id_buku);
                        $stmt->execute();
                        $stmt->close();
                    }
                    if ($old_status === 'dikembalikan' && $status === 'dipinjam') {
                        $stmt = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku=? AND stok > 0");
                        $stmt->bind_param("i", $id_buku);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            } else {
                // Cek stok jika status dipinjam
                if ($status === 'dipinjam') {
                    $stmt = $conn->prepare("SELECT stok FROM buku WHERE id_buku=? FOR UPDATE");
                    $stmt->bind_param("i", $id_buku);
                    $stmt->execute();
                    $stmt->bind_result($stok);
                    $stmt->fetch();
                    $stmt->close();
                    if ($stok <= 0) {
                        throw new Exception('Stok habis.');
                    }
                }

                $stmt = $conn->prepare("INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, tgl_kembali, status) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("iissss", $id_anggota, $id_buku, $tgl_pinjam, $tgl_jatuh_tempo, $tgl_kembali, $status);
                $stmt->execute();
                $stmt->close();

                if ($status === 'dipinjam') {
                    $stmt = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku=?");
                    $stmt->bind_param("i", $id_buku);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $conn->commit();
            header('Location: transaksi_list.php');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Gagal menyimpan transaksi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Transaksi</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted">Transaksi</div>
            <h3 class="mb-0 fw-semibold"><?php echo $id > 0 ? 'Edit' : 'Tambah'; ?> Peminjaman</h3>
        </div>
        <a class="btn btn-outline-secondary" href="transaksi_list.php">Kembali</a>
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
                            <label class="form-label">Anggota</label>
                            <select name="id_anggota" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($anggota_list as $a): ?>
                                    <option value="<?php echo (int)$a['id_anggota']; ?>" <?php echo $id_anggota == $a['id_anggota'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($a['nama'] . ' (' . $a['nis'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Buku</label>
                            <select name="id_buku" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($buku_list as $b): ?>
                                    <option value="<?php echo (int)$b['id_buku']; ?>" <?php echo $id_buku == $b['id_buku'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($b['judul'] . ' (stok: ' . $b['stok'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tgl Pinjam</label>
                            <input type="date" name="tgl_pinjam" class="form-control" value="<?php echo htmlspecialchars($tgl_pinjam ?: date('Y-m-d')); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" name="tgl_jatuh_tempo" class="form-control" value="<?php echo htmlspecialchars($tgl_jatuh_tempo ?: date('Y-m-d', strtotime('+7 days'))); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tgl Kembali</label>
                            <input type="date" name="tgl_kembali" class="form-control" value="<?php echo htmlspecialchars($tgl_kembali); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="dipinjam" <?php echo $status === 'dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                                <option value="dikembalikan" <?php echo $status === 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline-secondary" href="transaksi_list.php">Batal</a>
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



<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$total_buku = $conn->query("SELECT COUNT(*) AS c FROM buku")->fetch_assoc()['c'] ?? 0;
$total_anggota = $conn->query("SELECT COUNT(*) AS c FROM anggota")->fetch_assoc()['c'] ?? 0;
$total_pinjam = $conn->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status='dipinjam'")->fetch_assoc()['c'] ?? 0;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="/perpustakaan_ukk/bootstrap.min.css" rel="stylesheet">
    <link href="/perpustakaan_ukk/assets/css/ui-bootstrap.css" rel="stylesheet">
    
</head>
<body>
<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted">Dashboard</div>
            <h3 class="mb-0 fw-semibold">Ringkasan Admin</h3>
        </div>
        <div class="badge text-bg-primary">Admin Panel</div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-muted">Total Buku</div>
                    <h4><?php echo (int)$total_buku; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-muted">Total Anggota</div>
                    <h4><?php echo (int)$total_anggota; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ui-card">
                <div class="card-body">
                    <div class="text-muted">Sedang Dipinjam</div>
                    <h4><?php echo (int)$total_pinjam; ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
</div>
</div>
<script src="/perpustakaan_ukk/bootstrap.min.js"></script>
</body>
</html>






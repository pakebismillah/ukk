<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$display_name = 'Admin';
?>
<div class="container-fluid">
    <div class="row min-vh-100">
        <aside class="col-lg-2 col-md-3 bg-light border-end p-3">
            <div class="fw-bold mb-3">Perpustakaan UKK</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link px-0" href="/perpustakaan_ukk/admin/index.php">Dashboard</a>
                <a class="nav-link px-0" href="/perpustakaan_ukk/admin/buku_list.php">Buku</a>
                <a class="nav-link px-0" href="/perpustakaan_ukk/admin/anggota_list.php">Anggota</a>
                <a class="nav-link px-0" href="/perpustakaan_ukk/admin/transaksi_list.php">Transaksi</a>
                <a class="nav-link px-0 text-danger" href="/perpustakaan_ukk/auth/logout.php">Logout</a>
            </nav>
        </aside>
        <main class="col-lg-10 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="fw-semibold">Sistem Perpustakaan</div>
                    <div class="text-muted small">Admin Panel</div>
                </div>
                <span class="badge text-bg-light border text-dark"><?php echo htmlspecialchars($display_name); ?></span>
            </div>

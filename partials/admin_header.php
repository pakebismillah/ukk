<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$display_name = 'Admin';
$current = basename($_SERVER['PHP_SELF'] ?? '');

$nav = [
    ['label' => 'Dashboard', 'href' => '/perpustakaan_ukk/admin/index.php', 'match' => ['index.php']],
    ['label' => 'Buku', 'href' => '/perpustakaan_ukk/admin/buku_list.php', 'match' => ['buku_list.php', 'buku_form.php']],
    ['label' => 'Anggota', 'href' => '/perpustakaan_ukk/admin/anggota_list.php', 'match' => ['anggota_list.php', 'anggota_form.php']],
    ['label' => 'Transaksi', 'href' => '/perpustakaan_ukk/admin/transaksi_list.php', 'match' => ['transaksi_list.php', 'transaksi_form.php', 'transaksi_detail.php']],
];
?>
<div class="container-fluid px-0 app-shell">
    <div class="row g-0 min-vh-100">
        <aside class="col-lg-2 col-md-3 p-3 app-sidebar">
            <div class="app-brand mb-3">Perpustakaan UKK</div>
            <nav class="nav flex-column gap-1">
                <?php foreach ($nav as $item): ?>
                    <?php $active = in_array($current, $item['match'], true) ? ' active' : ''; ?>
                    <a class="nav-link app-nav-link<?php echo $active; ?>" href="<?php echo $item['href']; ?>">
                        <?php echo $item['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="col-lg-10 col-md-9 app-main">
            <div class="app-topbar d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">Sistem Perpustakaan</div>
                    <div class="text-muted small">Admin Panel</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-light border text-dark"><?php echo htmlspecialchars($display_name); ?></span>
                    <a class="btn btn-sm btn-outline-secondary" href="/perpustakaan_ukk/auth/logout.php">Logout</a>
                </div>
            </div>

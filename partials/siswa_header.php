<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$display_name = 'Anggota';
if (isset($_SESSION['id_anggota'])) {
    $id = (int)$_SESSION['id_anggota'];
    $stmt = $conn->prepare("SELECT nama FROM anggota WHERE id_anggota=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nama);
    if ($stmt->fetch()) {
        $display_name = $nama;
    }
    $stmt->close();
}

$current = basename($_SERVER['PHP_SELF'] ?? '');
$nav = [
    ['label' => 'Dashboard', 'href' => '/perpustakaan_ukk/siswa/index.php', 'match' => ['index.php']],
    ['label' => 'Pinjam', 'href' => '/perpustakaan_ukk/siswa/pinjam.php', 'match' => ['pinjam.php']],
    ['label' => 'Kembali', 'href' => '/perpustakaan_ukk/siswa/kembali.php', 'match' => ['kembali.php']],
    ['label' => 'Riwayat', 'href' => '/perpustakaan_ukk/siswa/riwayat.php', 'match' => ['riwayat.php']],
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
                    <div class="text-muted small">Dashboard Anggota</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-light border text-dark"><?php echo htmlspecialchars($display_name); ?></span>
                    <a class="btn btn-sm btn-outline-secondary" href="/perpustakaan_ukk/auth/logout.php">Logout</a>
                </div>
            </div>

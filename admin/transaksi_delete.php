<?php
require_once __DIR__ . '/../auth/guard.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT id_buku, status FROM peminjaman WHERE id_pinjam=? FOR UPDATE");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id_buku, $status);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM peminjaman WHERE id_pinjam=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        if ($status === 'dipinjam') {
            $stmt = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku=?");
            $stmt->bind_param("i", $id_buku);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
    }
}

header('Location: transaksi_list.php');
exit;

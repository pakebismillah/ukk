<?php
$conn = new mysqli("127.0.0.1", "root", "", "perpustakaan_ukk", 3307);
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}
?>


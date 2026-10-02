<?php

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID siswa tidak valid.");
}

/* Nonaktifkan siswa */
$sql = "UPDATE t_siswa
        SET status_aktif = 0,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: menu1.php");
    exit;
} else {
    echo "Gagal menghapus data: " . $stmt->error;
}

?>
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Kelola_Wali_Kelas.php");
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: Kelola_Wali_Kelas.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    
    // DITAMBAHKAN ?pesan=dihapus AGAR NOTIFIKASI MUNCUL DI KELOLA WALI KELAS
    header("Location: Kelola_Wali_Kelas.php?pesan=dihapus");
    exit;
}

mysqli_stmt_close($stmt);
die("Gagal menghapus data wali kelas.");
?>
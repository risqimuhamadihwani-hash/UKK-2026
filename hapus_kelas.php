<?php
require_once "config/koneksi.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu3.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: menu3.php?status=hapus_berhasil");
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

if (stripos($error, 'foreign key constraint fails') !== false) {
    die("Kelas tidak bisa dihapus karena masih digunakan oleh data penempatan siswa. Periksa tabel t_kelas_siswa terlebih dahulu.");
}

die("Gagal menghapus kelas: " . htmlspecialchars($error));
?>

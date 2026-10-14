<?php
require_once "config/koneksi.php";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = mysqli_prepare($koneksi, "DELETE FROM t_siswa WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: menu1.php");
        exit;
    } else {
        echo "Gagal menghapus data siswa: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: menu1.php");
    exit;
}
?>
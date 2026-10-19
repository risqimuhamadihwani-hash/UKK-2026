<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($id > 0) {
        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM t_kelas_siswa WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: menu5.php");
exit;
?>

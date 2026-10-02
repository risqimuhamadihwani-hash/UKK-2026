<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id='$id'"
);

header("Location: menu3.php");
exit;
?>
<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id='$id'"
);

header("Location: menu4.php");
exit;
?>
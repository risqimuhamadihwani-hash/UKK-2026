<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM t_guru WHERE id='$id'");

header("Location: menu2.php");
exit;
?>
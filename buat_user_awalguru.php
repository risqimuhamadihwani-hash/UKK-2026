<?php

include 'config/koneksi.php';

$name = "guru";
$email = "guru@gmail.com";
$password = password_hash("guru123", PASSWORD_DEFAULT);
$role = "guru";

// Masukkan ke tabel t_users
$sql = "INSERT INTO t_users (name, email, password, role)
        VALUES ('$name', '$email', '$password', '$role')";

if (mysqli_query($koneksi, $sql)) {

    echo "User guru telah dibuat, silakan hapus file ini.";

} else {

    echo "Gagal membuat user: " . mysqli_error($koneksi);

}

?>

<?php
include 'config/koneksi.php';

$email = 'guru@gmail.com';
$password = password_hash('guru123', PASSWORD_DEFAULT);

$sql = "UPDATE t_users
        SET password = ?
        WHERE email = ? AND role = 'guru'";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ss", $password, $email);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Password Guru berhasil diperbaiki. ";
        echo "Silakan login dan hapus file ini.";
    } else {
        echo "Akun Guru tidak ditemukan. Periksa email dan role di t_users.";
    }
} else {
    echo "Gagal: " . htmlspecialchars(mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
?>

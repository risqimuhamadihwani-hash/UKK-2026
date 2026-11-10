<?php
include 'config/koneksi.php';

$name = "Aldi Maulana";
$email = "aldimaulana@gmail.com";
$password = password_hash("aldi123", PASSWORD_DEFAULT);
$role = "guru";

// Cek apakah email sudah terdaftar di t_users
$cek = mysqli_query($koneksi, "SELECT id FROM t_users WHERE email = '$email'");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah ada, update password dan namanya
    $sql = "UPDATE t_users SET name = '$name', password = '$password', role = '$role' WHERE email = '$email'";
    $aksi = "diperbarui";
} else {
    // Jika belum ada, insert baru
    $sql = "INSERT INTO t_users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    $aksi = "dibuat";
}

if (mysqli_query($koneksi, $sql)) {
    echo "Akun wali kelas ($email) berhasil $aksi! Silakan hapus file ini, lalu coba login.";
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>
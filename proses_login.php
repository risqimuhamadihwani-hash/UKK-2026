<?php
session_start();

include 'config/koneksi.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email == '' || $password == '') {
    die('Email dan password wajib diisi.');
}

$email = mysqli_real_escape_string($koneksi, $email);

$sql = "SELECT id, name, email, password, role
        FROM t_users
        WHERE email = '$email'
        LIMIT 1";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Error database: ' . mysqli_error($koneksi));
}

if (mysqli_num_rows($query) == 1) {

    $user = mysqli_fetch_assoc($query);

    // Cek password
    if (password_verify($password, $user['password'])) {

        // Simpan data user ke session
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Masuk dashboard
        header("Location: dashboard.php");
        exit;

    } else {
        echo "Password salah.";
    }

} else {
    echo "Email tidak ditemukan.";
}
?>
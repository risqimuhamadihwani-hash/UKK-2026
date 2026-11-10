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
        // Ubah role ke huruf kecil agar seragam
        $_SESSION['role'] = strtolower(trim($user['role']));

        // KEDUA ROLE SEKARANG DIARAHKAN KE DASHBOARD.PHP YANG SAMA
        if ($_SESSION['role'] === 'guru' || $_SESSION['role'] === 'admin') {
            header("Location: dashboard.php");
            exit;
        } else {
            // Jika ada role lain yang tidak dikenal
            echo "Role akun tidak valid.";
        }

    } else {
        echo "Password salah.";
    }

} else {
    echo "Email tidak ditemukan.";
}
?>
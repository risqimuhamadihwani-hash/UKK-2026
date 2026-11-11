<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (trim($email) === '' || trim($password) === '') {
    echo "<script>alert('Email dan password wajib diisi.'); window.location='login.php';</script>";
    exit;
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

    // Bypass: Mendukung password_verify, MD5, ataupun teks biasa (plain text)
    $password_db = $user['password'];
    $is_match = false;

    if (password_verify($password, $password_db) || md5($password) === $password_db || $password === $password_db) {
        $is_match = true;
    }

    if ($is_match) {
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = strtolower(trim($user['role']));

        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Password salah.'); window.location='login.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Email tidak ditemukan.'); window.location='login.php';</script>";
    exit;
}
?>
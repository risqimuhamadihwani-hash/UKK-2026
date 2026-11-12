<?php
// Memulai session untuk mengecek status login pengguna
session_start();

// Jika pengguna sudah login, arahkan ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
} else {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}
?>
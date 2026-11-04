<?php
session_start();

// Cek apakah user sudah login dan memiliki role yang sesuai (misal: admin)
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Sertakan file koneksi database
include 'config/koneksi.php';

// Periksa apakah parameter 'id' ada di URL dan bernilai angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Gunakan prepared statement untuk keamanan dari SQL Injection
    $sql = "DELETE FROM t_pelanggaran WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Berhasil dihapus, arahkan kembali ke halaman menu8.php
            header("Location: menu8.php?status=sukses_hapus");
            exit;
        } else {
            // Gagal eksekusi query
            echo "Gagal menghapus data: " . mysqli_error($koneksi);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Gagal menyiapkan statement: " . mysqli_error($koneksi);
    }
} else {
    // Jika ID tidak valid atau tidak ada, kembalikan ke menu
    header("Location: menu8.php");
    exit;
}
?>
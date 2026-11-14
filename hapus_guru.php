<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

// Cek apakah ada parameter id yang dikirimkan melalui URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Lakukan query hapus data guru berdasarkan ID
    $query = mysqli_query($koneksi, "DELETE FROM t_guru WHERE id = '$id'");

    if ($query) {
        // Redirect kembali ke halaman Kelola Guru dengan membawa pesan sukses hapus
        header("Location: Kelola_Guru.php?pesan=dihapus");
        exit;
    } else {
        // Jika query gagal
        header("Location: Kelola_Guru.php?pesan=gagal");
        exit;
    }
} else {
    // Jika tidak ada parameter id, langsung kembalikan ke halaman kelola guru
    header("Location: Kelola_Guru.php");
    exit;
}
?>
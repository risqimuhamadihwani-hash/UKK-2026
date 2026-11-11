<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

// CEK ROLE ADMIN / GURU / WALI KELAS
if ($role !== 'admin' && $role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$koneksi = $conn ?? $koneksi ?? null;

if (!$koneksi instanceof mysqli) {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php.");
}

// AMBIL ID DARI URL
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        // Gunakan Prepared Statement untuk menghapus data dengan aman
        $stmt = $koneksi->prepare("DELETE FROM t_pelanggaran_siswa WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: catatan_pelanggaran.php?berhasil=hapus");
        exit;

    } catch (Throwable $e) {
        // Jika ada kendala/trigger dari database yang memblokir, pesan error akan muncul di sini
        die("Gagal menghapus data dari database: " . $e->getMessage());
    }
} else {
    header("Location: catatan_pelanggaran.php");
    exit;
}

<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu7.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    die("ID kategori tidak valid.");
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
);

if (!$stmt) {
    die("Query gagal disiapkan: " . htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $jumlah = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($jumlah > 0) {
        header("Location: menu7.php");
        exit;
    }

    die("Data kategori tidak ditemukan atau sudah terhapus.");
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

die("Gagal menghapus kategori: " . htmlspecialchars($error));
?>

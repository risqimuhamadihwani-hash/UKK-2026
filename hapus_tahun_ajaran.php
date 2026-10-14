```php
<?php
require_once "config/koneksi.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Cek apakah tahun ajaran masih digunakan
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) FROM t_pelanggaran_siswa WHERE tahun_ajaran_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $jumlah);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($jumlah > 0) {
        header("Location: menu4.php?status=masih_digunakan");
        exit;
    }

    // Hapus jika tidak digunakan
    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_tahun_ajaran WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: menu4.php?status=hapus_berhasil");
        exit;
    }

    $error = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    die("Gagal menghapus tahun ajaran: " . htmlspecialchars($error));
}

header("Location: menu4.php");
exit;
?>
```
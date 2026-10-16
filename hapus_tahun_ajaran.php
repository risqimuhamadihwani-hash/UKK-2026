<?php
require_once "config/koneksi.php";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        // Hapus data pelanggaran yang terkait terlebih dahulu
        $stmt = $koneksi->prepare(
            "DELETE FROM t_pelanggaran_siswa
             WHERE tahun_ajaran_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Hapus tahun ajaran
        $stmt = $koneksi->prepare(
            "DELETE FROM t_tahun_ajaran WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: menu4.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        echo "Gagal menghapus: " .
             htmlspecialchars($e->getMessage());
    }
} else {
    header("Location: menu4.php");
    exit;
}
?>
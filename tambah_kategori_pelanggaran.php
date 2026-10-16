
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 0);

    if ($nama === '' || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Nama kategori wajib diisi.";
    } else {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO t_pelanggaran_kategori
             (nama, deskripsi, status_aktif)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt, "ssi", $nama, $deskripsi, $status_aktif
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: menu7.php");
            exit;
        } else {
            $pesan = "Gagal menyimpan kategori: " .
                mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori Pelanggaran</title>
</head>
<body>

<h2>Tambah Kategori Pelanggaran</h2>

<?php if ($pesan !== ''): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Nama Kategori</label><br>
    <input type="text" name="nama" maxlength="100"
           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
           required>

    <br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>

    <br><br>

    <label>Status Aktif</label><br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>

    <br><br>

    <button type="submit" name="simpan">Simpan Kategori</button>
    <a href="menu7.php">Kembali</a>
</form>

</body>
</html>

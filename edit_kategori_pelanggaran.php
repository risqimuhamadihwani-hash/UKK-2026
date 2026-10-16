
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: menu7.php");
    exit;
}

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Kategori pelanggaran tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if ($nama === '' || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $update = mysqli_prepare($koneksi,
            "UPDATE t_pelanggaran_kategori
             SET nama = ?, deskripsi = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update, "ssii", $nama, $deskripsi, $status_aktif, $id
        );

        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            header("Location: menu7.php");
            exit;
        } else {
            $pesan = "Gagal mengubah kategori: " .
                mysqli_stmt_error($update);
        }

        mysqli_stmt_close($update);
    }

    $data['nama'] = $nama;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori Pelanggaran</title>
</head>
<body>

<h2>Edit Kategori Pelanggaran</h2>

<?php if ($pesan !== ''): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Nama Kategori</label><br>
    <input type="text" name="nama" maxlength="100"
           value="<?= htmlspecialchars($data['nama']) ?>"
           required>

    <br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>

    <br><br>

    <label>Status Aktif</label><br>
    <select name="status_aktif" required>
        <option value="1"
            <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
            Aktif
        </option>
        <option value="0"
            <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
            Tidak Aktif
        </option>
    </select>

    <br><br>

    <button type="submit" name="simpan">Simpan Perubahan</button>
    <a href="menu7.php">Kembali</a>
</form>

</body>
</html>

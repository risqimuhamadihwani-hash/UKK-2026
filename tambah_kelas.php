<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $status_aktif = (int) $_POST['status_aktif'];

    $sql = "INSERT INTO t_kelas
            (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssi", $nama, $tingkat, $jurusan, $status_aktif);

    if ($stmt->execute()) {
        header("Location: menu3.php");
        exit;
    } else {
        $pesan = "Gagal menambahkan kelas: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kelas</title>
</head>
<body>

<h1>Tambah Data Kelas</h1>

<?php if ($pesan != "") { ?>
    <p><?= htmlspecialchars($pesan); ?></p>
<?php } ?>

<form method="POST">
    <label>Nama Kelas</label><br>
    <input type="text" name="nama" required>
    <br><br>

    <label>Tingkat</label><br>
    <select name="tingkat" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="X">X</option>
        <option value="XI">XI</option>
        <option value="XII">XII</option>
    </select>
    <br><br>

    <label>Jurusan</label><br>
    <input type="text" name="jurusan" placeholder="Contoh: RPL, TSM, TKJ" required>
    <br><br>

    <label>Status</label><br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>
    <br><br>

    <button type="submit" name="simpan">Simpan Kelas</button>
    <a href="menu3.php">Kembali</a>
</form>

</body>
</html>

<?php
$koneksi->close();
?>
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $sql = "INSERT INTO t_siswa
            (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);

    $stmt->bind_param(
        "ssssssi",
        $nis,
        $nisn,
        $nama,
        $jenis_kelamin,
        $tanggal_lahir,
        $alamat,
        $status_aktif
    );

    if ($stmt->execute()) {
        header("Location: menu1.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Siswa</title>
</head>
<body>

<h1>Tambah Siswa</h1>

<form method="POST">

    NIS:<br>
    <input type="text" name="nis" required>
    <br><br>

    NISN:<br>
    <input type="text" name="nisn">
    <br><br>

    Nama Siswa:<br>
    <input type="text" name="nama" required>
    <br><br>

    Jenis Kelamin:<br>
    <select name="jenis_kelamin" required>
        <option value="">-- Pilih --</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
    </select>
    <br><br>

    Tanggal Lahir:<br>
    <input type="date" name="tanggal_lahir">
    <br><br>

    Alamat:<br>
    <textarea name="alamat"></textarea>
    <br><br>

    Status Aktif:<br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>
    <br><br>

    <button type="submit">Simpan</button>
    <a href="menu1.php">Kembali</a>

</form>

</body>
</html>
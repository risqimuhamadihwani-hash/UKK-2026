<?php
include "config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')
    ");

    header("Location: menu4.php");
    exit;
}
?>

<h1>Tambah Tahun Ajaran</h1>

<form method="post">

    Nama Tahun Ajaran<br>
    <input type="text" name="nama" placeholder="Contoh: 2026/2027" required>

    <br><br>

    Tanggal Mulai<br>
    <input type="date" name="tanggal_mulai" required>

    <br><br>

    Tanggal Selesai<br>
    <input type="date" name="tanggal_selesai" required>

    <br><br>

    Status Aktif<br>
    <select name="status_aktif">
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

    <a href="menu4.php">
        Kembali
    </a>

</form>
<?php
include "config/koneksi.php";

if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    $query = "INSERT INTO t_kelas_siswa
              (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif)
              VALUES
              ('$siswa_id', '$tahun_ajaran_id', '$kelas_id', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')";

    mysqli_query($koneksi, $query);

    header("Location: menu3.php");
    exit;
}
?>

<h1>Tambah Kelas Siswa</h1>

<form method="post">

    <p>
        Siswa ID<br>
        <input type="number" name="siswa_id" required>
    </p>

    <p>
        Tahun Ajaran ID<br>
        <input type="number" name="tahun_ajaran_id" required>
    </p>

    <p>
        Kelas ID<br>
        <input type="number" name="kelas_id" required>
    </p>

    <p>
        Tanggal Mulai<br>
        <input type="date" name="tanggal_mulai" required>
    </p>

    <p>
        Tanggal Selesai<br>
        <input type="date" name="tanggal_selesai">
    </p>

    <p>
        Status Aktif<br>
        <select name="status_aktif">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </p>

    <button type="submit" name="simpan">Simpan</button>

    <a href="menu3.php">Batal</a>

</form>
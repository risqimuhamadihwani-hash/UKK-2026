<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa WHERE id='$id'");
$kelas = mysqli_fetch_assoc($data);

if (!$kelas) {
    die("Data tidak ditemukan");
}

if (isset($_POST['update'])) {

    $siswa_id = $_POST['siswa_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    mysqli_query($koneksi, "UPDATE t_kelas_siswa SET
        siswa_id='$siswa_id',
        tahun_ajaran_id='$tahun_ajaran_id',
        kelas_id='$kelas_id',
        tanggal_mulai='$tanggal_mulai',
        tanggal_selesai='$tanggal_selesai',
        status_aktif='$status_aktif'
        WHERE id='$id'
    ");

    header("Location: menu3.php");
    exit;
}
?>

<h1>Edit Kelas Siswa</h1>

<form method="post">

    Siswa ID<br>
    <input type="number" name="siswa_id"
           value="<?= $kelas['siswa_id']; ?>" required>

    <br><br>

    Tahun Ajaran ID<br>
    <input type="number" name="tahun_ajaran_id"
           value="<?= $kelas['tahun_ajaran_id']; ?>" required>

    <br><br>

    Kelas ID<br>
    <input type="number" name="kelas_id"
           value="<?= $kelas['kelas_id']; ?>" required>

    <br><br>

    Tanggal Mulai<br>
    <input type="date" name="tanggal_mulai"
           value="<?= $kelas['tanggal_mulai']; ?>" required>

    <br><br>

    Tanggal Selesai<br>
    <input type="date" name="tanggal_selesai"
           value="<?= $kelas['tanggal_selesai']; ?>">

    <br><br>

    Status Aktif<br>
    <select name="status_aktif">
        <option value="1"
            <?= $kelas['status_aktif'] == 1 ? 'selected' : ''; ?>>
            Aktif
        </option>

        <option value="0"
            <?= $kelas['status_aktif'] == 0 ? 'selected' : ''; ?>>
            Tidak Aktif
        </option>
    </select>

    <br><br>

    <button type="submit" name="update">Update</button>

    <a href="menu3.php">kembali</a>

</form>
<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran WHERE id='$id'"
);

$tahun = mysqli_fetch_assoc($data);

if (!$tahun) {
    die("Data tahun ajaran tidak ditemukan");
}

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET
        nama='$nama',
        tanggal_mulai='$tanggal_mulai',
        tanggal_selesai='$tanggal_selesai',
        status_aktif='$status_aktif'
        WHERE id='$id'
    ");

    header("Location: menu4.php");
    exit;
}
?>

<h1>Edit Tahun Ajaran</h1>

<form method="post">

    Nama Tahun Ajaran<br>
    <input type="text"
           name="nama"
           value="<?= $tahun['nama']; ?>"
           required>

    <br><br>

    Tanggal Mulai<br>
    <input type="date"
           name="tanggal_mulai"
           value="<?= $tahun['tanggal_mulai']; ?>"
           required>

    <br><br>

    Tanggal Selesai<br>
    <input type="date"
           name="tanggal_selesai"
           value="<?= $tahun['tanggal_selesai']; ?>"
           required>

    <br><br>

    Status Aktif<br>
    <select name="status_aktif">

        <option value="1"
            <?= $tahun['status_aktif'] == 1 ? 'selected' : ''; ?>>
            Aktif
        </option>

        <option value="0"
            <?= $tahun['status_aktif'] == 0 ? 'selected' : ''; ?>>
            Tidak Aktif
        </option>

    </select>

    <br><br>

    <button type="submit" name="update">
        Update
    </button>

    <a href="menu4.php">
        Kembali
    </a>

</form>
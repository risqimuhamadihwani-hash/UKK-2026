<?php
include "config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status_aktif = $_POST['status_aktif'];

    $query = "INSERT INTO t_guru (nip, nama, email, status_aktif)
              VALUES ('$nip', '$nama', '$email', '$status_aktif')";

    mysqli_query($koneksi, $query);

    header("Location: menu2.php");
    exit;
}
?>

<h1>Tambah Guru</h1>

<form method="post">

    <p>
        NIP<br>
        <input type="text" name="nip" required>
    </p>

    <p>
        Nama Guru<br>
        <input type="text" name="nama" required>
    </p>

    <p>
        Email<br>
        <input type="email" name="email">
    </p>

    <p>
        Status<br>
        <select name="status_aktif">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </p>

    <button type="submit" name="simpan">Simpan</button>

    <a href="menu2.php">Kembali</a>

</form>
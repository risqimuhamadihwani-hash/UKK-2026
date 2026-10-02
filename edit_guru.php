<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id='$id'");
$guru = mysqli_fetch_assoc($data);

if (!$guru) {
    die("Data guru tidak ditemukan");
}

if (isset($_POST['update'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status_aktif = $_POST['status_aktif'];

    $query = "UPDATE t_guru SET
                nip='$nip',
                nama='$nama',
                email='$email',
                status_aktif='$status_aktif'
              WHERE id='$id'";

    mysqli_query($koneksi, $query);

    header("Location: menu2.php");
    exit;
}
?>

<h1>Edit Guru</h1>

<form method="post">

    <p>
        NIP<br>
        <input type="text" name="nip"
               value="<?= $guru['nip']; ?>" required>
    </p>

    <p>
        Nama Guru<br>
        <input type="text" name="nama"
               value="<?= $guru['nama']; ?>" required>
    </p>

    <p>
        Email<br>
        <input type="email" name="email"
               value="<?= $guru['email']; ?>">
    </p>

    <p>
        Status<br>
        <select name="status_aktif">

            <option value="1"
                <?= $guru['status_aktif'] == 1 ? 'selected' : ''; ?>>
                Aktif
            </option>

            <option value="0"
                <?= $guru['status_aktif'] == 0 ? 'selected' : ''; ?>>
                Tidak Aktif
            </option>

        </select>
    </p>

    <button type="submit" name="update">Update</button>

    <a href="menu2.php">Batal</a>

</form>
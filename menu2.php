<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_guru");
?>

<h1>Kelola Guru</h1>

<a href="tambah_guru.php">Tambah Guru</a>

<br>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama Guru</th>
        <th>Email</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($guru = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $guru['nip']; ?></td>
        <td><?= $guru['nama']; ?></td>
        <td><?= $guru['email']; ?></td>

        <td>
            <?php
            if ($guru['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }
            ?>
        </td>

        <td>
            <a href="edit_guru.php?id=<?= $guru['id']; ?>">Edit</a>
            |
            <a href="hapus_guru.php?id=<?= $guru['id']; ?>"
               onclick="return confirm('Yakin hapus data guru?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php } ?>

</table>


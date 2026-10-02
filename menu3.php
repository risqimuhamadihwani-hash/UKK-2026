<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa");
?>

<h1>Kelola Kelas Siswa</h1>

<a href="tambah_kelas_siswa.php">Tambah Kelas Siswa</a>

<br>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Siswa ID</th>
        <th>Tahun Ajaran ID</th>
        <th>Kelas ID</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($kelas = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $kelas['siswa_id']; ?></td>
        <td><?= $kelas['tahun_ajaran_id']; ?></td>
        <td><?= $kelas['kelas_id']; ?></td>
        <td><?= $kelas['tanggal_mulai']; ?></td>
        <td><?= $kelas['tanggal_selesai']; ?></td>

        <td>
            <?php
            if ($kelas['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }
            ?>
        </td>

        <td>
            <a href="edit_kelas_siswa.php?id=<?= $kelas['id']; ?>">
                Edit
            </a>
            |
            <a href="hapus_kelas_siswa.php?id=<?= $kelas['id']; ?>"
               onclick="return confirm('Yakin hapus data ini?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php } ?>

</table>


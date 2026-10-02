<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
?>

<h1>Kelola Tahun Ajaran</h1>

<a href="tambah_tahun_ajaran.php">Tambah Tahun Ajaran</a>

<br>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status Aktif</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($tahun = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>

        <td><?= $tahun['nama']; ?></td>

        <td><?= $tahun['tanggal_mulai']; ?></td>

        <td><?= $tahun['tanggal_selesai']; ?></td>

        <td>
            <?php
            if ($tahun['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }
            ?>
        </td>

        <td>
            <a href="edit_tahun_ajaran.php?id=<?= $tahun['id']; ?>">
                Edit
            </a>

            |

            <a href="hapus_tahun_ajaran.php?id=<?= $tahun['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus data ini?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php
    }
    ?>

</table>

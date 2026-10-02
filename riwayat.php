<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Hanya guru yang boleh membuka riwayat
if ($_SESSION['role'] != 'guru') {
    header("Location: dashboard.php");
    exit;
}

include "config/koneksi.php";

/*
    Mengambil riwayat pelanggaran siswa
    berdasarkan:
    t_pelanggaran_siswa
    t_siswa
    t_pelanggaran_kategori
*/

$query = "
    SELECT
        ps.id,
        s.nama AS nama_siswa,
        pk.nama AS nama_pelanggaran,
        ps.tanggal,
        ps.keterangan
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s
        ON ps.siswa_id = s.id
    LEFT JOIN t_pelanggaran_kategori pk
        ON ps.pelanggaran_kategori_id = pk.id
    ORDER BY ps.tanggal DESC, ps.id DESC
";

$data = mysqli_query($koneksi, $query);

if (!$data) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pelanggaran</title>
</head>

<body>

<h2>Riwayat Pelanggaran</h2>

<p>
    Selamat datang,
    <b><?= htmlspecialchars($_SESSION['name']); ?></b>
</p>

<hr>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Siswa</th>
        <th>Pelanggaran</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
    </tr>

    <?php
    $no = 1;

    if (mysqli_num_rows($data) > 0) :

        while ($row = mysqli_fetch_assoc($data)) :
    ?>

        <tr>

            <td>
                <?= $no++; ?>
            </td>

            <td>
                <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
            </td>

            <td>
                <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
            </td>

            <td>
                <?= htmlspecialchars($row['tanggal']); ?>
            </td>

            <td>
                <?= htmlspecialchars($row['keterangan'] ?? '-'); ?>
            </td>

        </tr>

    <?php
        endwhile;

    else :
    ?>

        <tr>
            <td colspan="5">
                Belum ada riwayat pelanggaran.
            </td>
        </tr>

    <?php endif; ?>

</table>

<br>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

</body>
</html>
<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'guru') {
    header("Location: dashboard.php");
    exit;
}

include "config/koneksi.php";

// Ambil rekap pelanggaran siswa
$query = "
    SELECT
        s.id,
        s.nama AS nama_siswa,
        COUNT(ps.id) AS jumlah_pelanggaran
    FROM t_siswa s
    LEFT JOIN t_pelanggaran_siswa ps
        ON s.id = ps.siswa_id
    WHERE s.status_aktif = 1
    GROUP BY s.id, s.nama
    ORDER BY jumlah_pelanggaran DESC, s.nama ASC
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
    <title>Rekap Poin</title>
</head>

<body>

<h2>Rekap Poin Pelanggaran</h2>

<p>
    Selamat datang,
    <b><?= htmlspecialchars($_SESSION['name']); ?></b>
</p>

<hr>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Jumlah Pelanggaran</th>
        <th>Total Poin</th>
    </tr>

    <?php
    $no = 1;

    while ($row = mysqli_fetch_assoc($data)) :
    ?>

    <tr>
        <td><?= $no++; ?></td>

        <td>
            <?= htmlspecialchars($row['nama_siswa']); ?>
        </td>

        <td>
            <?= $row['jumlah_pelanggaran']; ?>
        </td>

        <td>
            <?= $row['jumlah_pelanggaran']; ?>
        </td>
    </tr>

    <?php endwhile; ?>

</table>

<br>

<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>
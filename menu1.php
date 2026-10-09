<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$sql = "
    SELECT
        id,
        nis,
        nisn,
        nama,
        jenis_kelamin,
        tanggal_lahir,
        alamat,
        status_aktif
    FROM t_siswa
    ORDER BY id ASC
";

$result = $koneksi->query($sql);

if (!$result) {
    die("Query gagal: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
</head>
<body>

<h1>Kelola Siswa</h1>

<p>Halaman untuk mengelola data siswa.</p>

<a href="tambah_siswa.php">Tambah Siswa</a>
<br>
<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>NIS</th>
        <th>NISN</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    while ($siswa = $result->fetch_assoc()) {
    ?>
    <tr>
        <td><?= htmlspecialchars($siswa['id']); ?></td>
        <td><?= htmlspecialchars($siswa['nis']); ?></td>
        <td><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($siswa['nama']); ?></td>

        <td>
            <?php
            if ($siswa['jenis_kelamin'] == 'L') {
                echo "Laki-laki";
            } elseif ($siswa['jenis_kelamin'] == 'P') {
                echo "Perempuan";
            } else {
                echo "-";
            }
            ?>
        </td>

        <td><?= htmlspecialchars($siswa['tanggal_lahir'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($siswa['alamat'] ?? '-'); ?></td>

        <td>
            <?= $siswa['status_aktif'] == 1
                ? 'Aktif'
                : 'Tidak Aktif'; ?>
        </td>

        <td>
            <a href="edit_siswa.php?id=<?= $siswa['id']; ?>">
                Edit
            </a>
            |
            <a
                href="hapus_siswa.php?id=<?= $siswa['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus siswa ini?');"
            >
                Hapus
            </a>
        </td>
    </tr>
    <?php
    }

    if ($result->num_rows == 0) {
    ?>
    <tr>
        <td colspan="9">Belum ada data siswa.</td>
    </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
$koneksi->close();
?>
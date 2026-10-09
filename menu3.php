<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$sql = "SELECT id, nama, tingkat, jurusan, status_aktif
        FROM t_kelas
        ORDER BY id ASC";

$result = $koneksi->query($sql);

if (!$result) {
    die("Query gagal: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kelas</title>
</head>
<body>

<h1>Kelola Kelas</h1>

<p>Halaman untuk mengelola data kelas.</p>

<a href="tambah_kelas.php">Tambah Kelas</a>
<br>
<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php while ($kelas = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlspecialchars($kelas['id']); ?></td>
        <td><?= htmlspecialchars($kelas['nama']); ?></td>
        <td><?= htmlspecialchars($kelas['tingkat']); ?></td>
        <td><?= htmlspecialchars($kelas['jurusan']); ?></td>
        <td>
            <?= $kelas['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>
        <td>
            <a href="edit_kelas.php?id=<?= $kelas['id']; ?>">Edit</a>
            |
            <a href="hapus_kelas.php?id=<?= $kelas['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus kelas ini?');">
                Hapus
            </a>
        </td>
    </tr>
    <?php } ?>

    <?php if ($result->num_rows == 0) { ?>
    <tr>
        <td colspan="6">Belum ada data kelas.</td>
    </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
$koneksi->close();
?>
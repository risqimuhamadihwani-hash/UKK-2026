<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$sql = "SELECT id, nip, nama, email, status_aktif
        FROM t_guru
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
    <title>Kelola Guru</title>
</head>
<body>

<h1>Kelola Guru</h1>

<p>Halaman untuk mengelola data guru.</p>

<a href="tambah_guru.php">Tambah Guru</a>
<br>
<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Id</th>
        <th>NIP</th>
        <th>Nama Guru</th>
        <th>Email</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($guru = $result->fetch_assoc()) {
    ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($guru['nip']); ?></td>
        <td><?= htmlspecialchars($guru['nama']); ?></td>
        <td><?= htmlspecialchars($guru['email']); ?></td>
        <td>
            <?= $guru['status_aktif'] == 1
                ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>
        <td>
            <a href="edit_guru.php?id=<?= $guru['id']; ?>">Edit</a>
            |
            <a href="hapus_guru.php?id=<?= $guru['id']; ?>"
               onclick="return confirm('Yakin hapus data guru ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php
    }

    if ($result->num_rows == 0) {
    ?>
    <tr>
        <td colspan="6">Belum ada data guru.</td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
<?php
$koneksi->close();
?>
```

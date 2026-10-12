```php
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY id ASC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tahun Ajaran</title>
</head>
<body>

<h1>Kelola Tahun Ajaran</h1>
<p>Halaman untuk mengelola data tahun ajaran.</p>

<a href="tambah_tahun_ajaran.php">Tambah Tahun Ajaran</a>
<br>
<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Tahun Ajaran</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php while ($data = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $data['id']; ?></td>
        <td><?= htmlspecialchars($data['nama']); ?></td>
        <td><?= $data['tanggal_mulai']; ?></td>
        <td><?= $data['tanggal_selesai']; ?></td>
        <td><?= $data['status_aktif'] ? 'Aktif' : 'Tidak Aktif'; ?></td>
        <td>
            <a href="edit_tahun_ajaran.php?id=<?= $data['id']; ?>">Edit</a> |
            <a href="hapus_tahun_ajaran.php?id=<?= $data['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
```
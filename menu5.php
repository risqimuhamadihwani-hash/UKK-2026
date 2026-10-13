<?php
$conn = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$result = $conn->query(
    "SELECT * FROM t_wali_kelas ORDER BY id DESC"
);
?>

<h2>Kelola Wali Kelas</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Tahun Ajaran ID</th>
            <th>Kelas ID</th>
            <th>Guru ID</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $no = 1; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_ajaran_id']) ?></td>
                    <td><?= htmlspecialchars($row['kelas_id']) ?></td>
                    <td><?= htmlspecialchars($row['guru_id']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_mulai']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_selesai']) ?></td>
                    <td>
                        <?= $row['status_aktif'] == 1 ? 'Aktif' : 'Nonaktif' ?>
                    </td>
                    <td>
                        <a href="menu5.php?edit=<?= (int)$row['id'] ?>">Edit</a>

                        <form method="POST" action="menu5.php"
                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            <input type="hidden" name="hapus_id"
                                   value="<?= (int)$row['id'] ?>">
                            <button type="submit" name="hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <tr>
                <td colspan="9">Data wali kelas belum tersedia.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
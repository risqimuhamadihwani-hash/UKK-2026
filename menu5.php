<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$sql = "
    SELECT
        wk.id,
        ta.tahun AS tahun_ajaran,
        k.nama AS nama_kelas,
        g.nama AS nama_guru,
        wk.tanggal_mulai,
        wk.tanggal_selesai,
        wk.status_aktif
    FROM t_wali_kelas AS wk
    LEFT JOIN t_tahun_ajaran AS ta
        ON wk.tahun_ajaran_id = ta.id
    LEFT JOIN t_kelas AS k
        ON wk.kelas_id = k.id
    LEFT JOIN t_guru AS g
        ON wk.siswa_ajaran_id = g.id
    ORDER BY wk.id ASC
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
    <title>Kelola Wali Kelas</title>
</head>
<body>

<h3>Daftar Wali Kelas</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Guru</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($row = $result->fetch_assoc()) {
    ?>
    <tr>
        <td><?= $no++; ?></td>

        <td>
            <?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['tanggal_mulai'] ?? '-'); ?>
        </td>

        <td>
            <?php
            if (
                empty($row['tanggal_selesai']) ||
                $row['tanggal_selesai'] == '0000-00-00'
            ) {
                echo '-';
            } else {
                echo htmlspecialchars($row['tanggal_selesai']);
            }
            ?>
        </td>

        <td>
            <?= $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>

        <td>
            <a href="edit_wali_kelas.php?id=<?= (int)$row['id']; ?>">
                Edit
            </a>
            |
            <a
                href="hapus_wali_kelas.php?id=<?= (int)$row['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');"
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
        <td colspan="8">Belum ada data wali kelas.</td>
    </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
$koneksi->close();
?>
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

include "config/koneksi.php";

$pesan = "";

// Ambil pesan dari proses simpan
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'berhasil') {
        $pesan = "Penempatan siswa berhasil disimpan.";
    } elseif ($_GET['pesan'] == 'duplikat') {
        $pesan = "Siswa sudah ditempatkan pada tahun ajaran tersebut.";
    } elseif ($_GET['pesan'] == 'tanggal') {
        $pesan = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    } elseif ($_GET['pesan'] == 'gagal') {
        $pesan = "Gagal menyimpan penempatan siswa.";
    }
}

// Data siswa
$data_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Data kelas: sesuai struktur t_kelas
$data_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY tingkat, jurusan, nama"
);

// Data tahun ajaran
$data_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Daftar penempatan
$query = "
    SELECT
        ks.id,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        ta.nama AS nama_tahun,
        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif
    FROM t_kelas_siswa ks
    LEFT JOIN t_siswa s ON ks.siswa_id = s.id
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    ORDER BY ks.id DESC
";

$data_penempatan = mysqli_query($koneksi, $query);

if (!$data_siswa || !$data_kelas || !$data_tahun || !$data_penempatan) {
    die("Query gagal: " . htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penempatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

<h2>Penempatan Siswa</h2>

<p>Selamat datang,
<b><?= htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></b>
</p>

<hr>

<?php if ($pesan != "") : ?>
    <p><?= htmlspecialchars($pesan); ?></p>
<?php endif; ?>

<h3>Form Penempatan Siswa</h3>

<form method="POST" action="proses_penempatan_siswa.php">

    <label>Nama Siswa</label><br>
    <select name="siswa_id" required>
        <option value="">-- Pilih Siswa --</option>
        <?php while ($siswa = mysqli_fetch_assoc($data_siswa)) : ?>
            <option value="<?= (int) $siswa['id']; ?>">
                <?= htmlspecialchars($siswa['nama']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Kelas</label><br>
    <select name="kelas_id" required>
        <option value="">-- Pilih Kelas --</option>
        <?php while ($kelas = mysqli_fetch_assoc($data_kelas)) : ?>
            <option value="<?= (int) $kelas['id']; ?>">
                <?= htmlspecialchars(
                    $kelas['nama'] . ' - ' .
                    $kelas['tingkat'] . ' - ' .
                    $kelas['jurusan']
                ); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tahun Ajaran</label><br>
    <select name="tahun_ajaran_id" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        <?php while ($tahun = mysqli_fetch_assoc($data_tahun)) : ?>
            <option value="<?= (int) $tahun['id']; ?>">
                <?= htmlspecialchars($tahun['nama']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="tanggal_mulai" required>

    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="tanggal_selesai" required>

    <br><br>

    <button type="submit" name="simpan">Simpan</button>

</form>

<hr>

<h3>Daftar Penempatan Siswa</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Tahun Ajaran</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
    </tr>

    <?php $no = 1; ?>

    <?php while ($row = mysqli_fetch_assoc($data_penempatan)) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['tingkat'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['jurusan'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['nama_tahun'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
            <td><?= $row['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>

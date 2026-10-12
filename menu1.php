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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                JusticeAdmin
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu1.php" class= "nav-link active">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu2.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu3.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu4.php" class="nav-link text-white">
                        Kelola Tahun ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu5.php" class="nav-link text-white">
                        Kelola wali kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu6.php" class="nav-link text-white">
                        Penempatan siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu7.php" class="nav-link text-white">
                        kelola Kategori pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="#" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<h1>Kelola Siswa</h1>

<p>Halaman untuk mengelola data siswa.</p>

<a href="tambah_siswa.php">
<button type="button" class="btn btn-secondary">Tambah Siswa + </button>
</a>
<br>

<table class="table">
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
            <button type="button" class="btn btn-warning">Edit</button>
            </a>
            
            <a href="hapus_siswa.php?id=<?= $siswa['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus siswa ini?');"
               <button type="button" class="btn btn-danger">Hapus</button>
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
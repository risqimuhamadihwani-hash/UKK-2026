
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Mengambil data tahun ajaran
$sql = "
    SELECT
        id,
        nama,
        tanggal_mulai,
        tanggal_selesai,
        status_aktif
    FROM t_tahun_ajaran
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tahun Ajaran</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

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
                    <a href="menu1.php" class="nav-link text-white">
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
                    <a href="menu4.php" class="nav-link active">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu5.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu6.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu7.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu8.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
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

            <h1>Kelola Tahun Ajaran</h1>

            <p>Halaman untuk mengelola data tahun ajaran</p>

            <a href="tambah_tahun_ajaran.php"
               class="btn btn-secondary mb-3">
                + Tambah Tahun Ajaran 
            </a>

            <!-- TABEL TAHUN AJARAN -->
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Tahun Ajaran</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($tahun = $result->fetch_assoc()) { ?>

                        <tr>
                            <td>
                                <?= htmlspecialchars($tahun['id']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($tahun['nama']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($tahun['tanggal_mulai']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($tahun['tanggal_selesai']); ?>
                            </td>

                            <td>
                                <?php if ($tahun['status_aktif'] == 1) { ?>

                                        Aktif

                                <?php } else { ?>

    
                                        Tidak Aktif
                                

                                <?php } ?>
                            </td>
            <td>
            <a href="edit_Tahun_ajaran.php?id=<?= $tahun['id']; ?>">
            <button type="button" class="btn btn-warning">Edit</button>
            </a>
            
            <a href="hapus_tahun_ajaran.php?id=<?= $tahun['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus siswa ini?');"
               <button type="button" class="btn btn-danger">Hapus</button>
            </a>
        </td>
                            </td>
                        </tr>

                    <?php } ?>

                    <?php if ($result->num_rows == 0) { ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada data tahun ajaran.
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>
                </table>

            </div>

        </main>
    </div>
</div>

</body>
</html>

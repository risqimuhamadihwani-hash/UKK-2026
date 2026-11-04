<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$sql = "
    SELECT id, nip, nama, email, status_aktif
    FROM t_guru
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
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
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link active">
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
                    <a href="menu9.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu10.php" class="nav-link text-white">
                        Cetak/Export
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

            <h1>Kelola Guru</h1>

            <p>Halaman untuk mengelola data guru</p>

            <div class="mb-3">
                <a href="tambah_guru.php" class="btn btn-secondary">
                    + Tambah Guru
                </a>
            </div>

            <!-- NOTIFIKASI BERHASIL DISIMPAN DI SINI -->
            <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data guru berhasil ditambahkan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">

                <table id="tabelGuru" class="table table-striped table-bordered align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($guru = $result->fetch_assoc()):
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($guru['nip'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($guru['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($guru['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?php if ((int)$guru['status_aktif'] === 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="edit_guru.php?id=<?= (int)$guru['id'] ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="hapus_guru.php?id=<?= (int)$guru['id'] ?>"
                                       onclick="return confirm('Yakin ingin menghapus guru ini?')"
                                       class="btn btn-danger btn-sm">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>
</div>

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelGuru').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        columnDefs: [
            {
                targets: 5,
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data guru",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>

</body>
</html>
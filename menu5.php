<?php
session_start();
include 'config/koneksi.php';
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$data = mysqli_query(
    $koneksi,
    "SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif
    FROM t_kelas_siswa ks
    INNER JOIN t_siswa s ON ks.siswa_id = s.id
    INNER JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    INNER JOIN t_kelas k ON ks.kelas_id = k.id
    ORDER BY s.id ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penempatan Siswa</title>
    
    <link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

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
                    <a href="menu4.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                 <li class="nav-item mb-2">
                    <a href="menu5.php" class="nav-link active">
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
            <h1>Penempatan Siswa</h1>
            <p>Halaman untuk mengelola penempatan siswa ke kelas</p>

            <a href="tambah_penempatan.php" class="btn btn-secondary mb-3">
                + Tambah Penempatan
            </a>

            <div class="table-responsive">
<div class="table-responsive">
    <table id="tabelPenempatan"
           class="table table-hover table-striped table-bordered align-middle">

        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Tahun Ajaran</th>
                <th>Kelas</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($data && mysqli_num_rows($data) > 0): ?>
                <?php $no = 1; ?>

                <?php while ($d = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= htmlspecialchars($d['nis']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['nama_siswa']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['tahun_ajaran']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['nama_kelas']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($d['tanggal_mulai']) ?>
                        </td>

                        <td>
                            <?= $d['tanggal_selesai']
                                ? htmlspecialchars($d['tanggal_selesai'])
                                : '-' ?>
                        </td>

                        <td>
                            <?php if ($d['status_aktif'] == 1): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="edit_penempatan.php?id=<?= (int)$d['id'] ?>"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <a href="hapus_penempatan.php?id=<?= (int)$d['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus data ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- Integrasi DataTables dengan Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabelPenempatan').DataTable({
            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            ordering: true,
            searching: true,
            paging: true,
            info: true,

            columnDefs: [
                {
                    targets: 8,
                    orderable: false,
                    searchable: false
                }
            ],

            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang ditampilkan",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Data penempatan tidak ditemukan",
                emptyTable: "Belum ada data penempatan siswa",

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

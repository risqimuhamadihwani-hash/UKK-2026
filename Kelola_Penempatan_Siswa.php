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
    ORDER BY ks.id ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penempatan Siswa - JusticeAdmin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4 ">
            Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dashboard</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Guru</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Tahun Ajaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Kelola Penempatan Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Wali Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kategori Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Jenis Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Laporan</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">About Me</h7> 
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                    <h7 class="fw-bold mb-4">Logout</h7> 
                    </a>
                </li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h1>Penempatan Siswa</h1>
            <p class="text-muted">Halaman untuk mengelola penempatan siswa ke kelas</p>

            <div class="mb-3">
                <a href="Tambah_Penempatan_Siswa.php" class="btn btn-primary">
                    + Tambah Penempatan
                </a>
                <a href="dashboard.php" class="btn btn-dark">
                     Kembali ke Dashboard
                </a>
            </div>

                <table id="tabelPenempatan" class="table table-hover table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="13%">Tahun Ajaran</th>
                            <th width="10%">Kelas</th>
                            <th width="12%">Tanggal Mulai</th>
                            <th width="12%">Tanggal Selesai</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data && mysqli_num_rows($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($d = mysqli_fetch_assoc($data)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($d['nis']) ?></td>
                                    <td><?= htmlspecialchars($d['nama_siswa']) ?></td>
                                    <td><?= htmlspecialchars($d['tahun_ajaran']) ?></td>
                                    <td><?= htmlspecialchars($d['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($d['tanggal_mulai']) ?></td>
                                    <td>
                                        <?= $d['tanggal_selesai'] ? htmlspecialchars($d['tanggal_selesai']) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ((int)$d['status_aktif'] === 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_penempatan_Siswa.php?id=<?= (int)$d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="hapus_penempatan_Siswa.php?id=<?= (int)$d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </main>
    </div>
</div>

<!-- jQuery & Bootstrap 5 Bundle JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
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
                targets: [0, 7, 8], // Kolom No, Status, dan Aksi dimatikan sorting & searching-nya agar rapi
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
<?php
// INISIASI SESSION & KONEKSI DATABASE
session_start();
require_once __DIR__ . '/config/koneksi.php';

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil statistik penempatan siswa
$countTotalPenempatan = 0;
$countBelumDitempatkan = 0;
$countTahunAktif = 0;

$resTotal = $koneksi->query("SELECT COUNT(*) as total FROM t_kelas_siswa");
if ($resTotal) {
    $countTotalPenempatan = $resTotal->fetch_assoc()['total'];
}

$resBelum = $koneksi->query("SELECT COUNT(*) as total FROM t_siswa s LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id WHERE ks.id IS NULL");
if ($resBelum) {
    $countBelumDitempatkan = $resBelum->fetch_assoc()['total'];
}

$resTahun = $koneksi->query("SELECT COUNT(*) as total FROM t_tahun_ajaran WHERE status_aktif = 1");
if ($resTahun) {
    $countTahunAktif = $resTahun->fetch_assoc()['total'];
}

// QUERY DATA PENEMPATAN SISWA (Status disinkronkan langsung dari t_siswa / Kelola Siswa)
$data = mysqli_query(
    $koneksi,
    "SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        s.status_aktif AS status_aktif
    FROM t_kelas_siswa ks
    INNER JOIN t_siswa s ON ks.siswa_id = s.id
    INNER JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    INNER JOIN t_kelas k ON ks.kelas_id = k.id
    ORDER BY ks.id ASC"
);

if (!$data) {
    die("Query gagal: " . $koneksi->error);
}
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
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
            <h4 class="text-white fw-bold mb-4">
            Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                    <span class="fw-bold">Dashboard</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Guru</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Tahun Ajaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link active">
                    <span class="fw-bold">Kelola Penempatan Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Wali Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Kategori Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Jenis Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                    <span class="fw-bold">Laporan</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                    <span class="fw-bold">About Me</span> 
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                    <span class="fw-bold">Logout</span> 
                    </a>
                </li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1><strong>Kelola Penempatan Siswa</strong></h1> 
            <p>Halaman untuk mengelola penempatan siswa ke kelas</p>

            <!-- KARTU STATISTIK PENEMPATAN SISWA (Minimalis Tanpa Gambar/Ikon) -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Total Penempatan</span>
                        <h2 class="fw-bold mb-0 text-primary"><?= $countTotalPenempatan ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Siswa Belum Ditempatkan</span>
                        <h2 class="fw-bold mb-0 text-success"><?= $countBelumDitempatkan ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Tahun Ajaran Aktif</span>
                        <h2 class="fw-bold mb-0 text-warning"><?= $countTahunAktif ?></h2>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <a href="Tambah_Penempatan_Siswa.php" class="btn btn-primary">
                    + Tambah Penempatan
                </a>
                <a href="dashboard.php" class="btn btn-dark">
                     Kembali ke Dashboard
                </a>
            </div>

            <!-- NOTIFIKASI BERHASIL / DIUBAH / DIHAPUS -->
            <?php if (isset($_GET['pesan'])): ?>
                <?php if ($_GET['pesan'] === 'berhasil'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Siswa Berhasil Ditempatkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['pesan'] === 'diubah' || $_GET['pesan'] === 'edit'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Penempatan Siswa Berhasil Diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['pesan'] === 'dihapus' || $_GET['pesan'] === 'hapus'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Penempatan Siswa Berhasil Dihapus!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="table-responsive">

                <table id="tabelPenempatan" class="table table-hover table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="18%">Tahun Ajaran</th>
                            <th width="12%">Kelas</th>
                            <th width="12%">Status</th>
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
                targets: [0, 5, 6], // Kolom No, Status, Aksi tidak bisa di-sort dan search
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data Penempatan Siswa",
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
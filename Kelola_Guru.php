<?php
session_start();

require_once __DIR__ . '/config/koneksi.php';

// Ambil statistik jumlah guru
$countAktif = 0;
$countWali = 0;
$countTidakAktif = 0;

$resAktif = $koneksi->query("SELECT COUNT(*) as total FROM t_guru WHERE status_aktif = 1");
if ($resAktif) {
    $countAktif = $resAktif->fetch_assoc()['total'];
}

$resWali = $koneksi->query("SELECT COUNT(DISTINCT guru_id) as total FROM t_wali_kelas WHERE status_aktif = 1");
if ($resWali) {
    $countWali = $resWali->fetch_assoc()['total'];
}

$resTidakAktif = $koneksi->query("SELECT COUNT(*) as total FROM t_guru WHERE status_aktif = 0");
if ($resTidakAktif) {
    $countTidakAktif = $resTidakAktif->fetch_assoc()['total'];
}

$sql = "
    SELECT g.id, g.nip, g.nama, g.email, 
           COALESCE(
               (SELECT w.status_aktif FROM t_wali_kelas w WHERE w.guru_id = g.id ORDER BY w.id DESC LIMIT 1), 
               g.status_aktif
           ) AS status_aktif
    FROM t_guru g
    ORDER BY g.id ASC
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
                    <a href="Kelola_Guru.php" class="nav-link active">
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
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
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

            <h1><strong>Kelola Guru</strong></h1> 
            <p>Halaman untuk mengelola data guru</p>

            <!-- KARTU STATISTIK GURU (Gaya Minimalis Tanpa Gambar/Ikon) -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Guru Aktif -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Guru Aktif</span>
                        <h2 class="fw-bold mb-0 text-success"><?= $countAktif ?></h2>
                    </div>
                </div>

                <!-- Card 2: Guru Menjadi Wali Kelas -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Guru Menjadi Wali Kelas</span>
                        <h2 class="fw-bold mb-0 text-primary"><?= $countWali ?></h2>
                    </div>
                </div>

                <!-- Card 3: Guru Tidak Aktif -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3 h-100">
                        <span class="text-muted small mb-1">Guru Tidak Aktif</span>
                        <h2 class="fw-bold mb-0 text-danger"><?= $countTidakAktif ?></h2>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <a href="tambah_guru.php" class="btn btn-primary">
                    + Tambah Guru
                </a>
                <a href="dashboard.php" class="btn btn-dark">
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- NOTIFIKASI BERHASIL / DIUBAH / DIHAPUS -->
            <?php if (isset($_GET['pesan'])): ?>
                <?php if ($_GET['pesan'] === 'berhasil'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Guru Berhasil Ditambahkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['pesan'] === 'diubah' || $_GET['pesan'] === 'edit'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Guru Berhasil Diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['pesan'] === 'dihapus' || $_GET['pesan'] === 'hapus'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Guru Berhasil Dihapus!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
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
                            $bersihNip = str_ireplace('NIP', '', $guru['nip'] ?? '');
                            $bersihEmail = strtolower(trim($guru['email'] ?? ''));
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?= htmlspecialchars($bersihNip, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($guru['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($bersihEmail, ENT_QUOTES, 'UTF-8') ?>
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
            lengthMenu: "Tampilkan _MENU_ data Guru",
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
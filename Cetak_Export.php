<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($koneksi) || $koneksi->connect_error) {
    die("Koneksi database gagal.");
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';
$export = $_GET['export'] ?? '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai)) {
    $tanggal_mulai = '';
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai)) {
    $tanggal_selesai = '';
}

$where = [];
$params = [];
$types = '';

if ($cari !== '') {
    $where[] = "(
        nama_siswa LIKE ?
        OR nama_kelas LIKE ?
        OR nama_pelanggaran LIKE ?
        OR nama_guru LIKE ?
        OR status LIKE ?
    )";

    $keyword = "%{$cari}%";

    for ($i = 0; $i < 5; $i++) {
        $params[] = $keyword;
        $types .= 's';
    }
}

if ($tanggal_mulai !== '') {
    $where[] = "tanggal >= ?";
    $params[] = $tanggal_mulai;
    $types .= 's';
}

if ($tanggal_selesai !== '') {
    $where[] = "tanggal <= ?";
    $params[] = $tanggal_selesai;
    $types .= 's';
}

$sql = "SELECT
            id,
            nama_siswa,
            nama_kelas,
            nama_pelanggaran,
            nama_guru,
            tanggal,
            poin,
            tindakan,
            keterangan,
            status
        FROM t_pelanggaran_siswa";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY tanggal DESC, id DESC";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    die("Query gagal: " . e($koneksi->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
$totalPoin = 0;

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $totalPoin += (int)$row['poin'];
}

$stmt->close();

/* EXPORT CSV */
if ($export === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header(
        'Content-Disposition: attachment; filename="laporan_pelanggaran.csv"'
    );

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, [
        'No',
        'Nama Siswa',
        'Kelas',
        'Jenis Pelanggaran',
        'Guru Pencatat',
        'Tanggal',
        'Poin',
        'Tindakan',
        'Keterangan',
        'Status'
    ], ';');

    foreach ($data as $i => $row) {
        fputcsv($output, [
            $i + 1,
            $row['nama_siswa'],
            $row['nama_kelas'],
            $row['nama_pelanggaran'],
            $row['nama_guru'],
            $row['tanggal'],
            $row['poin'],
            $row['tindakan'],
            $row['keterangan'],
            $row['status']
        ], ';');
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak/Export Laporan - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                Sistem Informasi Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">Kelola Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">Kelola Guru</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">Kelola Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">Kelola Tahun Ajaran</a>
                </li>
                 <li class="nav-item mb-2">
                    <a href="Penempatan_Siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">Kelola Wali Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">Kelola Kategori Pelanggaran</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">Kelola Jenis Pelanggaran</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">Laporan</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link active">Cetak/Export</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2>Cetak/Export Laporan</h2>
            <p class="text-secondary">
                Cetak dan ekspor data pelanggaran siswa.
            </p>

            <hr>

            <!-- RINGKASAN -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-secondary">Total Laporan</h6>
                            <h3 class="fw-bold mb-0"><?= count($data) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-secondary">
                                Total Poin Pelanggaran
                            </h6>
                            <h3 class="fw-bold mb-0"><?= $totalPoin ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTER -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white fw-semibold">
                    Filter Data Laporan
                </div>

                <div class="card-body">
                    <form method="GET" action="Cetak_Export.php">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cari Data</label>
                                <input type="text"
                                       name="cari"
                                       class="form-control"
                                       value="<?= e($cari) ?>"
                                       placeholder="Nama siswa, kelas, guru...">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date"
                                       name="tanggal_mulai"
                                       class="form-control"
                                       value="<?= e($tanggal_mulai) ?>">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date"
                                       name="tanggal_selesai"
                                       class="form-control"
                                       value="<?= e($tanggal_selesai) ?>">
                            </div>

                            <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                <button type="submit"
                                        class="btn btn-primary w-100">
                                    Filter
                                </button>
                            </div>

                        </div>

                        <a href="Cetak_Export.php"
                           class="btn btn-secondary btn-sm">
                            Reset Filter
                        </a>
                    </form>
                </div>
            </div>

            <!-- TOMBOL CETAK DAN EXPORT -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white fw-semibold">
                    Cetak dan Export Data
                </div>

                <div class="card-body">
                    <p class="text-muted small">
                        Data yang dicetak atau diekspor mengikuti filter
                        pencarian dan tanggal yang dipilih.
                    </p>

                    <button type="button"
                            class="btn btn-primary me-2 mb-2"
                            onclick="window.print()">
                        Cetak Laporan
                    </button>

                    <!-- Tombol Export Excel (CSV) dengan warna hijau (btn-success) -->
                    <a href="Cetak_Export.php?<?= e(http_build_query([
                        'cari' => $cari,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                        'export' => 'csv'
                    ])) ?>"
                       class="btn btn-success mb-2">
                        Export Excel (CSV)
                    </a>
                </div>
            </div>

            <!-- TABEL -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Data Pelanggaran Siswa</span>
                    <span class="badge bg-primary fs-6">Total Poin: <?= $totalPoin ?></span>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelLaporan" class="table table-bordered table-striped table-hover align-middle w-100">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Guru Pencatat</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($data)): ?>
                                    <?php foreach ($data as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= e($row['nama_siswa']) ?></td>
                                            <td><?= e($row['nama_kelas']) ?></td>
                                            <td><?= e($row['nama_pelanggaran']) ?></td>
                                            <td><?= e($row['nama_guru']) ?></td>
                                            <td>
                                                <?= !empty($row['tanggal'])
                                                    ? e(date(
                                                        'd-m-Y',
                                                        strtotime($row['tanggal'])
                                                    ))
                                                    : '-' ?>
                                            </td>
                                            <td><span class="fw-bold text-danger"><?= (int)$row['poin'] ?></span></td>
                                            <td><?= e($row['tindakan']) ?></td>
                                            <td><?= e($row['keterangan']) ?></td>
                                            <td>
                                                <?php if (strtolower($row['status']) === 'selesai'): ?>
                                                    <span class="badge bg-success">Selesai</span>
                                                <?php elseif (strtolower($row['status']) === 'diproses'): ?>
                                                    <span class="badge bg-warning text-dark">Diproses</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= e(ucfirst($row['status'])) ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelLaporan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        // Pengaturan DOM agar teks 'Tampilkan data' & kotak pencarian berada rapi di atas tabel
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari data pelanggaran...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data laporan',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        }
    });
});
</script>
</body>
</html>
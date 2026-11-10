<?php
session_start();
require_once 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');
$status = $_GET['status'] ?? '';
$export = $_GET['export'] ?? '';

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

    $keyword = "%$cari%";

    for ($i = 0; $i < 5; $i++) {
        $params[] = $keyword;
        $types .= 's';
    }
}

if ($status !== '') {
    $where[] = "status = ?";
    $params[] = $status;
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
    die("Query laporan gagal: " . e($koneksi->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
$total_poin = 0;

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $total_poin += (int)$row['poin'];
}

$stmt->close();

/* EXPORT CSV */
if ($export === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan_pelanggaran.csv"');

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

$status_tersedia = [];

foreach ($data as $row) {
    if ($row['status'] !== null && $row['status'] !== '') {
        $status_tersedia[$row['status']] = $row['status'];
    }
}

if ($status !== '') {
    $status_tersedia[$status] = $status;
}
?>

<!DOCTYPE html>
<html lang="id">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Siswa - JusticeAdmin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR ADMIN -->
        <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'): ?>
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4">
                Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Dashboard</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Guru</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Tahun Ajaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Penempatan Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Wali Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Kategori Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Jenis Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link active">
                        <span class="fw-bold mb-4">Laporan</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">About Me</span> 
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        <span class="fw-bold mb-4">Logout</span> 
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- SIDEBAR GURU -->
        <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'guru'): ?>
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4">
                Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Dashboard</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Catat Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Tindakan</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Rekap Poin</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Riwayat</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link active">
                        <span class="fw-bold mb-4">Laporan</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">About Me</span> 
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        <span class="fw-bold mb-4">Logout</span> 
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">
                <h3>Laporan Pelanggaran Siswa</h3>
                <p class="text-muted mb-4">Rekap data pelanggaran siswa.</p>
            </div>

            <!-- RINGKASAN LAPORAN -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted">Total Laporan</h6>
                            <h3 class="text-primary fw-bold mb-0"><?= count($data) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted">Total Poin Pelanggaran</h6>
                            <h3 class="text-danger fw-bold mb-0"><?= $total_poin ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM FILTER / PENCARIAN -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="mb-3">Filter Laporan</h5>
                    <form method="GET" action="Laporan.php" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Cari Data:</label>
                            <input type="text" name="cari" class="form-control"
                                   value="<?= e($cari) ?>"
                                   placeholder="Nama siswa, kelas, pelanggaran, guru">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <?php foreach ($status_tersedia as $opsi): ?>
                                    <option value="<?= e($opsi) ?>"
                                        <?= $status === $opsi ? 'selected' : '' ?>>
                                        <?= e(ucfirst($opsi)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Cari / Filter</button>
                            <a href="Laporan.php" class="btn btn-secondary">Reset</a>
                            
                            <button type="button" onclick="window.print()" class="btn btn-success ms-2">Cetak Laporan</button>
                            <a href="?<?= e(http_build_query(array_merge($_GET, ['export' => 'csv']))) ?>" class="btn btn-info text-white">Export CSV</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL DATA LAPORAN -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Data Laporan Pelanggaran</h5>
                    <div class="table-responsive">
                        <table id="tabelLaporan" class="table table-hover table-striped table-bordered align-middle w-100">
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
                                <?php if (count($data) > 0): ?>
                                    <?php foreach ($data as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= e($row['nama_siswa']) ?></td>
                                            <td><?= e($row['nama_kelas']) ?></td>
                                            <td><?= e($row['nama_pelanggaran']) ?></td>
                                            <td><?= e($row['nama_guru']) ?></td>
                                            <td>
                                                <?= !empty($row['tanggal'])
                                                    ? e(date('d-m-Y', strtotime($row['tanggal'])))
                                                    : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= (int)$row['poin'] ?> Poin
                                                </span>
                                            </td>
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
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3 align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data laporan",
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
<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($koneksi) || $koneksi->connect_error) {
    die("Koneksi database gagal.");
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Siswa</title>
    

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
                    <a href="menu9.php" class="nav-link active">
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
<h2>Laporan Pelanggaran Siswa</h2>
<p>Rekap data pelanggaran siswa.</p>

<hr>

<h3>Ringkasan Laporan</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Total Laporan</th>
        <th>Total Poin Pelanggaran</th>
    </tr>
    <tr>
        <td><?= count($data) ?></td>
        <td><?= $total_poin ?></td>
    </tr>
</table>

<br>

<form method="GET" action="menu9.php">
    <label>Cari Data:</label>
    <input type="text" name="cari"
           value="<?= e($cari) ?>"
           placeholder="Nama siswa, kelas, pelanggaran, guru">

    <br><br>

    <label>Tanggal Mulai:</label>
    <input type="date" name="tanggal_mulai"
           value="<?= e($tanggal_mulai) ?>">

    <br><br>

    <label>Tanggal Selesai:</label>
    <input type="date" name="tanggal_selesai"
           value="<?= e($tanggal_selesai) ?>">

    <br><br>

    <label>Status:</label>
    <select name="status">
        <option value="">Semua Status</option>

        <?php foreach ($status_tersedia as $opsi): ?>
            <option value="<?= e($opsi) ?>"
                <?= $status === $opsi ? 'selected' : '' ?>>
                <?= e(ucfirst($opsi)) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <button type="submit">Cari / Filter</button>
    <a href="menu9.php">Reset</a>
</form>

<br>

<button type="button" onclick="window.print()">Cetak Laporan</button>

<a href="?<?= e(http_build_query(array_merge($_GET, [
    'export' => 'csv'
]))) ?>">
    <button type="button">Export CSV</button>
</a>

<br><br>

<h3>Data Laporan</h3>
<p>Jumlah data ditemukan: <?= count($data) ?></p>


<table id="tabelLaporan" class="table table-bordered table-striped table-hover">
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
                    <td><?= (int)$row['poin'] ?></td>
                    <td><?= e($row['tindakan']) ?></td>
                    <td><?= e($row['keterangan']) ?></td>
                    <td><?= e($row['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">Tidak ada data laporan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    if ($('#tabelLaporan tbody tr td[colspan]').length === 0) {
        new DataTable('#tabelLaporan', {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            ordering: true,
            searching: true,
            paging: true,
            info: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
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
    }
});
</script>
</body>
</html>
<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

// VALIDASI ROLE (Izinkan admin, guru, dan wali_kelas)
if ($role !== 'admin' && $role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika role wali kelas, samakan perlakuannya seperti guru untuk filter data
if ($role === 'wali_kelas' || $role === 'walikelas') {
    $role = 'guru';
}

// Tahun ajaran berlaku untuk admin maupun guru.
$tahunList = $koneksi->query("SELECT id, nama, status_aktif FROM t_tahun_ajaran ORDER BY tanggal_mulai DESC, id DESC");
$tahunAktif = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC LIMIT 1")->fetch_assoc();
$tahun_id = (int)($_GET['tahun_ajaran_id'] ?? ($tahunAktif['id'] ?? 0));
if ($tahun_id < 1) {
    $fallbackTahun = $koneksi->query("SELECT id FROM t_tahun_ajaran ORDER BY id DESC LIMIT 1")->fetch_assoc();
    $tahun_id = (int)($fallbackTahun['id'] ?? 0);
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');
$status = $_GET['status'] ?? '';
$tanggal = trim($_GET['tanggal'] ?? ''); 
$export = $_GET['export'] ?? '';

// Ambil ID Guru jika role adalah guru
$guru_id = 0;
$kelas_id_wali_arr = [];

if ($role === 'guru') {
    $session_user_id = (int)$_SESSION['id_user'];

    // Cek data guru berdasarkan user_id atau id yang sama dengan session login
    $stmtGuru = $koneksi->prepare("SELECT id FROM t_guru WHERE user_id = ? OR id = ? LIMIT 1");
    $stmtGuru->bind_param("ii", $session_user_id, $session_user_id);
    $stmtGuru->execute();
    $dataGuru = $stmtGuru->get_result()->fetch_assoc();
    $stmtGuru->close();

    if ($dataGuru) {
        $guru_id = (int)$dataGuru['id'];
    }

    // Jika masih 0, ambil data guru pertama atau sesuaikan dengan relasi tabel
    if ($guru_id === 0) {
        $resFB = $koneksi->query("SELECT id FROM t_guru ORDER BY id ASC LIMIT 1");
        if ($rowFB = $resFB->fetch_assoc()) {
            $guru_id = (int)$rowFB['id'];
        }
    }

    // Ambil SEMUA ID Kelas yang diampu oleh guru ini sebagai Wali Kelas pada tahun ajaran aktif
    if ($guru_id > 0) {
        $stmtWali = $koneksi->prepare(
            "SELECT kelas_id FROM t_wali_kelas 
             WHERE guru_id = ? AND tahun_ajaran_id = ? AND status_aktif = 1"
        );
        $stmtWali->bind_param("ii", $guru_id, $tahun_id);
        $stmtWali->execute();
        $resWali = $stmtWali->get_result();
        
        while ($rowWali = $resWali->fetch_assoc()) {
            $kelas_id_wali_arr[] = (int)$rowWali['kelas_id'];
        }
        $stmtWali->close();
    }
}

$where = ["ps.tahun_ajaran_id = ?"];
$params = [$tahun_id];
$types = 'i';

// Aturan filter: Jika guru adalah wali kelas di suatu kelas, tampilkan siswa di kelas tersebut. 
// Jika bukan wali kelas, tampilkan data pelanggaran yang dicatat oleh guru tersebut.
if ($role === 'guru') {
    if (!empty($kelas_id_wali_arr)) {
        $placeholders = implode(',', array_fill(0, count($kelas_id_wali_arr), '?'));
        $where[] = "(ps.siswa_id IN (
            SELECT ks.siswa_id FROM t_kelas_siswa ks
            WHERE ks.kelas_id IN ($placeholders) AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
        ) OR ps.guru_id = ?)";
        
        foreach ($kelas_id_wali_arr as $kid) {
            $params[] = $kid;
            $types .= 'i';
        }
        $params[] = $tahun_id;
        $types .= 'i';
        
        $params[] = $guru_id;
        $types .= 'i';
    } else {
        $where[] = "ps.guru_id = ?";
        $params[] = $guru_id;
        $types .= 'i';
    }
}

if ($cari !== '') {
    $where[] = "(
        s.nama LIKE ?
        OR k.nama LIKE ?
        OR p.nama LIKE ?
        OR ps.status LIKE ?
    )";

    $keyword = "%$cari%";

    for ($i = 0; $i < 4; $i++) {
        $params[] = $keyword;
        $types .= 's';
    }
}

if ($status !== '') {
    $where[] = "ps.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($tanggal !== '') {
    $where[] = "ps.tanggal = ?";
    $params[] = $tanggal;
    $types .= 's';
}

// QUERY MENGGUNAKAN JOIN AGAR NAMA KELAS & JENIS PELANGGARAN MUNCUL DENGAN BENAR
$sql = "SELECT
            ps.id,
            s.nama AS nama_siswa,
            COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas,
            COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran,
            g.nama AS nama_guru,
            ps.tanggal,
            ps.poin,
            ps.tindakan,
            ps.keterangan,
            ps.status
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s ON s.id = ps.siswa_id
        LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = ps.siswa_id AND ks.tahun_ajaran_id = ps.tahun_ajaran_id AND ks.status_aktif = 1
        LEFT JOIN t_kelas k ON k.id = ks.kelas_id
        LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
        LEFT JOIN t_guru g ON g.id = ps.guru_id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " GROUP BY ps.id ORDER BY ps.tanggal DESC, ps.id DESC";

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

/* EXPORT EXCEL (CSV) */
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
        'Catatan',
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
    
    <style>
        /* PENGATURAN KHUSUS CETAK LAPORAN (LANDSCAPE AGAR TIDAK TERPOTONG) */
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            /* Sembunyikan Sidebar, Form Filter, Tombol Aksi, dan Elemen Pendukung Lainnya */
            .col-md-3, .col-lg-2, 
            .card:has(form), 
            .btn, 
            .row.mb-4,
            .dataTables_length, 
            .dataTables_filter, 
            .dataTables_info, 
            .dataTables_paginate, 
            hr, 
            nav {
                display: none !important;
            }

            /* Perlebar area konten utama */
            main, .col-md-9, .col-lg-10 {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                font-size: 10pt;
            }

            /* Styling tabel bersih saat dicetak */
            table.table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table.table th, table.table td {
                background-color: transparent !important;
                color: #000000 !important;
                border: 1px solid #444 !important;
                padding: 6px 8px !important;
            }

            thead.table-dark th {
                background-color: #212529 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR DINAMIS BERDASARKAN ROLE -->
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

                <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                    <li class="nav-item mb-2"><a href="Kelola_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Siswa</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Guru.php" class="nav-link text-white"><span class="fw-bold">Kelola Guru</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Kelas.php" class="nav-link text-white"><span class="fw-bold">Kelola Kelas</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Tahun Ajaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Penempatan Siswa</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Wali_Kelas.php" class="nav-link text-white"><span class="fw-bold">Kelola Wali Kelas</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Kategori Pelanggaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Jenis Pelanggaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link active"><span class="fw-bold">Laporan</span></a></li>
                    <li class="nav-item mb-2"><a href="about_me.php" class="nav-link text-white"><span class="fw-bold">About Me</span></a></li>
                <?php else: ?>
                    <li class="nav-item mb-2"><a href="catatan_pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Catatan Pelanggaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Tindakan.php" class="nav-link text-white"><span class="fw-bold">Tindakan</span></a></li>
                    <li class="nav-item mb-2"><a href="rekap_poin.php" class="nav-link text-white"><span class="fw-bold">Rekap Poin</span></a></li>
                    <li class="nav-item mb-2"><a href="riwayat.php" class="nav-link text-white"><span class="fw-bold">Riwayat</span></a></li>
                    <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link active"><span class="fw-bold">Laporan</span></a></li>
                <?php endif; ?>

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
                        <div class="col-md-3">
                            <label class="form-label">Tahun Ajaran:</label>
                            <select name="tahun_ajaran_id" class="form-select">
                                <?php while ($th = $tahunList->fetch_assoc()): ?>
                                    <option value="<?= (int)$th['id'] ?>" <?= $tahun_id === (int)$th['id'] ? 'selected' : '' ?>>
                                        <?= e($th['nama']) ?><?= (int)$th['status_aktif'] === 1 ? ' (Aktif)' : '' ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari Data:</label>
                            <input type="text" name="cari" class="form-control"
                                   value="<?= e($cari) ?>"
                                   placeholder="Nama siswa, kelas, pelanggaran">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal:</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= e($tanggal) ?>">
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
                            
                            <button type="button" onclick="window.print()" class="btn btn-primary ms-2">Cetak Laporan</button>
                            <a href="?<?= e(http_build_query(array_merge($_GET, ['export' => 'csv']))) ?>" class="btn btn-success text-white">Export Excel</a>
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
                                    <th>Catatan</th>
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
<script src="https://cdn.jsdelivr.net/npm/datatables.net/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

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
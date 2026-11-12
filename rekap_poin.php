<?php
session_start();
require_once 'config/koneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
if ($role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    header("Location: dashboard.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');
$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

// Validasi tanggal agar filter hanya menerima format YYYY-MM-DD.
foreach (['tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai] as $namaField => $nilaiTanggal) {
    if ($nilaiTanggal !== '') {
        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $nilaiTanggal);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $nilaiTanggal) {
            die('Format tanggal tidak valid.');
        }
    }
}
if ($tanggal_mulai !== '' && $tanggal_selesai !== '' && $tanggal_selesai < $tanggal_mulai) {
    die('Tanggal selesai tidak boleh sebelum tanggal mulai.');
}

$sql = "
    SELECT
        p.siswa_id,
        p.nama_siswa,
        COALESCE(k.nama, p.nama_kelas, 'Tanpa Kelas') AS nama_kelas,
        COUNT(*) AS jumlah_pelanggaran,
        SUM(p.poin) AS total_poin
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_kelas_siswa ks
        ON ks.siswa_id = p.siswa_id
        AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON k.id = COALESCE(p.kelas_id, ks.kelas_id)
    WHERE 1=1
";
$params = [];
$types = '';

if ($cari !== '') {
    $sql .= " AND (p.nama_siswa LIKE ? OR k.nama LIKE ? OR p.nama_kelas LIKE ?)";
    $keyword = "%$cari%";
    array_push($params, $keyword, $keyword, $keyword);
    $types .= 'sss';
}
if ($tanggal_mulai !== '') {
    $sql .= " AND p.tanggal >= ?";
    $params[] = $tanggal_mulai;
    $types .= 's';
}
if ($tanggal_selesai !== '') {
    $sql .= " AND p.tanggal <= ?";
    $params[] = $tanggal_selesai;
    $types .= 's';
}

$sql .= " GROUP BY p.siswa_id, p.nama_siswa, nama_kelas ORDER BY total_poin DESC, p.nama_siswa ASC";
$stmt = $koneksi->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Poin Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

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
                    <a href="catatan_pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold">Catatan Pelanggaran</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        <span class="fw-bold">Tindakan</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link active">
                        <span class="fw-bold">Rekap Poin</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
                        <span class="fw-bold">Riwayat</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        <span class="fw-bold">Laporan</span>
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

            <div class="mb-4">
                <h3>Rekap Poin Siswa</h3>
                <p class="text-muted">
                    Rekapitulasi jumlah pelanggaran dan total poin setiap siswa.
                </p>
            </div>

            <!-- PENCARIAN -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Cari Siswa / Kelas</label>
                            <input type="text" name="cari" class="form-control"
                                   placeholder="Cari nama siswa atau kelas..." value="<?= e($cari) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control"
                                   value="<?= e($tanggal_mulai) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control"
                                   value="<?= e($tanggal_selesai) ?>">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="rekap_poin.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL REKAP -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-2">Data Rekap Poin Siswa</h5>
                    <p class="text-muted small mb-3">
                        <?php if ($tanggal_mulai !== '' || $tanggal_selesai !== ''): ?>
                            Periode: <?= $tanggal_mulai !== '' ? e(date('d-m-Y', strtotime($tanggal_mulai))) : 'Awal data' ?>
                            sampai <?= $tanggal_selesai !== '' ? e(date('d-m-Y', strtotime($tanggal_selesai))) : 'Sekarang' ?>
                        <?php else: ?>
                            Menampilkan seluruh periode data.
                        <?php endif; ?>
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>ID Siswa</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jumlah Pelanggaran</th>
                                    <th>Total Poin</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= e($row['siswa_id']) ?></td>
                                        <td><?= e($row['nama_siswa']) ?></td>
                                        <td><?= e($row['nama_kelas']) ?></td>
                                        <td><?= e($row['jumlah_pelanggaran']) ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= e($row['total_poin']) ?> Poin
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Data rekap poin tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
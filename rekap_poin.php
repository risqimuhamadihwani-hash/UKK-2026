<?php
session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE GURU
if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$cari = trim($_GET['cari'] ?? '');

// QUERY REKAP POIN DENGAN JOIN KELAS YANG AKURAT
$sql = "
    SELECT
        p.siswa_id,
        p.nama_siswa,
        COALESCE(k.nama, p.nama_kelas, 'Tanpa Kelas') AS nama_kelas,
        COUNT(*) AS jumlah_pelanggaran,
        SUM(p.poin) AS total_poin
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = p.siswa_id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON k.id = ks.kelas_id
    WHERE 1=1
";

$params = [];
$types = '';

if ($cari !== '') {
    $sql .= " AND (
        p.nama_siswa LIKE ?
        OR k.nama LIKE ?
        OR p.nama_kelas LIKE ?
    )";

    $keyword = "%$cari%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "sss";
}

$sql .= "
    GROUP BY p.siswa_id, p.nama_siswa, nama_kelas
    ORDER BY total_poin DESC, p.nama_siswa ASC
";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query rekap poin gagal: " . e(mysqli_error($koneksi)));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

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
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold">Catat Pelanggaran</span>
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
                    <form method="GET" class="row g-2">
                        <div class="col-md-9">
                            <input
                                type="text"
                                name="cari"
                                class="form-control"
                                placeholder="Cari nama siswa atau kelas..."
                                value="<?= e($cari) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                Cari
                            </button>

                            <a href="rekap_poin.php" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL REKAP -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Data Rekap Poin Siswa</h5>

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
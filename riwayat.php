<?php
session_start();
require_once 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE
$role = strtolower(str_replace([' ', '_'], '', $_SESSION['role'] ?? ''));

if (!in_array($role, ['guru', 'walikelas'])) {
    header("Location: dashboard.php");
    exit;
}

// FUNGSI ESCAPE
function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// FUNGSI BIND PARAMETER
function bindParams($stmt, $types, &$params) {
    if ($types !== '') {
        $bind = [$types];

        foreach ($params as $key => &$value) {
            $bind[] = &$value;
        }

        call_user_func_array([$stmt, 'bind_param'], $bind);
    }
}

// FILTER PENCARIAN
$cari = trim($_GET['cari'] ?? '');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';
$status = trim($_GET['status'] ?? '');

// VALIDASI TANGGAL
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai) && $tanggal_mulai !== '') {
    $tanggal_mulai = '';
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai) && $tanggal_selesai !== '') {
    $tanggal_selesai = '';
}

// QUERY FILTER
$where = " WHERE 1=1 ";
$params = [];
$types = '';

// FILTER KHUSUS WALI KELAS
if ($role === 'walikelas') {

    $where .= "
        AND ps.kelas_id IN (
            SELECT wk.kelas_id
            FROM t_wali_kelas wk
            INNER JOIN t_guru g ON g.id = wk.guru_id
            WHERE g.user_id = ?
            AND g.status_aktif = 1
            AND wk.status_aktif = 1
        )
    ";

    $params[] = (int) $_SESSION['id_user'];
    $types .= 'i';
}

// PENCARIAN
if ($cari !== '') {

    $where .= "
        AND (
            ps.nama_siswa LIKE ?
            OR ps.nama_kelas LIKE ?
            OR ps.nama_pelanggaran LIKE ?
            OR ps.nama_guru LIKE ?
            OR ps.keterangan LIKE ?
            OR ps.tindakan LIKE ?
        )
    ";

    $keyword = "%$cari%";

    for ($i = 0; $i < 6; $i++) {
        $params[] = $keyword;
        $types .= 's';
    }
}

// FILTER TANGGAL MULAI
if ($tanggal_mulai !== '') {
    $where .= " AND ps.tanggal >= ? ";
    $params[] = $tanggal_mulai;
    $types .= 's';
}

// FILTER TANGGAL SELESAI
if ($tanggal_selesai !== '') {
    $where .= " AND ps.tanggal <= ? ";
    $params[] = $tanggal_selesai;
    $types .= 's';
}

// FILTER STATUS
if ($status !== '') {
    $where .= " AND ps.status = ? ";
    $params[] = $status;
    $types .= 's';
}

// QUERY DATA RIWAYAT
$sql = "
    SELECT
        ps.id,
        ps.siswa_id,
        ps.nama_siswa,
        ps.nama_kelas,
        ps.nama_pelanggaran,
        ps.nama_guru,
        ps.tanggal,
        ps.keterangan,
        ps.poin,
        ps.tindakan,
        ps.status
    FROM t_pelanggaran_siswa ps
    $where
    ORDER BY ps.tanggal DESC, ps.id DESC
";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query riwayat gagal: " . e(mysqli_error($koneksi)));
}

bindParams($stmt, $types, $params);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// QUERY RINGKASAN
$sql_total = "
    SELECT
        COUNT(*) AS jumlah_pelanggaran,
        COUNT(DISTINCT ps.siswa_id) AS jumlah_siswa,
        COALESCE(SUM(ps.poin), 0) AS total_poin
    FROM t_pelanggaran_siswa ps
    $where
";

$stmt_total = mysqli_prepare($koneksi, $sql_total);

if (!$stmt_total) {
    die("Query ringkasan gagal: " . e(mysqli_error($koneksi)));
}

bindParams($stmt_total, $types, $params);

mysqli_stmt_execute($stmt_total);
$result_total = mysqli_stmt_get_result($stmt_total);

$ringkasan = mysqli_fetch_assoc($result_total);

// DATA RINGKASAN
$jumlah_pelanggaran = $ringkasan['jumlah_pelanggaran'];
$jumlah_siswa = $ringkasan['jumlah_siswa'];
$total_poin = $ringkasan['total_poin'];

// AMBIL DAFTAR STATUS DARI DATABASE
$sql_status = "SELECT DISTINCT status FROM t_pelanggaran_siswa ORDER BY status ASC";
$result_status = mysqli_query($koneksi, $sql_status);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white fw-bold mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dasboard</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Catat Pelanggaran</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Tindakan</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Rekap Poin</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Riwayat</h7> 
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
            <div class="mb-4">

                <h3>Riwayat Pelanggaran</h3>

                <p class="text-muted">
                    Menampilkan seluruh riwayat pencatatan pelanggaran siswa.
                </p>

            </div>

            <!-- RINGKASAN -->
            <div class="row mb-4">

                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Jumlah Pelanggaran
                            </h6>

                            <h3><?= e($jumlah_pelanggaran) ?></h3>

                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Jumlah Siswa
                            </h6>

                            <h3><?= e($jumlah_siswa) ?></h3>

                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Poin
                            </h6>

                            <h3><?= e($total_poin) ?></h3>

                        </div>
                    </div>
                </div>

            </div>

            <!-- FILTER -->
            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="mb-3">Filter Riwayat</h5>

                    <form method="GET" class="row g-3">

                        <div class="col-md-4">

                            <label class="form-label">
                                Pencarian
                            </label>

                            <input
                                type="text"
                                name="cari"
                                class="form-control"
                                placeholder="Cari nama siswa, kelas, pelanggaran..."
                                value="<?= e($cari) ?>"
                            >

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="<?= e($tanggal_mulai) ?>"
                            >

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="<?= e($tanggal_selesai) ?>"
                            >

                        </div>

                        <div class="col-md-2">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status" class="form-select">

                                <option value="">Semua</option>

                                <?php while ($st = mysqli_fetch_assoc($result_status)): ?>

                                    <option
                                        value="<?= e($st['status']) ?>"
                                        <?= $status === $st['status'] ? 'selected' : '' ?>
                                    >
                                        <?= e(ucfirst($st['status'])) ?>
                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <div class="col-12">

                            <button type="submit" class="btn btn-primary">
                                Cari
                            </button>

                            <a href="riwayat.php" class="btn btn-secondary">
                                Reset
                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <!-- TABEL RIWAYAT -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="mb-3">
                        Data Riwayat Pelanggaran
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Guru</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                            <?php if (mysqli_num_rows($result) > 0): ?>

                                <?php $no = 1; ?>

                                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                    <tr>

                                        <td><?= $no++ ?></td>

                                        <td>
                                            <?= e($row['tanggal']) ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_siswa']) ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_kelas']) ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_pelanggaran']) ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-primary">
                                                <?= e($row['poin']) ?> Poin
                                            </span>
                                        </td>

                                        <td>
                                            <?= e($row['nama_guru']) ?>
                                        </td>

                                        <td>
                                            <?= e($row['keterangan']) ?>
                                        </td>

                                        <td>
                                            <?= e($row['tindakan']) ?>
                                        </td>

                                        <td>
                                            <?php
                                            $warna = 'secondary';

                                            if (strtolower($row['status']) === 'selesai') {
                                                $warna = 'success';
                                            } elseif (strtolower($row['status']) === 'diproses') {
                                                $warna = 'warning';
                                            } elseif (strtolower($row['status']) === 'tercatat') {
                                                $warna = 'primary';
                                            }
                                            ?>

                                            <span class="badge bg-<?= $warna ?>">
                                                <?= e(ucfirst($row['status'])) ?>
                                            </span>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="10" class="text-center">
                                        Data riwayat pelanggaran tidak ditemukan.
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

</body>
</html>

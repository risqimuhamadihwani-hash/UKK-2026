<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE
$role = strtolower(str_replace([' ', '_'], '', $_SESSION['role'] ?? ''));

if (!in_array($role, ['guru', 'walikelas', 'admin'])) {
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
$status = trim($_GET['status'] ?? '');

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
            OR k.nama LIKE ?
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

// FILTER STATUS
if ($status !== '') {
    $where .= " AND ps.status = ? ";
    $params[] = $status;
    $types .= 's';
}

// QUERY DATA RIWAYAT DENGAN JOIN KELAS YANG AKURAT
$sql = "
    SELECT
        ps.id,
        ps.siswa_id,
        ps.nama_siswa,
        COALESCE(k.nama, ps.nama_kelas, 'Tanpa Kelas') AS nama_kelas,
        ps.nama_pelanggaran,
        ps.nama_guru,
        ps.tanggal,
        ps.keterangan,
        ps.poin,
        ps.tindakan,
        ps.status
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = ps.siswa_id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON k.id = ks.kelas_id
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
                    <a href="Catatan_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold">Catatan Pelanggaran</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        <span class="fw-bold">Tindakan</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        <span class="fw-bold">Rekap Poin</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link active">
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
                <h3>Riwayat Pelanggaran</h3>
                <p class="text-muted">
                    Menampilkan seluruh riwayat pencatatan pelanggaran siswa.
                </p>
            </div>

            <!-- FILTER -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="mb-3">Filter Riwayat</h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Pencarian</label>
                            <input
                                type="text"
                                name="cari"
                                class="form-control"
                                placeholder="Cari nama siswa, kelas, pelanggaran..."
                                value="<?= e($cari) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
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
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Data Riwayat Pelanggaran</h5>

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
                                    <th>Guru Pencatat</th>
                                    <th>Catatan</th>
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
                                        <td><?= e($row['tanggal']) ?></td>
                                        <td><?= e($row['nama_siswa']) ?></td>
                                        <td><?= e($row['nama_kelas']) ?></td>
                                        <td><?= e($row['nama_pelanggaran']) ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= e($row['poin']) ?> Poin
                                            </span>
                                        </td>
                                        <td><?= e($row['nama_guru']) ?></td>
                                        <td><?= e($row['keterangan']) ?></td>
                                        <td><?= e($row['tindakan']) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
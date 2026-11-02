
<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$pesan = '';
$error = '';

// PROSES SIMPAN TINDAKAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_tindakan'])) {

    $id = (int) ($_POST['id'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $status_valid = ['tercatat', 'diproses', 'selesai'];

    if ($id <= 0 || $tindakan === '' || !in_array($status, $status_valid, true)) {
        $error = "Data tindakan tidak valid.";
    } else {
        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran_siswa
            SET tindakan = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");

        mysqli_stmt_bind_param($stmt, "ssi", $tindakan, $status, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: tindakan.php?pesan=berhasil");
            exit;
        } else {
            $error = "Gagal menyimpan tindakan.";
        }

        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil') {
    $pesan = "Tindakan berhasil diperbarui.";
}

// PENCARIAN
$cari = trim($_GET['cari'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$sql = "
    SELECT
        id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        nama_guru,
        tanggal,
        poin,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    WHERE 1=1
";

$params = [];
$types = '';

if ($cari !== '') {
    $sql .= " AND (
        nama_siswa LIKE ?
        OR nama_kelas LIKE ?
        OR nama_pelanggaran LIKE ?
        OR nama_guru LIKE ?
    )";

    $keyword = "%$cari%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "ssss";
}

if (in_array($status_filter, ['tercatat', 'diproses', 'selesai'], true)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($koneksi, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// HITUNG DATA
$total = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM t_pelanggaran_siswa"
))['jumlah'];

$tercatat = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM t_pelanggaran_siswa WHERE status='tercatat'"
))['jumlah'];

$diproses = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM t_pelanggaran_siswa WHERE status='diproses'"
))['jumlah'];

$selesai = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS jumlah FROM t_pelanggaran_siswa WHERE status='selesai'"
))['jumlah'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tindakan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                        Catat pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link active">
                        Tindakan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu4guru.php" class="nav-link text-white">
                        Riwayat
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
<div class="container-fluid p-4">

    <div class="mb-4">
        <h3>Kelola Tindakan</h3>
        <p class="text-muted">
            Kelola tindakan dan status penanganan pelanggaran siswa.
        </p>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-success">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- RINGKASAN -->
    <div class="row mb-4">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Pelanggaran</h6>
                    <h3><?= e($total) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Tercatat</h6>
                    <h3><?= e($tercatat) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Diproses</h6>
                    <h3><?= e($diproses) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Selesai</h6>
                    <h3><?= e($selesai) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- PENCARIAN -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" class="row g-2">

                <div class="col-md-6">
                    <input
                        type="text"
                        name="cari"
                        class="form-control"
                        placeholder="Cari nama siswa, kelas, pelanggaran..."
                        value="<?= e($cari) ?>"
                    >
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tercatat" <?= $status_filter === 'tercatat' ? 'selected' : '' ?>>Tercatat</option>
                        <option value="diproses" <?= $status_filter === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="tindakan.php" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>
    </div>

    <!-- TABEL TINDAKAN -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Data Tindakan Pelanggaran</h5>

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Guru</th>
                            <th>Tanggal</th>
                            <th>Poin</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td><?= e($row['nama_siswa']) ?></td>

                                <td><?= e($row['nama_kelas']) ?></td>

                                <td><?= e($row['nama_pelanggaran']) ?></td>

                                <td><?= e($row['nama_guru']) ?></td>

                                <td>
                                    <?= $row['tanggal'] ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?>
                                </td>

                                <td><?= e($row['poin']) ?></td>

                                <form method="POST">

                                    <td>
                                        <input
                                            type="text"
                                            name="tindakan"
                                            class="form-control"
                                            value="<?= e($row['tindakan']) ?>"
                                            required
                                        >
                                    </td>

                                    <td>
                                        <select name="status" class="form-select" required>
                                            <option value="tercatat" <?= $row['status'] === 'tercatat' ? 'selected' : '' ?>>
                                                Tercatat
                                            </option>

                                            <option value="diproses" <?= $row['status'] === 'diproses' ? 'selected' : '' ?>>
                                                Diproses
                                            </option>

                                            <option value="selesai" <?= $row['status'] === 'selesai' ? 'selected' : '' ?>>
                                                Selesai
                                            </option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">

                                        <button
                                            type="submit"
                                            name="simpan_tindakan"
                                            class="btn btn-success btn-sm"
                                        >
                                            Simpan
                                        </button>
                                    </td>

                                </form>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="10" class="text-center">
                                Data tindakan tidak ditemukan.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>

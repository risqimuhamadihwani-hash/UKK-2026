
<?php
session_start();
require_once 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE GURU
$role = strtolower($_SESSION['role'] ?? '');

if ($role !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

$name = $_SESSION['name'] ?? 'Guru';

// RINGKASAN PELANGGARAN
$query_ringkasan = mysqli_query($koneksi, "
    SELECT
        COUNT(*) AS total_pelanggaran,
        SUM(CASE
            WHEN LOWER(status) = 'tercatat' THEN 1
            ELSE 0
        END) AS tercatat,
        SUM(CASE
            WHEN LOWER(status) = 'diproses' THEN 1
            ELSE 0
        END) AS diproses,
        SUM(CASE
            WHEN LOWER(status) = 'selesai' THEN 1
            ELSE 0
        END) AS selesai
    FROM t_pelanggaran_siswa
");

if (!$query_ringkasan) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

$ringkasan = mysqli_fetch_assoc($query_ringkasan);

$total_pelanggaran = $ringkasan['total_pelanggaran'] ?? 0;
$total_tercatat = $ringkasan['tercatat'] ?? 0;
$total_diproses = $ringkasan['diproses'] ?? 0;
$total_selesai = $ringkasan['selesai'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
    crossorigin="anonymous">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">
                JusticeGuru
            </h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link active">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                        Catat Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
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

            <h2>Dashboard Guru</h2>

            <p>
                Selamat datang <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <!-- RINGKASAN PELANGGARAN -->
            <div class="row mt-4 mb-4">

                <!-- TOTAL PELANGGARAN -->
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Pelanggaran
                            </h6>

                            <h3>
                                <?= number_format($total_pelanggaran) ?>
                            </h3>

                        </div>
                    </div>
                </div>

                <!-- TERCATAT -->
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Tercatat
                            </h6>

                            <h3>
                                <?= number_format($total_tercatat) ?>
                            </h3>

                        </div>
                    </div>
                </div>

                <!-- DIPROSES -->
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Diproses
                            </h6>

                            <h3>
                                <?= number_format($total_diproses) ?>
                            </h3>

                        </div>
                    </div>
                </div>

                <!-- SELESAI -->
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Selesai
                            </h6>

                            <h3>
                                <?= number_format($total_selesai) ?>
                            </h3>

                        </div>
                    </div>
                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>

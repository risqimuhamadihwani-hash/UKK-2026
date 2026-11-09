<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? 'administrator';
$role = strtolower($_SESSION['role'] ?? '');

if ($role === 'guru') {
    header("Location: guru.php");
    exit;
}

if ($role !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Menghitung jumlah data dengan aman
function hitungData($koneksi, $tabel) {
    // Sesuaikan nama tabel 't_pelanggaran' dengan nama tabel pelanggaran di database Anda jika berbeda
    $daftarTabel = ['t_siswa', 't_guru', 't_kelas', 't_pelanggaran'];

    if (!in_array($tabel, $daftarTabel, true)) {
        return 0;
    }

    $hasil = $koneksi->query("SELECT COUNT(*) AS jumlah FROM `$tabel`");

    if ($hasil) {
        return (int) $hasil->fetch_assoc()['jumlah'];
    }

    return 0;
}

$jml_siswa = hitungData($koneksi, 't_siswa');
$jml_guru  = hitungData($koneksi, 't_guru');
$jml_kelas = hitungData($koneksi, 't_kelas');
$jml_pelanggaran = hitungData($koneksi, 't_pelanggaran'); // Ganti 't_pelanggaran' jika nama tabelnya beda
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-stat {
            border: none;
            border-radius: 8px;
            background-color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                My website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                        KelolaPenempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link text-white">
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
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted mb-4">
                Selamat datang Admin, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>. Berikut ringkasan Sistem Informasi Pelanggaran Siswa.
            </p>

            <!-- STATISTIK (4 KARTU) -->
            <div class="row mb-4">
                <!-- DATA SISWA -->
                <div class="col-md-3 mb-3">
                    <div class="card card-stat shadow-sm p-2">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-normal">Data Siswa</h6>
                            <h2 class="display-6 fw-bold text-primary mb-0">
                                <?= $jml_siswa ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- DATA GURU -->
                <div class="col-md-3 mb-3">
                    <div class="card card-stat shadow-sm p-2">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-normal">Data Guru</h6>
                            <h2 class="display-6 fw-bold text-success mb-0">
                                <?= $jml_guru ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- DATA KELAS -->
                <div class="col-md-3 mb-3">
                    <div class="card card-stat shadow-sm p-2">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-normal">Data Kelas</h6>
                            <h2 class="display-6 fw-bold text-warning mb-0">
                                <?= $jml_kelas ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- CATATAN PELANGGARAN -->
                <div class="col-md-3 mb-3">
                    <div class="card card-stat shadow-sm p-2">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-normal">Catatan Pelanggaran</h6>
                            <h2 class="display-6 fw-bold text-danger mb-0">
                                <?= $jml_pelanggaran ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALUR PENGGUNAAN -->
            <div class="card card-stat shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Alur penggunaan</h5>
                    <p class="text-muted mb-0">
                        Pilih menu di sidebar untuk mengelola data, mencatat pelanggaran, melihat riwayat, serta mencetak laporan sesuai hak akses akun.
                    </p>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? 'Pengguna';
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
    $daftarTabel = ['t_siswa', 't_guru', 't_kelas'];

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
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
     
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
                    <a href="dashboard.php" class="nav-link active">
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
        <h2>Dashboard</h2>
        <p>Selamat datang
    <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
        </p>
<div class="row">
    
    <!-- DATA SISWA -->
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Data Siswa</h5>
                <h2 class="jumlah">
                    <?= $jml_siswa ?>
                </h2>
            </div>
        </div>
    </div>

    <!-- DATA GURU -->
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Data Guru</h5>
                <h2 class="jumlah">
                    <?= $jml_guru ?>
                </h2>
            </div>
        </div>
    </div>

    <!-- DATA KELAS -->
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Kelas</h5>
                <h2 class="jumlah">
                    <?= $jml_kelas ?>
                </h2>
            </div>
        </div>
    </div>

</div>

</main>
</div>
</div>

</body>
</html>

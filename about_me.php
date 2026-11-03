<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');

if (!in_array($role, ['admin', 'guru'])) {
    header("Location: login.php");
    exit;
}

// Menentukan sidebar berdasarkan role
$isAdmin = ($role === 'admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - JusticeAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">JusticeAdmin</h4>

            <ul class="nav nav-pills flex-column">

                <?php if ($isAdmin): ?>

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
                    <a href="menu9.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu10.php" class="nav-link text-white">
                        Cetak/Export
                    </a>
                <?php else: ?>

                    <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
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
                <?php endif; ?>

                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link active">About Me</a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>About Me</h2>
                <span class="badge bg-primary">
                    <?= htmlspecialchars(ucfirst($role)) ?>
                </span>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center"
                             style="width: 110px; height: 110px; font-size: 40px;">
                            R
                        </div>

                        <h3 class="mt-3">Risqi Muhamad Ihwani</h3>

                        <p class="text-muted">
                            Student | Web Developer
                        </p>

                    </div>

                    <hr>

                    <h5>Informasi Pribadi</h5>

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama</th>
                            <td>Risqi Muhamad Ihwani</td>
                        </tr>
                        <tr>
                            <th>Sekolah</th>
                            <td>SMK Muhammadiyah Tasikmalaya</td>
                        </tr>
                        <tr>
                            <th>Jurusan</th>
                            <td>Rekayasa Perangkat Lunak (RPL)</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>Pelajar</td>
                        </tr>
                    </table>

                    <hr>

                    <h5>Tentang Saya</h5>

                    <p>
                        Halo! Perkenalkan, nama saya Risqi Muhamad Ihwani.
                        Saya merupakan siswa jurusan Rekayasa Perangkat
                        Lunak (RPL) yang memiliki ketertarikan dalam
                        bidang teknologi, desain, dan pengembangan website.
                    </p>

                    <p>
                        Website JusticeAdmin merupakan sebuah sistem
                        informasi yang dibuat untuk membantu sekolah
                        dalam mengelola data siswa, guru, kelas,
                        serta pencatatan pelanggaran siswa.
                    </p>

                    <hr>

                    <h5>Keahlian</h5>

                    <ul>
                        <li>HTML</li>
                        <li>CSS</li>
                        <li>Bootstrap</li>
                        <li>PHP Dasar</li>
                        <li>MySQL</li>
                        <li>Desain Grafis</li>
                    </ul>

                    <hr>

                    <h5>Tujuan</h5>

                    <p>
                        Terus belajar dan mengembangkan kemampuan
                        di bidang teknologi informasi serta menciptakan
                        aplikasi yang bermanfaat bagi banyak orang.
                    </p>

                    <hr>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            © 2026 JusticeAdmin
                        </p>
                        <p class="text-muted">
                            Created by Risqi Muhamad Ihwani
                        </p>
                    </div>

                </div>
            </div>

        </main>

    </div>
</div>

</body>
</html>

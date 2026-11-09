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

            <h4 class="text-white mb-4">
                <?= ($role === 'admin') ? 'Sistem Pelanggaran Siswa' : 'Sistem Pelanggaran Siswa'; ?>
            </h4>

            <ul class="nav nav-pills flex-column">

                <?php if ($isAdmin): ?>

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
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
                        Kelola Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Katrgoti_Pelanggaran.php" class="nav-link text-white">
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

                <?php else: ?>

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
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        Laporan
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

                        <div class="overflow-hidden rounded-circle d-inline-flex justify-content-center align-items-center bg-primary"
                             style="width: 110px; height: 110px;">
                            <!-- Anda bisa mengganti teks inisial/div ini dengan tag <img src="path/foto.jpg" class="w-100 h-100 object-fit-cover"> jika ada fotonya -->
                            <span class="text-white fw-bold" style="font-size: 40px;">R</span>
                        </div>

                        <h3 class="mt-3">Risqi</h3>

                        <p class="badge bg-primary">
                            Siswa Rekayasa Perangkat Lunak
                        </p>

                    </div>

                    <hr>

                    <h5 class="mb-3">Informasi Pribadi</h5>

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama</th>
                            <td>Risqi</td>
                        </tr>
                        <tr>
                            <th>Nama Panggilan</th>
                            <td>Qi</td>
                        </tr>
                        <tr>
                            <th>Umur</th>
                            <td>17 Tahun</td>
                        </tr>
                         <tr>
                            <th>Hobi</th>
                            <td>Nonton film</td>
                        </tr>
                        <tr>
                            <th>Cita-cita</th>
                            <td>Streamer Minecraft</td>
                        </tr>
                        <tr>
                            <th>Asal Sekolah</th>
                            <td>SMK Muhammadiyah Tasikmalaya</td>
                        </tr>
                        <tr>
                            <th>Kota Tinggal</th>
                            <td>Bungursari</td>
                        </tr>
                        <tr>
                            <th>Kota Asal</th>
                            <td>Tasikmalaya</td>
                        </tr>
                    </table>

                    <hr>

                    <h5 class="mb-3">Bio / Tentang Saya</h5>

                    <div class="p-3 bg-light border rounded mb-4">
                        <p class="mb-0 text-muted">
                            Saya .
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="dashboard.php" class="btn btn-primary mb-3">
                            &larr; Kembali ke Dashboard
                        </a>
                        <p class="text-muted mb-0">
                            &copy; UKK 2026 - Rekayasa Perangkat Lunak
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
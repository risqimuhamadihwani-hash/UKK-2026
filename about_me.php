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
    <title>About Me - Pelanggaran Siswa</title>

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

                <?php if ($isAdmin): ?>

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Dashboard</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Guru</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Tahun Ajaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Penempatan Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Wali Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Kategori Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Kelola Jenis Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Laporan</span> 
                    </a>
                </li>

                <?php else: ?>

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Dashboard</span> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Catat Pelanggaran</span> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Tindakan</span> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Rekap Poin</span> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Riwayat</span> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        <span class="fw-bold mb-4">Laporan</span> 
                    </a>
                </li>

                <?php endif; ?>

                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link active">
                        <span class="fw-bold mb-4">About Me</span>
                    </a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        <span class="fw-bold mb-4">Logout</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- HEADER DENGAN IKON & SUBTITLE SESUAI GAMBAR -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="h3 fw-bold mb-0 text-dark">About Me</h1>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">Kenali lebih dekat tentang diri saya.</p>
                    </div>
                </div>
                <span class="badge bg-primary px-3 py-2">
                    <?= htmlspecialchars(ucfirst($role)) ?>
                </span>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <div class="overflow-hidden rounded-circle d-inline-flex justify-content-center align-items-center bg-light border shadow-sm"
                            style="width: 110px; height: 110px;">
                        <img src="risqi_png.jpeg" alt="Risqi" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 class="mt-3">Risqi</h3>

                        <p class="badge bg-primary text-white">
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
                            <td>YTMCI</td>
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
                        Halo! Saya Risqi Muhamad Ihwani, siswa kelas XII RPL 1 di SMK Muhammadiyah Tasikmalaya. 
                        Saya mengambil jurusan Rekayasa Perangkat Lunak dan sedang belajar tentang Membuat server di roblox,
                        minecraft serta pembuatan website.
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="dashboard.php" class="btn btn-dark mb-3">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
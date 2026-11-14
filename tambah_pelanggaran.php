<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

$kode = "";
$nama = "";
$poin = "";
$deskripsi = "";
$status_aktif = 1;

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if (
        $kode === '' ||
        $nama === '' ||
        $poin <= 0 ||
        $deskripsi === '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran
            (kode, nama, poin, deskripsi, status_aktif)
            VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            $pesan = "Query gagal: " . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "ssisi",
                $kode,
                $nama,
                $poin,
                $deskripsi,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                // REDIRECT DENGAN PARAMETER NOTIFIKASI BERHASIL
                header("Location: Kelola_Jenis_Pelanggaran.php?pesan=berhasil");
                exit;
            } else {
                $pesan = "Gagal menyimpan data: " .
                    mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    
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
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Guru</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Tahun Ajaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Penempatan Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Wali Kelas</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Kategori Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link active">
                    <span class="fw-bold">Kelola Jenis Pelanggaran</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                    <span class="fw-bold">Laporan</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                    <span class="fw-bold">About Me</span> 
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
            
            <h3 class="mb-4 fw-bold">Tambah Jenis Pelanggaran</h3>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- KARTU FORM -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="tambah_pelanggaran.php">
                        
                        <div class="row">
                            <!-- INPUT KODE PELANGGARAN -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Kode</label>
                                <input type="text" name="kode" class="form-control" placeholder="Contoh: PG-001" value="<?= htmlspecialchars($kode, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>

                            <!-- INPUT POIN -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Poin</label>
                                <input type="number" name="poin" class="form-control" value="<?= htmlspecialchars($poin, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>

                        <!-- INPUT NAMA PELANGGARAN -->
                        <div class="mb-4">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama pelanggaran" value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <!-- INPUT DESKRIPSI -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi pelanggaran" required><?= htmlspecialchars($deskripsi, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-4">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $status_aktif === 1 ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $status_aktif === 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            Tambah
                        </button>

                        <a href="Kelola_Jenis_Pelanggaran.php" class="btn btn-secondary px-4">
                            Kembali
                        </a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
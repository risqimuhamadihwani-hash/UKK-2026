<?php
session_start();
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// PROSES SIMPAN
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? 0);

    if ($nama === '' || $deskripsi === '' || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Nama kategori dan deskripsi wajib diisi.";
    } else {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO t_pelanggaran_kategori
             (nama, deskripsi, status_aktif)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt, "ssi",
            $nama,
            $deskripsi,
            $status_aktif
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: Kelola_Kategori_Pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal menyimpan kategori: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                JusticeAdmin
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">Kelola Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">Kelola Guru</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">Kelola Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">Kelola Tahun Ajaran</a>
                </li>
                 <li class="nav-item mb-2">
                    <a href="Penempatan_Siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">Kelola Wali Kelas</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link active">Kelola Kategori Pelanggaran</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">Kelola Jenis Pelanggaran</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">Laporan</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link text-white">Cetak/Export</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h3 class="mb-4 fw-bold">Tambah Kategori Pelanggaran</h3>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php endif; ?>

            <!-- KARTU FORM -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="tambah_kategori_pelanggaran.php">

                        <!-- DROPDOWN NAMA KATEGORI -->
                        <div class="mb-4">
                            <label class="form-label">Nama Kategori</label>
                            <select name="nama" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Kategori Pelanggaran --</option>
                                <?php
                                $bidang = ['Kedisiplinan', 'Kerapian', 'Kehadiran', 'Ketertiban', 'Perlengkapan', 'Sikap', 'Kebersihan', 'Keamanan', 'Akademik', 'Lingkungan'];
                                foreach ($bidang as $b) {
                                    for ($i = 1; $i <= 10; $i++) {
                                        $val = "$b $i";
                                        $selected = (($_POST['nama'] ?? '') === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $selected>$val</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DESKRIPSI (DIUBAH MENJADI TEXTAREA AGAR BISA DIKETIK) -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi kategori pelanggaran..." required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            Tambah
                        </button>

                        <a href="Kelola_Kategori_Pelanggaran.php" class="btn btn-secondary px-4">
                            Kembali
                        </a>

                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>
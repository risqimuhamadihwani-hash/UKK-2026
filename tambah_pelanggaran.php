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
$status_aktif = "1";

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
                header("Location: Kelola_Jenis_Pelanggaran.php");
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
                    <a href="Penempatan_Siswa.php" class="nav-link text-white">
                        Penempatan Siswa
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
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link active">
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
                        
                        <!-- DROPDOWN KODE PELANGGARAN -->
                        <div class="mb-4">
                            <label class="form-label">Kode Pelanggaran</label>
                            <select name="kode" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Kode Pelanggaran --</option>
                                <?php
                                for ($i = 1; $i <= 100; $i++) {
                                    $formattedCode = "PG-" . str_pad($i, 3, "0", STR_PAD_LEFT);
                                    $sel = ($kode === $formattedCode) ? 'selected' : '';
                                    echo "<option value=\"$formattedCode\" $sel>$formattedCode</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN NAMA PELANGGARAN -->
                        <div class="mb-4">
                            <label class="form-label">Nama Pelanggaran</label>
                            <select name="nama" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Nama Pelanggaran --</option>
                                <?php
                                $masterPelanggaran = [
                                    "Terlambat datang ke sekolah",
                                    "Tidak memakai atribut lengkap",
                                    "Tidak memakai seragam sesuai ketentuan",
                                    "Meninggalkan kelas tanpa izin",
                                    "Tidak mengerjakan tugas",
                                    "Rambut tidak sesuai aturan",
                                    "Membawa barang terlarang",
                                    "Berkata tidak sopan",
                                    "Membuang sampah sembarangan",
                                    "Tidak mengikuti upacara"
                                ];

                                for ($i = 1; $i <= 100; $i++) {
                                    $index = ($i - 1) % 10;
                                    $namaPelanggaran = $masterPelanggaran[$index] . " #" . str_pad($i, 3, "0", STR_PAD_LEFT);
                                    $selNama = ($nama === $namaPelanggaran) ? 'selected' : '';
                                    echo "<option value=\"$namaPelanggaran\" $selNama>$namaPelanggaran</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN POIN (1 sampai 10) -->
                        <div class="mb-4">
                            <label class="form-label">Poin</label>
                            <select name="poin" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Jumlah Poin --</option>
                                <?php
                                for ($p = 1; $p <= 10; $p++) {
                                    $selPoin = ($poin == $p) ? 'selected' : '';
                                    echo "<option value=\"$p\" $selPoin>$p</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN DESKRIPSI -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <select name="deskripsi" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Deskripsi Pelanggaran --</option>
                                <?php
                                for ($i = 1; $i <= 100; $i++) {
                                    $descVal = "Deskripsi pelanggaran nomor $i.";
                                    $selDesc = ($deskripsi === $descVal) ? 'selected' : '';
                                    echo "<option value=\"$descVal\" $selDesc>$descVal</option>";
                                }
                                ?>
                            </select>
                        </div>

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
</body>
</html>
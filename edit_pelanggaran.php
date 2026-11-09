<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: Kelola_Jenis_Pelanggaran.php");
    exit;
}

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Jenis pelanggaran tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if ($kode === '' || $nama === '' || $poin <= 0 || $deskripsi === '' || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $update = mysqli_prepare($koneksi,
            "UPDATE t_pelanggaran
             SET kode = ?, nama = ?, poin = ?, deskripsi = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update, "ssisii", $kode, $nama, $poin, $deskripsi, $status_aktif, $id
        );

        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            header("Location: Kelola_Jenis_Pelanggaran.php");
            exit;
        } else {
            if (mysqli_stmt_errno($update) == 1062) {
                $pesan = "Kode pelanggaran sudah terdaftar.";
            } else {
                $pesan = "Gagal mengubah jenis pelanggaran: " . mysqli_stmt_error($update);
            }
        }

        mysqli_stmt_close($update);
    }

    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['poin'] = $poin;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4 ">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dasboard</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Guru</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Tahun Ajaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Penempatan Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Wali Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kategori Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Kelola Jenis Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Laporan</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Cetak/Export</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">About Me</h7> 
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                    <h7 class="fw-bold mb-4">Logout</h7> 
                    </a>
                </li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h3 class="mb-4 fw-bold">Edit Jenis Pelanggaran</h3>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesan) ?>
                </div>
            <?php endif; ?>

            <!-- KARTU FORM -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST">

                        <!-- DROPDOWN KODE PELANGGARAN -->
                        <div class="mb-4">
                            <label class="form-label">Kode Pelanggaran</label>
                            <select name="kode" class="form-select" required>
                                <option value="" disabled>-- Pilih Kode Pelanggaran --</option>
                                <?php
                                for ($i = 1; $i <= 100; $i++) {
                                    $formattedCode = "PG-" . str_pad($i, 3, "0", STR_PAD_LEFT);
                                    $selectedCode = ($data['kode'] === $formattedCode) ? 'selected' : '';
                                    echo "<option value=\"$formattedCode\" $selectedCode>$formattedCode</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN NAMA PELANGGARAN -->
                        <div class="mb-4">
                            <label class="form-label">Nama Pelanggaran</label>
                            <select name="nama" class="form-select" required>
                                <option value="" disabled>-- Pilih Nama Pelanggaran --</option>
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
                                    $selectedNama = ($data['nama'] === $namaPelanggaran) ? 'selected' : '';
                                    echo "<option value=\"$namaPelanggaran\" $selectedNama>$namaPelanggaran</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN POIN (1 sampai 10) -->
                        <div class="mb-4">
                            <label class="form-label">Poin</label>
                            <select name="poin" class="form-select" required>
                                <option value="" disabled>-- Pilih Jumlah Poin --</option>
                                <?php
                                for ($p = 1; $p <= 10; $p++) {
                                    $selectedPoin = ($data['poin'] == $p) ? 'selected' : '';
                                    echo "<option value=\"$p\" $selectedPoin>$p</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DROPDOWN DESKRIPSI -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <select name="deskripsi" class="form-select" required>
                                <option value="" disabled>-- Pilih Deskripsi Pelanggaran --</option>
                                <?php
                                for ($i = 1; $i <= 100; $i++) {
                                    $descVal = "Deskripsi pelanggaran nomor $i.";
                                    $selectedDesc = ($data['deskripsi'] === $descVal) ? 'selected' : '';
                                    echo "<option value=\"$descVal\" $selectedDesc>$descVal</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-4">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>
                                <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            Simpan Perubahan
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
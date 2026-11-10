<?php
session_start();
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: Kelola_Kategori_Pelanggaran.php");
    exit;
}

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Kategori pelanggaran tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if ($nama === '' || $deskripsi === '' || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $update = mysqli_prepare($koneksi,
            "UPDATE t_pelanggaran_kategori
             SET nama = ?, deskripsi = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update, "ssii", $nama, $deskripsi, $status_aktif, $id
        );

        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            header("Location: Kelola_Kategori_Pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal mengubah kategori: " . mysqli_stmt_error($update);
        }

        mysqli_stmt_close($update);
    }

    $data['nama'] = $nama;
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
    <title>Edit Kategori Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4 ">
            Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dashboard</h7> 
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
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Kelola Kategori Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
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
            
            <h3 class="mb-4 fw-bold">Edit Kategori Pelanggaran</h3>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesan) ?>
                </div>
            <?php endif; ?>

            <!-- KARTU FORM -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST">

                        <!-- DROPDOWN NAMA KATEGORI -->
                        <div class="mb-4">
                            <label class="form-label">Nama Kategori</label>
                            <select name="nama" class="form-select" required>
                                <option value="" disabled>-- Pilih Kategori Pelanggaran --</option>
                                <?php
                                $bidang = ['Kedisiplinan', 'Kerapian', 'Kehadiran', 'Ketertiban', 'Perlengkapan', 'Sikap', 'Kebersihan', 'Keamanan', 'Akademik', 'Lingkungan'];
                                foreach ($bidang as $b) {
                                    for ($i = 1; $i <= 10; $i++) {
                                        $val = "$b $i";
                                        $selected = ($data['nama'] === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $selected>$val</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- DESKRIPSI (DIUBAH MENJADI TEXTAREA AGAR BISA DIKETIK) -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi kategori pelanggaran..." required><?= e($data['deskripsi']) ?></textarea>
                        </div>

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
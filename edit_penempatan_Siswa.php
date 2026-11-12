<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_kelas_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$d) {
    header("Location: Kelola_Penempatan_Siswa.php");
    exit;
}

// Ambil data tahun ajaran aktif secara otomatis untuk tanggal mulai & selesai
$tahun_aktif_query = mysqli_query($koneksi, "SELECT id, nama, tanggal_mulai, tanggal_selesai FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahun_aktif = mysqli_fetch_assoc($tahun_aktif_query);
$tahun_ajaran_aktif_id = $tahun_aktif ? (int)$tahun_aktif['id'] : 0;

$pesan = "";

if (isset($_POST['update'])) {
    $siswa_id = (int)$_POST['siswa_id'];
    $tahun_ajaran_id = $tahun_ajaran_aktif_id;
    $kelas_id = (int)$_POST['kelas_id'];
    $tanggal_mulai = $tahun_aktif ? $tahun_aktif['tanggal_mulai'] : '';
    $tanggal_selesai = $tahun_aktif ? $tahun_aktif['tanggal_selesai'] : '';
    $status_aktif = (int)$_POST['status_aktif'];

    if (
        $siswa_id <= 0 || $tahun_ajaran_id <= 0 ||
        $kelas_id <= 0 || $tanggal_mulai == "" ||
        $tanggal_selesai == "" ||
        $tanggal_selesai < $tanggal_mulai
    ) {
        $pesan = "Data tidak valid atau tidak ada tahun ajaran aktif. Periksa kembali form.";
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_kelas_siswa
             WHERE siswa_id = ?
             AND tahun_ajaran_id = ?
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cek, "iii", $siswa_id, $tahun_ajaran_id, $id
        );
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Siswa sudah ditempatkan pada tahun ajaran tersebut.";
        } else {
            $update = mysqli_prepare($koneksi,
                "UPDATE t_kelas_siswa
                 SET siswa_id = ?, tahun_ajaran_id = ?,
                     kelas_id = ?, tanggal_mulai = ?,
                     tanggal_selesai = ?, status_aktif = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update, "iiissii",
                $siswa_id, $tahun_ajaran_id, $kelas_id,
                $tanggal_mulai, $tanggal_selesai,
                $status_aktif, $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                mysqli_stmt_close($cek);
                header("Location: Kelola_Penempatan_Siswa.php");
                exit;
            } else {
                $pesan = "Gagal memperbarui data.";
            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($cek);
    }
}

// DATA SISWA: Menampilkan siswa yang belum ditempatkan, ATAU siswa yang sedang diedit ini sendiri
$siswa = mysqli_query($koneksi,
    "SELECT id, nis, nama FROM t_siswa 
     WHERE id NOT IN (SELECT siswa_id FROM t_kelas_siswa WHERE id != $id)
     ORDER BY nama ASC");

// DATA KELAS
$kelas = mysqli_query($koneksi,
    "SELECT id, nama, tingkat, jurusan FROM t_kelas
     WHERE status_aktif = 1 OR id = " . (int)$d['kelas_id'] . "
     ORDER BY nama ASC");

function e($data) {
    return htmlspecialchars((string) ($data ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penempatan Siswa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link active">
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
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
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

            <h3 class="fw-bold mb-4">Edit Penempatan Siswa</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($pesan !== ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= e($pesan) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Siswa (Belum Ditempatkan)</label>
                            <select name="siswa_id" id="selectSiswa" class="form-select" required>
                                <option value="">-- Pilih atau Cari Siswa --</option>
                                <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                                    <option value="<?= $s['id'] ?>"
                                        <?= $d['siswa_id'] == $s['id'] ? 'selected' : '' ?>>
                                        <?= e($s['nis'] . ' - ' . $s['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <?php if ($tahun_aktif): ?>
                                <input type="hidden" name="tahun_ajaran_id" value="<?= $tahun_ajaran_aktif_id ?>">
                                <input type="text" class="form-control bg-light" value="<?= e($tahun_aktif['nama']) ?>" readonly>
                            <?php else: ?>
                                <input type="text" class="form-control bg-light text-danger" value="Tidak ada tahun ajaran yang aktif saat ini!" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $k['id'] ?>"
                                        <?= $d['kelas_id'] == $k['id'] ? 'selected' : '' ?>>
                                        <?= e($k['nama'] . ' - ' . $k['tingkat'] . ' - ' . $k['jurusan']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $d['status_aktif'] == 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>
                                <option value="0" <?= $d['status_aktif'] == 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="update" class="btn btn-primary px-3 py-1 text-white" style="background-color: #0d6efd;" <?= !$tahun_aktif ? 'disabled' : '' ?>>
                                Simpan Perubahan
                            </button>
                            <a href="Kelola_Penempatan_Siswa.php" class="btn btn-secondary px-3 py-1" style="background-color: #6c757d;">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#selectSiswa').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
</body>
</html>
<?php
session_start();
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: Kelola_Wali_Kelas.php");
    exit;
}

$pesan = "";

// Ambil data tahun ajaran aktif secara otomatis (termasuk tanggal mulai & selesai)
$tahun_aktif_query = mysqli_query($koneksi, "SELECT id, nama, tanggal_mulai, tanggal_selesai FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahun_aktif = mysqli_fetch_assoc($tahun_aktif_query);
$tahun_ajaran_aktif_id = $tahun_aktif ? (int)$tahun_aktif['id'] : 0;

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data wali kelas tidak ditemukan.");
}

if (isset($_POST['simpan'])) {
    // Tahun ajaran dipaksa menggunakan ID tahun ajaran aktif yang sedang berjalan
    $tahun_ajaran_id = $tahun_ajaran_aktif_id;
    $kelas_id = (int) $_POST['kelas_id'];
    $guru_id = (int) $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = (int) ($_POST['status_aktif'] ?? -1);

    if (
        $tahun_ajaran_id <= 0 ||
        $kelas_id <= 0 ||
        $guru_id <= 0 ||
        $tanggal_mulai == "" ||
        $tanggal_selesai == "" ||
        $tanggal_selesai < $tanggal_mulai ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Data tidak valid atau tidak ada tahun ajaran aktif. Periksa kembali form.";
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_wali_kelas
             WHERE tahun_ajaran_id = ?
             AND kelas_id = ?
             AND status_aktif = 1
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cek, "iii",
            $tahun_ajaran_id, $kelas_id, $id
        );
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (
            $status_aktif == 1 &&
            mysqli_stmt_num_rows($cek) > 0
        ) {
            $pesan = "Kelas tersebut sudah memiliki wali kelas aktif.";
            mysqli_stmt_close($cek);
        } else {
            mysqli_stmt_close($cek);

            $update = mysqli_prepare($koneksi,
                "UPDATE t_wali_kelas
                 SET tahun_ajaran_id = ?,
                     kelas_id = ?,
                     guru_id = ?,
                     tanggal_mulai = ?,
                     tanggal_selesai = ?,
                     status_aktif = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update, "iiissii",
                $tahun_ajaran_id,
                $kelas_id,
                $guru_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);

                // Sinkronkan status_aktif guru di tabel t_guru agar ikut berubah otomatis
                $update_guru = mysqli_prepare($koneksi, 
                    "UPDATE t_guru SET status_aktif = ? WHERE id = ?"
                );
                mysqli_stmt_bind_param($update_guru, "ii", $status_aktif, $guru_id);
                mysqli_stmt_execute($update_guru);
                mysqli_stmt_close($update_guru);

                // DITAMBAHKAN ?pesan=diubah AGAR NOTIFIKASI MUNCUL DI KELOLA WALI KELAS
                header("Location: Kelola_Wali_Kelas.php?pesan=diubah");
                exit;
            } else {
                $pesan = "Gagal mengubah data wali kelas.";
                mysqli_stmt_close($update);
            }
        }
    }

    $data['tahun_ajaran_id'] = $tahun_ajaran_id;
    $data['kelas_id'] = $kelas_id;
    $data['guru_id'] = $guru_id;
    $data['tanggal_mulai'] = $tanggal_mulai;
    $data['tanggal_selesai'] = $tanggal_selesai;
    $data['status_aktif'] = $status_aktif;
}

$kelas = mysqli_query($koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$guru = mysqli_query($koneksi,
    "SELECT id, nip, nama FROM t_guru ORDER BY nama ASC"
);

if (!$kelas || !$guru) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

function e($data) {
    return htmlspecialchars((string) ($data ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wali Kelas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
            <h4 class="text-white mb-4">
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
                    <a href="Kelola_Wali_Kelas.php" class="nav-link active">
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

            <h3 class="fw-bold mb-4">Edit Wali Kelas</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($pesan !== ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= e($pesan) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
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
                                    <option value="<?= (int) $k['id'] ?>"
                                        <?= $data['kelas_id'] == $k['id'] ? 'selected' : '' ?>>
                                        <?= e($k['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select select2-guru" required>
                                <option value="">-- Pilih atau Cari Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)): ?>
                                    <?php $bersihNip = str_ireplace('NIP', '', $g['nip'] ?? ''); ?>
                                    <option value="<?= (int) $g['id'] ?>"
                                        <?= $data['guru_id'] == $g['id'] ? 'selected' : '' ?>>
                                        <?= e(trim($bersihNip) . ' - ' . $g['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                class="form-control bg-light"
                                value="<?= $tahun_aktif ? e($tahun_aktif['tanggal_mulai']) : e($data['tanggal_mulai']) ?>"
                                readonly required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                class="form-control bg-light"
                                value="<?= $tahun_aktif ? e($tahun_aktif['tanggal_selesai']) : e($data['tanggal_selesai']) ?>"
                                readonly required>
                        </div>

                        <div class="mb-3">
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

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary px-3 py-1 text-white" style="background-color: #0d6efd;" <?= !$tahun_aktif ? 'disabled' : '' ?>>
                                Simpan Perubahan
                            </button>
                            <a href="Kelola_Wali_Kelas.php" class="btn btn-secondary px-3 py-1" style="background-color: #6c757d;">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-guru').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih atau Cari Guru --',
        allowClear: true,
        width: '100%'
    });
});
</script>
</body>
</html>
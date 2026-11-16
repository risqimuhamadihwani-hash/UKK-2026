<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
$session_id = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? 0;
if ($session_id < 1) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? $_SESSION['level'] ?? ''));

// VALIDASI ROLE
if ($role !== 'admin' && $role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$is_admin = ($role === 'admin');
$is_session_wali_kelas = ($role === 'wali_kelas' || $role === 'walikelas');

if (!function_exists('e')) {
    function e($data) {
        return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$koneksi = $conn ?? $koneksi ?? null;

if (!$koneksi instanceof mysqli) {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php.");
}

$error = '';

// AMBIL TAHUN AJARAN AKTIF
$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

// DETEKSI GURU & KELAS WALI KELAS SECARA AKURAT
$guru_id = 0;
$nama_guru = '';
$kelas_wali_id = 0;
$is_wali_kelas = false;

$stmtGuru = $koneksi->prepare(
    "SELECT g.id AS guru_id, g.nama AS nama_guru 
     FROM t_guru g 
     WHERE g.user_id = ? OR g.id = ? 
     LIMIT 1"
);
$stmtGuru->bind_param("ii", $session_id, $session_id);
$stmtGuru->execute();
$dataGuruLogin = $stmtGuru->get_result()->fetch_assoc();
$stmtGuru->close();

if ($dataGuruLogin && !empty($dataGuruLogin['guru_id'])) {
    $guru_id = (int) $dataGuruLogin['guru_id'];
    $nama_guru = $dataGuruLogin['nama_guru'];

    $stmtWali = $koneksi->prepare(
        "SELECT wk.kelas_id, k.nama AS nama_kelas 
         FROM t_wali_kelas wk
         JOIN t_kelas k ON k.id = wk.kelas_id
         WHERE wk.guru_id = ? AND wk.tahun_ajaran_id = ? AND wk.status_aktif = 1
         LIMIT 1"
    );
    $stmtWali->bind_param("ii", $guru_id, $tahun_id);
    $stmtWali->execute();
    $resWali = $stmtWali->get_result()->fetch_assoc();
    $stmtWali->close();

    if ($resWali) {
        $kelas_wali_id = (int) $resWali['kelas_id'];
        $is_wali_kelas = true;
    }
} else {
    $guru_id = 0;
    $nama_guru = trim($_SESSION['name'] ?? ($_SESSION['nama'] ?? ''));
}

if ($is_session_wali_kelas) {
    $is_wali_kelas = true;
}

// Ambil daftar seluruh guru untuk dropdown (jika Admin/Guru biasa)
$guruListAll = $koneksi->query("SELECT id, nama FROM t_guru ORDER BY nama ASC");

// AMBIL ID DATA YANG MAU DIEDIT
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: catatan_pelanggaran.php");
    exit;
}

// AMBIL DATA PELANGGARAN SISWA BERDASARKAN ID
$stmtEdit = $koneksi->prepare("SELECT * FROM t_pelanggaran_siswa WHERE id = ? LIMIT 1");
$stmtEdit->bind_param("i", $id);
$stmtEdit->execute();
$dataCatatan = $stmtEdit->get_result()->fetch_assoc();
$stmtEdit->close();

if (!$dataCatatan) {
    header("Location: catatan_pelanggaran.php?error=notfound");
    exit;
}

// Ambil detail nama siswa dan kelas yang sedang diedit untuk ditampilkan (read-only)
$stmtSiswaEdit = $koneksi->prepare(
    "SELECT s.nama, COALESCE(k.nama, 'Tanpa Kelas') AS nama_kelas
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
     LEFT JOIN t_kelas k ON k.id = ks.kelas_id
     WHERE s.id = ? LIMIT 1"
);
$stmtSiswaEdit->bind_param("ii", $tahun_id, $dataCatatan['siswa_id']);
$stmtSiswaEdit->execute();
$resSiswaEdit = $stmtSiswaEdit->get_result()->fetch_assoc();
$stmtSiswaEdit->close();

$nama_siswa_edit = $resSiswaEdit['nama'] ?? ($dataCatatan['nama_siswa'] ?? 'Siswa');
$nama_kelas_edit = $resSiswaEdit['nama_kelas'] ?? ($dataCatatan['nama_kelas'] ?? '-');

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Siswa dikunci dari data awal, tidak diambil dari $_POST['siswa_id'] luar untuk keamanan
        $siswa_id = (int) $dataCatatan['siswa_id'];
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $status = trim($_POST['status'] ?? 'tercatat');

        if ($is_wali_kelas && !$is_admin) {
            if ($guru_id < 1) {
                throw new Exception("ID Guru Wali Kelas tidak valid.");
            }
        } else {
            $guru_id = (int) ($_POST['guru_id'] ?? 0);
        }

        if ($guru_id < 1 || $siswa_id < 1 || $pelanggaran_id < 1 || $keterangan === '') {
            throw new Exception("Lengkapi semua data form dengan benar.");
        }

        $stmtCekGuru = $koneksi->prepare("SELECT id, nama FROM t_guru WHERE id = ? LIMIT 1");
        $stmtCekGuru->bind_param("i", $guru_id);
        $stmtCekGuru->execute();
        $resCekGuru = $stmtCekGuru->get_result()->fetch_assoc();
        $stmtCekGuru->close();

        if (!$resCekGuru) {
            throw new Exception("Data guru pelapor tidak valid.");
        }
        $nama_guru = $resCekGuru['nama'];

        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new Exception("Format tanggal tidak valid.");
        }

        // Gunakan nama siswa yang sudah ada/terbaru
        $nama_siswa = $nama_siswa_edit;
        $kelas_id = $dataCatatan['kelas_id'];
        $nama_kelas = $nama_kelas_edit;

        // Ambil data jenis pelanggaran & poin
        $stmtP = $koneksi->prepare(
            "SELECT id, nama, poin, pelanggaran_kategori_id
             FROM t_pelanggaran
             WHERE id = ? AND status_aktif = 1 LIMIT 1"
        );
        $stmtP->bind_param("i", $pelanggaran_id);
        $stmtP->execute();
        $pelanggaran = $stmtP->get_result()->fetch_assoc();
        $stmtP->close();

        if (!$pelanggaran) {
            throw new Exception("Jenis pelanggaran tidak ditemukan atau tidak aktif.");
        }

        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) ($pelanggaran['pelanggaran_kategori_id'] ?? 0);
        $poin = (int) $pelanggaran['poin'];

        // Update database dengan penanganan aman untuk kelas_id NULL
        if ($kelas_id === null || $kelas_id === '') {
            $stmtUpdate = $koneksi->prepare(
                "UPDATE t_pelanggaran_siswa SET 
                    siswa_id = ?, 
                    nama_siswa = ?, 
                    kelas_id = NULL, 
                    nama_kelas = ?, 
                    pelanggaran_id = ?, 
                    nama_pelanggaran = ?, 
                    pelanggaran_kategori_id = ?, 
                    guru_id = ?,
                    nama_guru = ?,
                    tanggal = ?, 
                    keterangan = ?, 
                    poin = ?, 
                    status = ?
                 WHERE id = ?"
            );
            $stmtUpdate->bind_param(
                "isisisissisii",
                $siswa_id,
                $nama_siswa,
                $nama_kelas,
                $pelanggaran_id,
                $nama_pelanggaran,
                $kategori_id,
                $guru_id,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $status,
                $id
            );
        } else {
            $stmtUpdate = $koneksi->prepare(
                "UPDATE t_pelanggaran_siswa SET 
                    siswa_id = ?, 
                    nama_siswa = ?, 
                    kelas_id = ?, 
                    nama_kelas = ?, 
                    pelanggaran_id = ?, 
                    nama_pelanggaran = ?, 
                    pelanggaran_kategori_id = ?, 
                    guru_id = ?,
                    nama_guru = ?,
                    tanggal = ?, 
                    keterangan = ?, 
                    poin = ?, 
                    status = ?
                 WHERE id = ?"
            );
            $stmtUpdate->bind_param(
                "isisisiisssisi",
                $siswa_id,
                $nama_siswa,
                (int)$kelas_id,
                $nama_kelas,
                $pelanggaran_id,
                $nama_pelanggaran,
                $kategori_id,
                $guru_id,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $status,
                $id
            );
        }

        $stmtUpdate->execute();
        $stmtUpdate->close();

        header("Location: catatan_pelanggaran.php?berhasil=edit");
        exit;

    } catch (Throwable $e) {
        $error = "Gagal memperbarui: " . $e->getMessage();
    }
}

// Ambil daftar pelanggaran untuk dropdown
$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin 
     FROM t_pelanggaran 
     WHERE status_aktif = 1 
     ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan Pelanggaran Siswa</title>
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
            <h4 class="text-white fw-bold mb-4">Pelanggaran Siswa</h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link text-white"><span class="fw-bold">Dashboard</span></a></li>
                <li class="nav-item mb-2"><a href="catatan_pelanggaran.php" class="nav-link active"><span class="fw-bold">Catatan Pelanggaran</span></a></li>
                <li class="nav-item mb-2"><a href="Tindakan.php" class="nav-link text-white"><span class="fw-bold">Tindakan</span></a></li>
                <li class="nav-item mb-2"><a href="rekap_poin.php" class="nav-link text-white"><span class="fw-bold">Rekap Poin</span></a></li>
                <li class="nav-item mb-2"><a href="riwayat.php" class="nav-link text-white"><span class="fw-bold">Riwayat</span></a></li>
                <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
                <hr class="text-secondary">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a></li>
            </ul>
        </div>
        
        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Edit Catatan Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">Perbarui informasi pelanggaran siswa jika terdapat kesalahan data.</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- FORM EDIT CATATAN PELANGGARAN -->
            <div class="card shadow-sm border-0 p-4 mb-4">
                <h5 class="fw-bold mb-3">Form Edit Pelanggaran</h5>
                <form method="POST" action="">
                    <div class="row g-3 align-items-center mb-3">
                        
                        <!-- Pilihan Guru Pelapor -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Guru Pelapor</label>
                            <?php if ($is_wali_kelas && !$is_admin): ?>
                                <input type="hidden" name="guru_id" value="<?= (int)$guru_id ?>">
                                <input type="text" class="form-control form-control-sm bg-light fw-bold" value="<?= e($nama_guru) ?>" readonly>
                            <?php else: ?>
                                <select name="guru_id" id="selectGuru" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Guru Pelapor --</option>
                                    <?php 
                                    if ($guruListAll) {
                                        $guruListAll->data_seek(0);
                                        while ($g = $guruListAll->fetch_assoc()) {
                                            $selected = ((int)$g['id'] === (int)$dataCatatan['guru_id']) ? 'selected' : '';
                                            echo '<option value="' . (int)$g['id'] . '" ' . $selected . '>' . e($g['nama']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Siswa (Dikunci / Read-only karena tidak boleh diganti saat edit catatan pelanggaran) -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Siswa</label>
                            <input type="hidden" name="siswa_id" value="<?= (int)$dataCatatan['siswa_id'] ?>">
                            <input type="text" class="form-control form-control-sm bg-light fw-bold" value="<?= e($nama_siswa_edit) ?> (<?= e($nama_kelas_edit) ?>)" readonly>
                        </div>

                        <!-- Tanggal Kejadian -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= e($dataCatatan['tanggal']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center mb-3">
                        <!-- Jenis Pelanggaran -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Jenis Pelanggaran</label>
                            <select name="pelanggaran_id" id="selectPelanggaran" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Pelanggaran --</option>
                                <?php while ($p = $pelanggaranList->fetch_assoc()): ?>
                                    <option value="<?= (int) $p['id'] ?>" <?= ((int)$p['id'] === (int)$dataCatatan['pelanggaran_id']) ? 'selected' : '' ?>>
                                        <?= e($p['nama']) ?> (<?= (int)$p['poin'] ?> Poin)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Status Penanganan -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Status Penanganan</label>
                            <select name="status" class="form-select form-select-sm" required>
                                <option value="tercatat" <?= ($dataCatatan['status'] === 'tercatat') ? 'selected' : '' ?>>Tercatat / Perlu Tindakan</option>
                                <option value="diproses" <?= ($dataCatatan['status'] === 'diproses') ? 'selected' : '' ?>>Diproses</option>
                                <option value="selesai" <?= ($dataCatatan['status'] === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                    </div>

                    <!-- Catatan & Tombol Aksi -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-10">
                            <label class="form-label text-muted small mb-1">Catatan</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="3" placeholder="Catatan detail kronologi kejadian..." required><?= e($dataCatatan['keterangan']) ?></textarea>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-50">Simpan</button>
                            <a href="catatan_pelanggaran.php" class="btn btn-secondary btn-sm w-50 text-center pt-2">Batal</a>
                        </div>
                    </div>
                </form>
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
    if ($('#selectGuru').length) {
        $('#selectGuru').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
    $('#selectPelanggaran').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
</body>
</html>
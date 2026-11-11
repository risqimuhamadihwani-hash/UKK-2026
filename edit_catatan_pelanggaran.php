<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

// VALIDASI ROLE (Izinkan admin, guru, dan wali_kelas)
if ($role !== 'admin' && $role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    session_destroy();
    header("Location: login.php");
    exit;
}

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
$session_id = (int)($_SESSION['id_user'] ?? 0);

// AMBIL TAHUN AJARAN AKTIF
$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

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

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $status = trim($_POST['status'] ?? 'tercatat');

        if ($siswa_id < 1 || $pelanggaran_id < 1 || $keterangan === '') {
            throw new Exception("Lengkapi semua data form dengan benar.");
        }

        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new Exception("Format tanggal tidak valid.");
        }

        // Ambil data siswa terbaru
        $stmtSiswa = $koneksi->prepare("SELECT id, nama FROM t_siswa WHERE id = ? LIMIT 1");
        $stmtSiswa->bind_param("i", $siswa_id);
        $stmtSiswa->execute();
        $siswa = $stmtSiswa->get_result()->fetch_assoc();
        $stmtSiswa->close();

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }
        $nama_siswa = $siswa['nama'];

        // Ambil data kelas siswa berdasarkan tahun ajaran aktif
        $stmtKelas = $koneksi->prepare(
            "SELECT k.id AS kelas_id, k.nama AS nama_kelas
             FROM t_kelas_siswa ks
             JOIN t_kelas k ON k.id = ks.kelas_id
             WHERE ks.siswa_id = ? AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
             LIMIT 1"
        );
        $stmtKelas->bind_param("ii", $siswa_id, $tahun_id);
        $stmtKelas->execute();
        $kelasData = $stmtKelas->get_result()->fetch_assoc();
        $stmtKelas->close();

        $kelas_id = ($kelasData && !empty($kelasData['kelas_id'])) ? (int) $kelasData['kelas_id'] : null;
        $nama_kelas = $kelasData ? $kelasData['nama_kelas'] : '-';

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

        // Update database
        $stmtUpdate = $koneksi->prepare(
            "UPDATE t_pelanggaran_siswa SET 
                siswa_id = ?, 
                nama_siswa = ?, 
                kelas_id = ?, 
                nama_kelas = ?, 
                pelanggaran_id = ?, 
                nama_pelanggaran = ?, 
                pelanggaran_kategori_id = ?, 
                tanggal = ?, 
                keterangan = ?, 
                poin = ?, 
                status = ?
             WHERE id = ?"
        );

        $stmtUpdate->bind_param(
            "isiississisi",
            $siswa_id,
            $nama_siswa,
            $kelas_id,
            $nama_kelas,
            $pelanggaran_id,
            $nama_pelanggaran,
            $kategori_id,
            $tanggal,
            $keterangan,
            $poin,
            $status,
            $id
        );

        $stmtUpdate->execute();
        $stmtUpdate->close();

        header("Location: catatan_pelanggaran.php?berhasil=edit");
        exit;

    } catch (Throwable $e) {
        $error = "Gagal memperbarui: " . $e->getMessage();
    }
}

// Ambil daftar siswa untuk dropdown
$siswaList = $koneksi->query(
    "SELECT s.id, s.nis, s.nama, COALESCE(MAX(k.nama), 'Tanpa Kelas') AS nama_kelas
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.status_aktif = 1 AND ks.tahun_ajaran_id = $tahun_id
     LEFT JOIN t_kelas k ON k.id = ks.kelas_id
     GROUP BY s.id, s.nis, s.nama
     ORDER BY s.nama ASC"
);

// Ambil daftar pelanggaran untuk dropdown
$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin 
     FROM t_pelanggaran 
     WHERE status_aktif = 1 
     ORDER BY CAST(SUBSTRING(kode, 4) AS UNSIGNED) ASC"
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
                        
                        <!-- Pilih Siswa dengan Select2 -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Siswa</label>
                            <select name="siswa_id" id="selectSiswa" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php while ($s = $siswaList->fetch_assoc()): ?>
                                    <option value="<?= (int) $s['id'] ?>" <?= ($s['id'] == $dataCatatan['siswa_id']) ? 'selected' : '' ?>>
                                        <?= e($s['nama']) ?> (<?= e($s['nama_kelas']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Pilih Jenis Pelanggaran -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Jenis Pelanggaran</label>
                            <select name="pelanggaran_id" id="selectPelanggaran" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Pelanggaran --</option>
                                <?php while ($p = $pelanggaranList->fetch_assoc()): ?>
                                    <option value="<?= (int) $p['id'] ?>" <?= ($p['id'] == $dataCatatan['pelanggaran_id']) ? 'selected' : '' ?>>
                                        <?= e($p['nama']) ?> (<?= (int)$p['poin'] ?> Poin)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Tanggal Kejadian -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= e($dataCatatan['tanggal']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center mb-3">
                        <!-- Status Pelanggaran -->
                        <div class="col-md-4">
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
    $('#selectSiswa').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    $('#selectPelanggaran').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
</body>
</html>
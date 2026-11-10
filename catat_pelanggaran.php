<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin' && $role !== 'guru') {
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

$pesan = '';
$error = '';

$session_name = trim($_SESSION['name'] ?? 'Aldi Maulana');
$session_email = trim($_SESSION['email'] ?? 'aldimaulana@gmail.com');

$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

// CARI DATA GURU SECARA EKSPLISIT BERDASARKAN NAMA ATAU EMAIL ALDI MAULANA
$guru_id = 0;
$nama_guru = $session_name;
$nama_kelas_wali = '';

$stmtGuru = $koneksi->prepare(
    "SELECT g.id AS guru_id, g.nama AS nama_guru, k.id AS kelas_id, k.nama AS nama_kelas
     FROM t_guru g
     LEFT JOIN t_wali_kelas wk ON wk.guru_id = g.id AND wk.tahun_ajaran_id = ? AND wk.status_aktif = 1
     LEFT JOIN t_kelas k ON k.id = wk.kelas_id
     WHERE g.nama LIKE '%Aldi Maulana%' OR g.email = ?
     LIMIT 1"
);
$stmtGuru->bind_param("is", $tahun_id, $session_email);
$stmtGuru->execute();
$dataWaliLogin = $stmtGuru->get_result()->fetch_assoc();
$stmtGuru->close();

if ($dataWaliLogin) {
    $guru_id = (int) $dataWaliLogin['guru_id'];
    $nama_guru = $dataWaliLogin['nama_guru'];
    $nama_kelas_wali = $dataWaliLogin['nama_kelas'] ?? '';
} else {
    // Fallback absolut ke data Aldi Maulana di t_guru (ID 32)
    $guru_id = 32;
    $nama_guru = "Aldi Maulana, S.Pd";
}

// PROSES SIMPAN PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($siswa_id < 1 || $pelanggaran_id < 1 || $guru_id < 1 || $keterangan === '') {
            throw new Exception("Lengkapi semua data form dengan benar.");
        }

        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new Exception("Format tanggal tidak valid.");
        }

        $stmt = $koneksi->prepare("SELECT id, nama FROM t_siswa WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $siswa_id);
        $stmt->execute();
        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }
        $nama_siswa = $siswa['nama'];

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

        $stmt = $koneksi->prepare(
            "SELECT id, nama, poin, pelanggaran_kategori_id
             FROM t_pelanggaran
             WHERE id = ? AND status_aktif = 1 LIMIT 1"
        );
        $stmt->bind_param("i", $pelanggaran_id);
        $stmt->execute();
        $pelanggaran = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$pelanggaran) {
            throw new Exception("Jenis pelanggaran tidak ditemukan atau tidak aktif.");
        }

        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) ($pelanggaran['pelanggaran_kategori_id'] ?? 0);
        $poin = (int) $pelanggaran['poin'];
        $status = 'tercatat';
        $tindakan = ''; 

        $stmt = $koneksi->prepare(
            "INSERT INTO t_pelanggaran_siswa
            (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iisiisiississis",
            $tahun_id,
            $siswa_id,
            $nama_siswa,
            $kelas_id,
            $nama_kelas,
            $pelanggaran_id,
            $nama_pelanggaran,
            $kategori_id,
            $guru_id,
            $nama_guru,
            $tanggal,
            $keterangan,
            $poin,
            $tindakan,
            $status
        );

        $stmt->execute();
        $stmt->close();

        header("Location: catat_pelanggaran.php?berhasil=1");
        exit;

    } catch (Throwable $e) {
        $error = "Gagal menyimpan: " . $e->getMessage();
    }
}

if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
}

$siswaList = $koneksi->query(
    "SELECT s.id, s.nis, s.nama, COALESCE(MAX(k.nama), 'Tanpa Kelas') AS nama_kelas
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.status_aktif = 1
     LEFT JOIN t_kelas k ON k.id = ks.kelas_id
     GROUP BY s.id, s.nis, s.nama
     ORDER BY s.nama ASC"
);

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
    <title>Catat Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4">Pelanggaran Siswa</h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link text-white"><span class="fw-bold">Dashboard</span></a></li>
                <li class="nav-item mb-2"><a href="catat_pelanggaran.php" class="nav-link active"><span class="fw-bold">Catat Pelanggaran</span></a></li>
                <li class="nav-item mb-2"><a href="Tindakan.php" class="nav-link text-white"><span class="fw-bold">Tindakan</span></a></li>
                <li class="nav-item mb-2"><a href="rekap_poin.php" class="nav-link text-white"><span class="fw-bold">Rekap Poin</span></a></li>
                <li class="nav-item mb-2"><a href="riwayat.php" class="nav-link text-white"><span class="fw-bold">Riwayat</span></a></li>
                <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
                <hr class="text-secondary">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a></li>
            </ul>
        </div>
        
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Catat Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">Formulir pencatatan pelanggaran siswa</p>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 p-4 mb-4">
                <form method="POST" action="catat_pelanggaran.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Wali Kelas Pencatat</label>
                        <input
                            type="text"
                            class="form-control bg-light"
                            value="<?= e($nama_guru . ($nama_kelas_wali !== '' ? ' (Wali Kelas ' . $nama_kelas_wali . ')' : '')) ?>"
                            readonly
                        >
                        <div class="form-text text-muted">Nama wali kelas terdeteksi otomatis sesuai akun yang login.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = $siswaList->fetch_assoc()): ?>
                                <option value="<?= (int) $s['id'] ?>">
                                    <?= e($s['nis'] . ' - ' . $s['nama'] . ' (' . $s['nama_kelas'] . ')') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Pelanggaran</label>
                        <select name="pelanggaran_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Pelanggaran --</option>
                            <?php while ($p = $pelanggaranList->fetch_assoc()): ?>
                                <option value="<?= (int) $p['id'] ?>">
                                    <?= e($p['kode'] . ' - ' . $p['nama'] . ' (' . $p['poin'] . ' poin)') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Kejadian</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan detail kronologi kejadian..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary text-white">Simpan Catatan</button>
                    <a href="dashboard.php" class="btn btn-dark">Kembali</a>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
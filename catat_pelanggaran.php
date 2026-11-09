<?php
session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE GURU
if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// KONEKSI DATABASE
$koneksi = $conn ?? $koneksi ?? null;

if (!$koneksi instanceof mysqli) {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php.");
}

$pesan = '';
$error = '';

$guru_id = 0;
$nama_guru = $_SESSION['name'] ?? 'Guru';

// === OTOMATISASI DATA GURU (MENCEGAH FOREIGN KEY ERROR) ===
$stmtGuru = $koneksi->prepare("SELECT id, nama FROM t_guru WHERE user_id = ? OR id = ? LIMIT 1");
$stmtGuru->bind_param("ii", $_SESSION['id_user'], $_SESSION['id_user']);
$stmtGuru->execute();
$dataGuru = $stmtGuru->get_result()->fetch_assoc();
$stmtGuru->close();

if ($dataGuru) {
    $guru_id = (int) $dataGuru['id'];
    $nama_guru = $dataGuru['nama'] ?: $nama_guru;
} else {
    $resultAnyGuru = $koneksi->query("SELECT id, nama FROM t_guru LIMIT 1");
    if ($resultAnyGuru && $resultAnyGuru->num_rows > 0) {
        $anyGuru = $resultAnyGuru->fetch_assoc();
        $guru_id = (int) $anyGuru['id'];
        $nama_guru = $anyGuru['nama'] ?: $nama_guru;
    } else {
        $uid = (int) $_SESSION['id_user'];
        $safe_nama = $koneksi->real_escape_string($nama_guru);
        $koneksi->query("INSERT INTO t_guru (user_id, nama, nip, status_aktif) VALUES ($uid, '$safe_nama', '12345', 1)");
        $guru_id = (int) $koneksi->insert_id;
    }
}
// =========================================================

// PROSES SIMPAN PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
        $tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
        $tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $tindakan = trim($_POST['tindakan'] ?? '');

        if ($siswa_id < 1 || $pelanggaran_id < 1 || $keterangan === '' || $tindakan === '') {
            throw new Exception("Lengkapi semua data terlebih dahulu.");
        }

        // VALIDASI TANGGAL
        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new Exception("Format tanggal tidak valid.");
        }

        // CEK SISWA
        $stmt = $koneksi->prepare("SELECT id, nama FROM t_siswa WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $siswa_id);
        $stmt->execute();
        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }

        // CEK KELAS SISWA
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

        // CEK JENIS PELANGGARAN
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

        $nama_siswa = $siswa['nama'];
        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) ($pelanggaran['pelanggaran_kategori_id'] ?? 0);
        $poin = (int) $pelanggaran['poin'];
        $status = 'tercatat';

        // SIMPAN KE DATABASE
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

        header("Location: Catat_Pelanggaran.php?berhasil=1");
        exit;

    } catch (Throwable $e) {
        $error = "Gagal menyimpan: " . $e->getMessage();
    }
}

if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
}

// DAFTAR SISWA
$siswaList = $koneksi->query(
    "SELECT s.id, s.nis, s.nama, COALESCE(MAX(k.nama), 'Tanpa Kelas') AS nama_kelas
    FROM t_siswa s
    LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON k.id = ks.kelas_id
    GROUP BY s.id, s.nis, s.nama
    ORDER BY s.id ASC"
);

// DAFTAR PELANGGARAN
$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white fw-bold mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dasboard</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Catat Pelanggaran</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Tindakan</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Rekap Poin</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Riwayat</h7> 
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Laporan</h7> 
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

            <h2>Catat Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">Formulir pencatatan pelanggaran dan tindakan disiplin siswa</p>

            <!-- ALERT BERHASIL -->
            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- ALERT ERROR -->
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- FORM CARD -->
            <div class="card shadow-sm border-0 p-4 mb-4">
                <form method="POST" action="Catat_Pelanggaran.php">

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
                        <label class="form-label fw-bold">Tindakan / Sanksi</label>
                        <textarea
                            name="tindakan"
                            class="form-control"
                            rows="2"
                            placeholder="Contoh: Teguran lisan / Panggilan Orang Tua / Skorsing 3 Hari"
                            required
                        ></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Kejadian</label>
                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Tambahan</label>
                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Catatan detail kronologi kejadian..."
                            required
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary text-white">Simpan Catatan</button>
                    <a href="guru.php" class="btn btn-secondary px-4">Kembali</a>

                </form>
            </div>

        </main>

    </div>
</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
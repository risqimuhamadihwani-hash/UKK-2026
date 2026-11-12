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

// Jika role wali kelas, samakan penanganan hak aksesnya seperti guru
if ($role === 'wali_kelas' || $role === 'walikelas') {
    $role = 'guru';
    $is_wali_kelas = true;
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

// PENANGANAN PESAN NOTIFIKASI
if (isset($_GET['berhasil'])) {
    if ($_GET['berhasil'] === '1' || $_GET['berhasil'] === 'tambah') {
        $pesan = "Catatan pelanggaran berhasil disimpan.";
    } elseif ($_GET['berhasil'] === 'edit') {
        $pesan = "Catatan pelanggaran berhasil diperbarui.";
    } elseif ($_GET['berhasil'] === 'hapus') {
        $pesan = "Catatan pelanggaran berhasil dihapus.";
    }
}

$session_id = (int)($_SESSION['id_user'] ?? 0);
$session_name = trim($_SESSION['name'] ?? ($_SESSION['nama'] ?? ''));

$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

// DETEKSI GURU & KELAS WALI KELAS SECARA AKURAT
$guru_id = 0;
$nama_guru = '';
$nama_kelas_wali = '';
$kelas_wali_id = 0;
$is_wali_kelas = false;

$stmtGuru = $koneksi->prepare(
    "SELECT g.id AS guru_id, g.nama AS nama_guru, k.id AS kelas_id, k.nama AS nama_kelas
     FROM t_guru g
     LEFT JOIN t_wali_kelas wk ON wk.guru_id = g.id AND wk.tahun_ajaran_id = ? AND wk.status_aktif = 1
     LEFT JOIN t_kelas k ON k.id = wk.kelas_id
     WHERE g.user_id = ? OR g.id = ?
     LIMIT 1"
);
$stmtGuru->bind_param("iii", $tahun_id, $session_id, $session_id);
$stmtGuru->execute();
$dataGuruLogin = $stmtGuru->get_result()->fetch_assoc();
$stmtGuru->close();

if ($dataGuruLogin && !empty($dataGuruLogin['guru_id'])) {
    $guru_id = (int) $dataGuruLogin['guru_id'];
    $nama_guru = $dataGuruLogin['nama_guru'];
    $kelas_wali_id = (int) ($dataGuruLogin['kelas_id'] ?? 0);
    $nama_kelas_wali = $dataGuruLogin['nama_kelas'] ?? '';
    if (!empty($nama_kelas_wali)) {
        $is_wali_kelas = true;
    }
} else {
    $guru_id = $session_id;
    $nama_guru = $session_name !== '' ? $session_name : 'Ari Sandriana, S.Kom';
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

        // Ambil Nama Siswa
        $stmt = $koneksi->prepare("SELECT id, nama FROM t_siswa WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $siswa_id);
        $stmt->execute();
        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }
        $nama_siswa = $siswa['nama'];

        // Ambil Data Kelas Berdasarkan Penempatan Siswa (t_kelas_siswa) yang Aktif
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
        $nama_kelas = ($kelasData && !empty($kelasData['nama_kelas'])) ? $kelasData['nama_kelas'] : '-';

        // Ambil Data Jenis Pelanggaran
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
        $status = 'Tercatat';
        $tindakan = ''; // Dikosongkan karena form tindakan dihilangkan

        // SIMPAN KE DATABASE
        $stmt = $koneksi->prepare(
            "INSERT INTO t_pelanggaran_siswa
            (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iisisiisiississ",
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

        header("Location: catatan_pelanggaran.php?berhasil=tambah");
        exit;

    } catch (Throwable $e) {
        $error = "Gagal menyimpan: " . $e->getMessage();
    }
}

// Ambil data siswa (Jika wali kelas, filter ketat hanya siswa di kelas walinya)
if ($role === 'guru' && $is_wali_kelas) {
    $stmtSiswa = $koneksi->prepare(
        "SELECT s.id, s.nis, s.nama, k.nama AS nama_kelas
         FROM t_siswa s
         JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
         JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE k.id = ?
         ORDER BY s.nama ASC"
    );
    $stmtSiswa->bind_param("ii", $tahun_id, $kelas_wali_id);
    $stmtSiswa->execute();
    $siswaList = $stmtSiswa->get_result();
} else {
    $siswaList = $koneksi->query(
        "SELECT s.id, s.nis, s.nama, COALESCE(MAX(k.nama), 'Tanpa Kelas') AS nama_kelas
         FROM t_siswa s
         LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.status_aktif = 1 AND ks.tahun_ajaran_id = $tahun_id
         LEFT JOIN t_kelas k ON k.id = ks.kelas_id
         GROUP BY s.id, s.nis, s.nama
         ORDER BY s.nama ASC"
    );
}

$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin 
     FROM t_pelanggaran 
     WHERE status_aktif = 1 
     ORDER BY CAST(SUBSTRING(kode, 4) AS UNSIGNED) ASC"
);

// QUERY RIWAYAT DENGAN JOIN AGAR NAMA KELAS & JENIS PELANGGARAN MUNCUL DENGAN BENAR
$riwayatList = $koneksi->query(
    "SELECT ps.*, 
            COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas_fix, 
            COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran_fix 
     FROM t_pelanggaran_siswa ps
     LEFT JOIN t_kelas k ON k.id = ps.kelas_id
     LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
     WHERE ps.tahun_ajaran_id = $tahun_id 
     ORDER BY ps.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Pelanggaran Siswa</title>
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
            <h2>Catatan Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">Selamat datang, <?= e($nama_guru) ?><?= $is_wali_kelas ? ' (Wali Kelas ' . e($nama_kelas_wali) . ')' : ' (Guru)' ?></p>

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

            <!-- FORM TAMBAH CATATAN PELANGGARAN -->
            <div class="card shadow-sm border-0 p-4 mb-4">
                <h5 class="fw-bold mb-3">Tambah Catatan Pelanggaran</h5>
                <form method="POST" action="catatan_pelanggaran.php">
                    <input type="hidden" name="guru_id" value="<?= (int)$guru_id ?>">

                    <div class="row g-3 align-items-center mb-3">
                        <!-- Pilihan Kelas (Terkunci jika Wali Kelas) -->
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Kelas</label>
                            <select id="filterKelas" class="form-select form-select-sm" <?= ($role === 'guru' && $is_wali_kelas) ? 'disabled style="background-color: #e9ecef;"' : '' ?>>
                                <option value="<?= e($nama_kelas_wali) ?>" selected><?= e($nama_kelas_wali !== '' ? $nama_kelas_wali : '-- Kelas --') ?></option>
                            </select>
                        </div>

                        <!-- Pilih Siswa dengan Select2 -->
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Siswa</label>
                            <select name="siswa_id" id="selectSiswa" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php while ($s = $siswaList->fetch_assoc()): ?>
                                    <option value="<?= (int) $s['id'] ?>" data-kelas="<?= e($s['nama_kelas']) ?>">
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
                                    <option value="<?= (int) $p['id'] ?>">
                                        <?= e($p['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Tanggal Kejadian -->
                        <div class="col-md-2">
                            <label class="form-label text-muted small mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <!-- Catatan & Tombol Simpan -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-10">
                            <label class="form-label text-muted small mb-1">Catatan</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan detail kronologi kejadian..." required></textarea>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 text-white py-2">Simpan Catatan</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR CATATAN PELANGGARAN -->
            <div class="card shadow-sm border-0 p-4">
                <h5 class="fw-bold mb-3">Daftar Catatan Pelanggaran</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Poin</th>
                                <th>Catatan</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($riwayatList && $riwayatList->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $riwayatList->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= !empty($row['tanggal']) ? e(date('d-m-Y', strtotime($row['tanggal']))) : '-' ?></td>
                                        <td><?= e($row['nama_siswa']) ?></td>
                                        <td><?= e($row['nama_kelas_fix']) ?></td>
                                        <td><?= e($row['nama_pelanggaran_fix']) ?></td>
                                        <td><span class="badge bg-danger"><?= (int)$row['poin'] ?></span></td>
                                        <td><?= e($row['keterangan']) ?></td>
                                        <td><?= e($row['tindakan']) ?></td>
                                        <td>
                                            <?php 
                                                $st = strtolower($row['status']);
                                                $badgeClass = 'bg-secondary';
                                                if ($st === 'selesai') $badgeClass = 'bg-success';
                                                elseif ($st === 'diproses') $badgeClass = 'bg-warning text-dark';
                                                elseif ($st === 'tercatat') $badgeClass = 'bg-info text-dark';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= e(ucfirst($row['status'])) ?></span>
                                        </td>
                                        <td>
                                            <a href="edit_catatan_pelanggaran.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning btn-sm text-dark">Edit</a>
                                            <a href="hapus_catatan_pelanggaran.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus catatan ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-3">Belum ada catatan pelanggaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
    $('#selectPelanggaran').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
</body>
</html>
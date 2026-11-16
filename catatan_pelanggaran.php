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

$pesan = '';
$error = '';

// PENANGANAN PESAN NOTIFIKASI & ERROR
if (isset($_GET['berhasil'])) {
    if ($_GET['berhasil'] === '1' || $_GET['berhasil'] === 'tambah') {
        $pesan = "Catatan pelanggaran berhasil disimpan dan dikirim ke menu Tindakan.";
    } elseif ($_GET['berhasil'] === 'edit') {
        $pesan = "Catatan pelanggaran berhasil diperbarui.";
    } elseif ($_GET['berhasil'] === 'hapus') {
        $pesan = "Catatan pelanggaran berhasil dihapus.";
    }
}

if (isset($_GET['error']) && $_GET['error'] === 'selesai') {
    $error = "Akses ditolak: Catatan pelanggaran yang sudah berstatus 'Selesai' terkunci dan tidak dapat diubah atau dihapus.";
}

$session_name = trim($_SESSION['name'] ?? ($_SESSION['nama'] ?? ''));

$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

// DETEKSI GURU & KELAS WALI KELAS SECARA AKURAT BERDASARKAN USER ID
$guru_id = 0;
$nama_guru = '';
$nama_kelas_wali = '';
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
        $nama_kelas_wali = $resWali['nama_kelas'];
        $is_wali_kelas = true;
    }
} else {
    $guru_id = 0;
    $nama_guru = $session_name !== '' ? $session_name : '';
}

if ($is_session_wali_kelas) {
    $is_wali_kelas = true;
}

$guruListAll = $koneksi->query("SELECT id, nama FROM t_guru ORDER BY nama ASC");

// PROSES SIMPAN PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');
        
        if ($is_wali_kelas && $role !== 'admin') {
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
        $nama_kelas = ($kelasData && !empty($kelasData['nama_kelas'])) ? $kelasData['nama_kelas'] : '-';

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
        $status = 'tercatat'; // Status awal saat dicatat masuk ke Tindakan
        $tindakan = ''; 

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

// AMBIL DATA SISWA
if ($is_wali_kelas && $kelas_wali_id > 0) {
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
    $stmtSiswa = $koneksi->prepare(
        "SELECT s.id, s.nis, s.nama, k.nama AS nama_kelas
         FROM t_siswa s
         JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
         JOIN t_kelas k ON k.id = ks.kelas_id
         ORDER BY s.nama ASC"
    );
    $stmtSiswa->bind_param("i", $tahun_id);
    $stmtSiswa->execute();
    $siswaList = $stmtSiswa->get_result();
}

$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin 
     FROM t_pelanggaran 
     WHERE status_aktif = 1 
     ORDER BY nama ASC"
);

$cari_siswa = trim($_GET['cari_siswa'] ?? '');

// QUERY DAFTAR CATATAN (Menampilkan riwayat input di halaman ini)
if ($is_wali_kelas && $role !== 'admin' && $kelas_wali_id > 0) {
    if ($cari_siswa !== '') {
        $stmtRiwayat = $koneksi->prepare(
            "SELECT ps.*, 
                    COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas_fix, 
                    COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran_fix 
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_kelas k ON k.id = ps.kelas_id
             LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
             WHERE ps.tahun_ajaran_id = ? AND ps.kelas_id = ? AND ps.nama_siswa LIKE ?
             ORDER BY ps.id DESC"
        );
        $keyword = "%$cari_siswa%";
        $stmtRiwayat->bind_param("iis", $tahun_id, $kelas_wali_id, $keyword);
    } else {
        $stmtRiwayat = $koneksi->prepare(
            "SELECT ps.*, 
                    COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas_fix, 
                    COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran_fix 
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_kelas k ON k.id = ps.kelas_id
             LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
             WHERE ps.tahun_ajaran_id = ? AND ps.kelas_id = ?
             ORDER BY ps.id DESC"
        );
        $stmtRiwayat->bind_param("ii", $tahun_id, $kelas_wali_id);
    }
    $stmtRiwayat->execute();
    $riwayatList = $stmtRiwayat->get_result();
} else {
    if ($cari_siswa !== '') {
        $stmtRiwayat = $koneksi->prepare(
            "SELECT ps.*, 
                    COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas_fix, 
                    COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran_fix 
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_kelas k ON k.id = ps.kelas_id
             LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
             WHERE ps.tahun_ajaran_id = ? AND ps.nama_siswa LIKE ?
             ORDER BY ps.id DESC"
        );
        $keyword = "%$cari_siswa%";
        $stmtRiwayat->bind_param("is", $tahun_id, $keyword);
    } else {
        $stmtRiwayat = $koneksi->prepare(
            "SELECT ps.*, 
                    COALESCE(k.nama, ps.nama_kelas, '-') AS nama_kelas_fix, 
                    COALESCE(p.nama, ps.nama_pelanggaran, '-') AS nama_pelanggaran_fix 
             FROM t_pelanggaran_siswa ps
             LEFT JOIN t_kelas k ON k.id = ps.kelas_id
             LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
             WHERE ps.tahun_ajaran_id = ?
             ORDER BY ps.id DESC"
        );
        $stmtRiwayat->bind_param("i", $tahun_id);
    }
    $stmtRiwayat->execute();
    $riwayatList = $stmtRiwayat->get_result();
}
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
                
                <?php if ($is_wali_kelas || $role === 'admin'): ?>
                    <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
                <?php endif; ?>

                <hr class="text-secondary">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a></li>
            </ul>
        </div>
        
        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Catatan Pelanggaran Siswa</h2>
            <p class="text-muted mb-4">Selamat datang, <?= e($nama_guru !== '' ? $nama_guru : ucfirst($role)) ?><?= $is_wali_kelas && !empty($nama_kelas_wali) ? ' (Wali Kelas ' . e($nama_kelas_wali) . ')' : ' (' . ucfirst($role) . ')' ?></p>

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
                    
                    <div class="row g-3 align-items-center mb-3">
                        <!-- Pilihan Guru Pelapor -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Guru Pelapor</label>
                            <?php if ($is_wali_kelas && $role !== 'admin'): ?>
                                <input type="hidden" name="guru_id" value="<?= (int)$guru_id ?>">
                                <input type="text" class="form-control form-control-sm bg-light fw-bold" value="<?= e($nama_guru) ?>" readonly>
                            <?php else: ?>
                                <select name="guru_id" id="selectGuru" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Guru Pelapor --</option>
                                    <?php 
                                    if ($guruListAll) {
                                        $guruListAll->data_seek(0);
                                        while ($g = $guruListAll->fetch_assoc()) {
                                            $selected = ((int)$g['id'] === $guru_id) ? 'selected' : '';
                                            echo '<option value="' . (int)$g['id'] . '" ' . $selected . '>' . e($g['nama']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Pilih Siswa dengan Select2 -->
                        <div class="col-md-5">
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

                        <!-- Tanggal Kejadian -->
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center mb-3">
                        <!-- Pilih Jenis Pelanggaran -->
                        <div class="col-md-12">
                            <label class="form-label text-muted small mb-1">Jenis Pelanggaran</label>
                            <select name="pelanggaran_id" id="selectPelanggaran" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Pelanggaran --</option>
                                <?php while ($p = $pelanggaranList->fetch_assoc()): ?>
                                    <option value="<?= (int) $p['id'] ?>">
                                        <?= e($p['kode'] ? $p['kode'] . ' - ' : '') ?><?= e($p['nama']) ?> (Poin: <?= (int)$p['poin'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0">Daftar Catatan Pelanggaran</h5>
                    <!-- Form Pencarian Siswa -->
                    <form method="GET" class="d-flex gap-2" style="width: 300px;">
                        <input type="text" name="cari_siswa" class="form-control form-control-sm" placeholder="Cari nama siswa..." value="<?= e($cari_siswa) ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap">Cari</button>
                        <?php if ($cari_siswa !== ''): ?>
                            <a href="catatan_pelanggaran.php" class="btn btn-outline-secondary btn-sm text-nowrap">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

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
                                        <td>
                                            <?php 
                                                $st = strtolower($row['status']);
                                                $badgeClass = 'bg-secondary';
                                                if ($st === 'selesai') $badgeClass = 'bg-success';
                                                elseif ($st === 'diproses') $badgeClass = 'bg-warning text-dark';
                                                elseif ($st === 'tercatat') $badgeClass = 'bg-info text-white';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= e(ucfirst($row['status'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($st === 'selesai'): ?>
                                                <span class="text-muted small fst-italic">Terkunci</span>
                                            <?php else: ?>
                                                <a href="edit_catatan_pelanggaran.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning btn-sm text-dark">Edit</a>
                                                <a href="hapus_catatan_pelanggaran.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus catatan ini?')">Hapus</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">Belum ada catatan pelanggaran untuk kelas Anda atau siswa yang dicari tidak ditemukan.</td>
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
    if ($('#selectGuru').length) {
        $('#selectGuru').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
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
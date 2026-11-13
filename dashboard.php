<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Cek koneksi database
if (file_exists('config/koneksi.php')) {
    require_once 'config/koneksi.php';
}

if (!isset($koneksi) || $koneksi->connect_error) {
    $koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");
    if ($koneksi->connect_error) {
        die("Koneksi database gagal: " . $koneksi->connect_error);
    }
}

// 1. CEK APAKAH USER SUDAH LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? 'User';
$email = $_SESSION['email'] ?? '-';
$role = strtolower(trim($_SESSION['role'] ?? ''));

// 2. VALIDASI ROLE (Izinkan admin, guru, dan wali_kelas)
if ($role !== 'admin' && $role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika role di database adalah wali_kelas, perlakukan hak aksesnya seperti guru/wali kelas
$is_wali_kelas = false;
if ($role === 'wali_kelas' || $role === 'walikelas') {
    $is_wali_kelas = true;
    $role = 'guru'; // Menggunakan hak akses menu guru
}

// Cek apakah guru/wali kelas ini memegang kelas tertentu
$nama_kelas_wali = '';

// Ambil tahun ajaran aktif
$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

$session_email = trim($_SESSION['email'] ?? '');

// Cari data kelas wali berdasarkan email atau nama
$stmtWali = $koneksi->prepare(
    "SELECT k.nama AS nama_kelas 
     FROM t_guru g
     JOIN t_wali_kelas wk ON wk.guru_id = g.id AND wk.tahun_ajaran_id = ? AND wk.status_aktif = 1
     JOIN t_kelas k ON k.id = wk.kelas_id
     WHERE g.email = ? OR g.nama LIKE ? OR g.id = ?
     LIMIT 1"
);
$search_name = "%" . $name . "%";
$session_id = (int)($_SESSION['id_user'] ?? 0);

$stmtWali->bind_param("issi", $tahun_id, $session_email, $search_name, $session_id);
$stmtWali->execute();
$resWali = $stmtWali->get_result()->fetch_assoc();
$stmtWali->close();

if ($resWali && !empty($resWali['nama_kelas'])) {
    $nama_kelas_wali = $resWali['nama_kelas'];
    $is_wali_kelas = true;
}

// 3. AMBIL DATA STATISTIK BERDASARKAN ROLE
$total_pelanggaran = 0;
$total_tercatat = 0;
$total_diproses = 0;
$total_selesai = 0;

$jml_siswa = 0;
$jml_guru = 0;
$jml_kelas = 0;
$jml_pelanggaran_admin = 0;

if ($role === 'guru') {
    // Ringkasan untuk Guru/Wali Kelas dari tabel t_pelanggaran_siswa
    $query_ringkasan = mysqli_query($koneksi, "
        SELECT
            COUNT(*) AS total_pelanggaran,
            SUM(CASE WHEN LOWER(status) = 'tercatat' THEN 1 ELSE 0 END) AS tercatat,
            SUM(CASE WHEN LOWER(status) = 'diproses' THEN 1 ELSE 0 END) AS diproses,
            SUM(CASE WHEN LOWER(status) = 'selesai' THEN 1 ELSE 0 END) AS selesai
        FROM t_pelanggaran_siswa
    ");
    if ($query_ringkasan) {
        $ringkasan = mysqli_fetch_assoc($query_ringkasan);
        $total_pelanggaran = $ringkasan['total_pelanggaran'] ?? 0;
        $total_tercatat = $ringkasan['tercatat'] ?? 0;
        $total_diproses = $ringkasan['diproses'] ?? 0;
        $total_selesai = $ringkasan['selesai'] ?? 0;
    }
} else {
    // Ringkasan untuk Admin
    function hitungData($koneksi, $tabel) {
        $daftarTabel = ['t_siswa', 't_guru', 't_kelas', 't_pelanggaran'];
        if (!in_array($tabel, $daftarTabel, true)) {
            return 0;
        }
        $hasil = $koneksi->query("SELECT COUNT(*) AS jumlah FROM `$tabel`");
        if ($hasil) {
            return (int) $hasil->fetch_assoc()['jumlah'];
        }
        return 0;
    }

    $jml_siswa = hitungData($koneksi, 't_siswa');
    $jml_guru  = hitungData($koneksi, 't_guru');
    $jml_kelas = hitungData($koneksi, 't_kelas');
    $jml_pelanggaran_admin = hitungData($koneksi, 't_pelanggaran');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard <?= $is_wali_kelas ? 'Wali Kelas' : ucfirst($role) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR DINAMIS -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
            <h4 class="text-white fw-bold mb-4">Pelanggaran Siswa</h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active">
                        <span class="fw-bold">Dashboard</span>
                    </a>
                </li>

<?php if ($role === 'guru'): ?>
        <!-- MENU KHUSUS GURU / WALI KELAS -->
        <li class="nav-item mb-2"><a href="catatan_pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Catatan Pelanggaran</span></a></li>
        <li class="nav-item mb-2"><a href="Tindakan.php" class="nav-link text-white"><span class="fw-bold">Tindakan</span></a></li>
        <li class="nav-item mb-2"><a href="rekap_poin.php" class="nav-link text-white"><span class="fw-bold">Rekap Poin</span></a></li>
        <li class="nav-item mb-2"><a href="riwayat.php" class="nav-link text-white"><span class="fw-bold">Riwayat</span></a></li>
        
        <!-- MENU LAPORAN HANYA MUNCUL JIKA DIA WALI KELAS -->
        <?php if ($is_wali_kelas): ?>
            <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
        <?php endif; ?>
       
    <?php else: ?>
                    <!-- MENU KHUSUS ADMIN -->
                    <li class="nav-item mb-2"><a href="Kelola_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Siswa</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Guru.php" class="nav-link text-white"><span class="fw-bold">Kelola Guru</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Kelas.php" class="nav-link text-white"><span class="fw-bold">Kelola Kelas</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Tahun Ajaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Penempatan Siswa</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Wali_Kelas.php" class="nav-link text-white"><span class="fw-bold">Kelola Wali Kelas</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Kategori Pelanggaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Jenis Pelanggaran</span></a></li>
                    <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
                    <li class="nav-item mb-2"><a href="about_me.php" class="nav-link text-white"><span class="fw-bold">About Me</span></a></li>
                <?php endif; ?>

                <hr class="text-secondary">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a></li>
            </ul>
        </div>
        
        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2 class="fw-bold mb-3">
                Dashboard <?= $is_wali_kelas ? 'Wali Kelas ' . htmlspecialchars($nama_kelas_wali, ENT_QUOTES, 'UTF-8') : ucfirst($role) ?>
            </h2>

            <!-- INFORMASI USER -->
            <div class="mb-4 text-secondary">
                Selamat datang, <span class="fw-bold text-dark"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span><br>
                Email: <span class="text-dark"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span><br>
                Role: <span class="badge bg-primary px-2 py-1"><?= $role === 'guru' ? ($is_wali_kelas ? 'Wali Kelas' . (!empty($nama_kelas_wali) ? ' (' . htmlspecialchars($nama_kelas_wali, ENT_QUOTES, 'UTF-8') . ')' : '') : 'Guru') : htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <?php if ($role === 'guru'): ?>
                <!-- STATISTIK GURU / WALI KELAS -->
                <div class="row mt-4 mb-4">
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0"><div class="card-body">
                        <h6 class="text-muted">Total Pelanggaran</h6>
                        <h3 class="fw-bold mb-0 text-primary"><?= number_format($total_pelanggaran) ?></h3>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0"><div class="card-body">
                        <h6 class="text-muted">Tercatat</h6>
                        <h3 class="fw-bold mb-0 text-secondary"><?= number_format($total_tercatat) ?></h3>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0"><div class="card-body">
                        <h6 class="text-muted">Diproses</h6>
                        <h3 class="fw-bold mb-0 text-warning"><?= number_format($total_diproses) ?></h3>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0"><div class="card-body">
                        <h6 class="text-muted">Selesai</h6>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($total_selesai) ?></h3>
                    </div></div></div>
                </div>
            <?php else: ?>
                <!-- STATISTIK ADMIN -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0 p-2"><div class="card-body">
                        <h6 class="card-title text-muted fw-normal">Data Siswa</h6>
                        <h2 class="display-6 fw-bold text-primary mb-0"><?= $jml_siswa ?></h2>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0 p-2"><div class="card-body">
                        <h6 class="card-title text-muted fw-normal">Data Guru</h6>
                        <h2 class="display-6 fw-bold text-success mb-0"><?= $jml_guru ?></h2>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0 p-2"><div class="card-body">
                        <h6 class="card-title text-muted fw-normal">Data Kelas</h6>
                        <h2 class="display-6 fw-bold text-warning mb-0"><?= $jml_kelas ?></h2>
                    </div></div></div>
                    <div class="col-md-3 mb-3"><div class="card shadow-sm border-0 p-2"><div class="card-body">
                        <h6 class="card-title text-muted fw-normal">Catatan Pelanggaran</h6>
                        <h2 class="display-6 fw-bold text-danger mb-0"><?= $jml_pelanggaran_admin ?></h2>
                    </div></div></div>
                </div>
            <?php endif; ?>

            <!-- ALUR PENGGUNAAN -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Alur Penggunaan</h5>
                    <p class="text-muted mb-0">
                        <?php if ($role === 'guru'): ?>
                            Pilih menu di sidebar untuk mencatat pelanggaran, memproses tindakan, melihat rekap poin, riwayat, dan laporan pelanggaran kelas yang diwalikelaskan.
                        <?php else: ?>
                            Pilih menu di sidebar untuk mengelola data, mencatat pelanggaran, melihat riwayat, serta mencetak laporan sesuai hak akses akun.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Sembunyikan error mentah di production untuk keamanan hosting
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

// VALIDASI ROLE: Hanya admin yang diizinkan mengelola/mengedit kelas
if ($role !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: Kelola_Kelas.php");
    exit;
}

// AMBIL DATA KELAS
$stmt = $koneksi->prepare("SELECT id, nama, tingkat, jurusan, status_aktif FROM t_kelas WHERE id = ?");
if (!$stmt) {
    die("Query gagal: " . htmlspecialchars($koneksi->error, ENT_QUOTES, 'UTF-8'));
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$kelas = $result->fetch_assoc();
$stmt->close();

if (!$kelas) {
    header("Location: Kelola_Kelas.php");
    exit;
}

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    if ($nama === '' || $tingkat === '' || $jurusan === '') {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
        $pesan = "Tingkat kelas tidak valid.";
    } elseif (!in_array($status_aktif, ['0', '1'], true)) {
        $pesan = "Status kelas tidak valid.";
    } else {
        $status_aktif = (int)$status_aktif;

        $stmtUpdate = $koneksi->prepare("UPDATE t_kelas SET nama = ?, tingkat = ?, jurusan = ?, status_aktif = ?, updated_at = NOW() WHERE id = ?");
        
        if (!$stmtUpdate) {
            $pesan = "Query update gagal: " . $koneksi->error;
        } else {
            $stmtUpdate->bind_param("sssii", $nama, $tingkat, $jurusan, $status_aktif, $id);

            if ($stmtUpdate->execute()) {
                $stmtUpdate->close();
                header("Location: Kelola_Kelas.php?pesan=diubah");
                exit;
            } else {
                $pesan = "Gagal mengubah data kelas: " . $stmtUpdate->error;
            }
            $stmtUpdate->close();
        }
    }

    // PERTAHANKAN INPUT JIKA VALIDASI GAGAL
    $kelas['nama'] = $nama;
    $kelas['tingkat'] = $tingkat;
    $kelas['jurusan'] = $jurusan;
    $kelas['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas - Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
            <h4 class="text-white fw-bold mb-4">Pelanggaran Siswa</h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link text-white"><span class="fw-bold">Dashboard</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Siswa</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Guru.php" class="nav-link text-white"><span class="fw-bold">Kelola Guru</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Kelas.php" class="nav-link active"><span class="fw-bold">Kelola Kelas</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Tahun Ajaran</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white"><span class="fw-bold">Kelola Penempatan Siswa</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Wali_Kelas.php" class="nav-link text-white"><span class="fw-bold">Kelola Wali Kelas</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Kategori Pelanggaran</span></a></li>
                <li class="nav-item mb-2"><a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Kelola Jenis Pelanggaran</span></a></li>
                <li class="nav-item mb-2"><a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a></li>
                <li class="nav-item mb-2"><a href="about_me.php" class="nav-link text-white"><span class="fw-bold">About Me</span></a></li>
                <hr class="text-secondary">
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a></li>
            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h3 class="fw-bold mb-4">Edit Kelas</h3>

            <div class="card shadow-sm border bg-white border-light">
                <div class="card-body p-4">

                    <!-- PESAN ERROR -->
                    <?php if ($pesan !== ''): ?>
                        <div class="alert alert-danger py-2">
                            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM EDIT KELAS -->
                    <form method="POST" action="edit_kelas.php?id=<?= (int)$id ?>" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                   placeholder="Contoh: X RPL 1" maxlength="100"
                                   value="<?= htmlspecialchars($kelas['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="X" <?= $kelas['tingkat'] === 'X' ? 'selected' : '' ?>>X (Sepuluh)</option>
                                <option value="XI" <?= $kelas['tingkat'] === 'XI' ? 'selected' : '' ?>>XI (Sebelas)</option>
                                <option value="XII" <?= $kelas['tingkat'] === 'XII' ? 'selected' : '' ?>>XII (Dua Belas)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control"
                                   placeholder="Contoh: RPL, TSM, TKJ" maxlength="100"
                                   value="<?= htmlspecialchars($kelas['jurusan'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= (string)$kelas['status_aktif'] === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= (string)$kelas['status_aktif'] === '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary px-3 py-1 text-white">Simpan Perubahan</button>
                            <a href="Kelola_Kelas.php" class="btn btn-secondary px-3 py-1">Kembali</a>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
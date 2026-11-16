<?php
session_start();
require_once 'auth.php'; 
require_once 'config.php'; 

if (function_exists('requireRole')) {
    requireRole(['admin']);
}

$title = 'Tambah Penempatan Siswa';
$section = 'admin';

$db_pdo = $pdo ?? ($conn instanceof PDO ? $conn : null);

// Tahun ajaran aktif (otomatis, hanya satu yang dipakai).
$tahun_aktif = null;
if ($db_pdo) {
    try {
        $tahun_aktif = $db_pdo->query("SELECT id, nama, tanggal_mulai, tanggal_selesai FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY tanggal_mulai DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Exception $e) {
        $tahun_aktif = null;
    }
}

if (isset($_POST['submit'])) {
    $siswa_id        = trim($_POST['siswa_id'] ?? '');
    $kelas_id        = trim($_POST['kelas_id'] ?? '');
    $tahun_ajaran_id = $tahun_aktif['id'] ?? '';
    $status_aktif    = isset($_POST['status_aktif']) ? 1 : 0;

    if (!$tahun_aktif) {
        echo "<script>alert('Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.');</script>";
    } elseif ($db_pdo) {
        try {
            $stmt = $db_pdo->prepare("INSERT INTO t_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $insert = $stmt->execute([$siswa_id, $kelas_id, $tahun_ajaran_id, $tahun_aktif['tanggal_mulai'], $tahun_aktif['tanggal_selesai'], $status_aktif]);
            
            if ($insert) {
                echo "<script>alert('Data penempatan siswa berhasil ditambahkan!'); window.location='penempatan_siswa.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data penempatan siswa.');</script>";
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Koneksi database PDO belum terhubung.');</script>";
    }
}

// Ambil data untuk dropdown (Siswa, Kelas, Tahun Ajaran)
$siswa_list = [];
$kelas_list = [];

if ($db_pdo) {
    try {
        // Tambahkan filter NOT IN untuk mengecualikan siswa yang sudah ditempatkan
        $siswa_list = $db_pdo->query("SELECT id, nama FROM t_siswa WHERE status_aktif = 1 AND id NOT IN (SELECT siswa_id FROM t_kelas_siswa) ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        $kelas_list = $db_pdo->query("SELECT id, nama FROM t_kelas WHERE status_aktif = 1 ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Abaikan jika tabel belum ada / error query dropdown
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body {
            background-color: #f4f4f4;
            overflow-x: hidden;
            margin: 0;
        }
        .content-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .nav-pills .nav-link.active {
            background-color: #00bcd4 !important;
            color: #ffffff !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container-fluid d-flex p-0" style="min-height: 100vh;">
    <div class="row flex-grow-1 g-0 w-100">

        <!-- Memanggil Sidebar Terpusat -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN UTAMA FORM TAMBAH PENEMPATAN SISWA -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="content-card shadow-sm">
                <div class="mb-3">
                    <h2>Tambah Penempatan Siswa</h2>
                    <p class="text-muted mb-0">Formulir penempatan siswa ke dalam kelas dan tahun ajaran.</p>
                </div>
                <hr>

                <form action="" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="siswa_id" class="form-label fw-bold">Pilih Siswa</label>
                        <select class="form-select" id="siswa_id" name="siswa_id" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($siswa_list as $s): ?>
                                <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="kelas_id" class="form-label fw-bold">Pilih Kelas</label>
                        <select class="form-select" id="kelas_id" name="kelas_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= (int)$k['id'] ?>"><?= htmlspecialchars($k['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label fw-bold">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun_ajaran" value="<?= htmlspecialchars($tahun_aktif['nama'] ?? 'Belum ada tahun ajaran aktif', ENT_QUOTES, 'UTF-8') ?>" readonly>
                        <?php if (!$tahun_aktif): ?><div class="form-text text-danger">Aktifkan satu tahun ajaran terlebih dahulu.</div><?php endif; ?>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" value="1" checked>
                        <label class="form-check-label fw-bold" for="status_aktif">Status Aktif</label>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-warning fw-semibold px-4 text-dark" <?= !$tahun_aktif ? 'disabled' : '' ?>>Simpan</button>
                        <a href="penempatan_siswa.php" class="btn btn-secondary fw-semibold px-4 ms-2">Kembali</a>
                    </div>
                </form>
            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
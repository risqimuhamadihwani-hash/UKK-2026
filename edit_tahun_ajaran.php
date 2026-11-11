<?php
session_start();
require_once "config/koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID tahun ajaran tidak valid.");
}

$stmt = $koneksi->prepare("SELECT * FROM t_tahun_ajaran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$tahun = $result->fetch_assoc();

$stmt->close();

if (!$tahun) {
    die("Data tahun ajaran tidak ditemukan.");
}

$error = "";

$nama = $tahun['nama'];
$tanggal_mulai = $tahun['tanggal_mulai'];
$tanggal_selesai = $tahun['tanggal_selesai'];
$status_aktif = (string)$tahun['status_aktif'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {

    $nama = trim($_POST["nama"] ?? "");
    $tanggal_mulai = trim($_POST["tanggal_mulai"] ?? "");
    $tanggal_selesai = trim($_POST["tanggal_selesai"] ?? "");
    $status_aktif = $_POST["status_aktif"] ?? "";

    if ($nama === "" || $tanggal_mulai === "" || $tanggal_selesai === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!in_array($status_aktif, ["0", "1"], true)) {
        $error = "Status aktif tidak valid.";
    } else {
        // ATURAN NO 5: Cek jika status yang diubah menjadi '1' (Aktif)
        if ($status_aktif === "1") {
            // Cek apakah sudah ada tahun ajaran lain (selain ID ini) yang berstatus aktif
            $stmt_cek = $koneksi->prepare("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 AND id != ?");
            $stmt_cek->bind_param("i", $id);
            $stmt_cek->execute();
            $stmt_cek->store_result();

            if ($stmt_cek->num_rows > 0) {
                $error = "Gagal: Sudah ada tahun ajaran lain yang berstatus Aktif. Tidak boleh ada status aktif lebih dari 1.";
            }
            $stmt_cek->close();
        }

        // Jika tidak ada error, lanjutkan proses update
        if ($error === "") {
            $stmt = $koneksi->prepare("
                UPDATE t_tahun_ajaran
                SET nama = ?, tanggal_mulai = ?, tanggal_selesai = ?, status_aktif = ?
                WHERE id = ?
            ");

            if (!$stmt) {
                die("Query gagal: " . $koneksi->error);
            }

            $statusInt = (int)$status_aktif;

            $stmt->bind_param(
                "sssii",
                $nama,
                $tanggal_mulai,
                $tanggal_selesai,
                $statusInt,
                $id
            );

            if ($stmt->execute()) {
                $stmt->close();
                $koneksi->close();

                header("Location: Kelola_Tahun_Ajaran.php?pesan=diubah");
                exit;
            } else {
                $error = "Gagal mengubah data tahun ajaran: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

function e($nilai) {
    return htmlspecialchars((string)($nilai ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tahun Ajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
            <h4 class="text-white fw-bold mb-4 ">
            Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Dashboard</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Guru</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Kelola Tahun Ajaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Penempatan Siswa</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Wali Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kategori Pelanggaran</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Jenis Pelanggaran</h7> 
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

            <h3 class="fw-bold mb-4">Edit Tahun Ajaran</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Tahun Ajaran</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Contoh: 2026/2027"
                                value="<?= e($nama) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="<?= e($tanggal_mulai) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="<?= e($tanggal_selesai) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $status_aktif === "1" ? "selected" : "" ?>>
                                    Aktif
                                </option>
                                <option value="0" <?= $status_aktif === "0" ? "selected" : "" ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="update" class="btn btn-primary px-3 py-1 text-white" style="background-color: #0d6efd;">
                                Simpan Perubahan
                            </button>

                            <a href="Kelola_Tahun_Ajaran.php" class="btn btn-secondary px-3 py-1" style="background-color: #6c757d;">
                                Kembali
                            </a>
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
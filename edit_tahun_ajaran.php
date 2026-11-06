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

            header("Location: menu4.php?pesan=diubah");
            exit;
        } else {
            $error = "Gagal mengubah data tahun ajaran: " . $stmt->error;
        }

        $stmt->close();
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
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                JusticeAdmin
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link active">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                 <li class="nav-item mb-2">
                    <a href="Penempatan_Siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link text-white">
                        Cetak/Export
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Edit Tahun Ajaran</h1>

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
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

                <div class="mb-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        class="form-control"
                        value="<?= e($tanggal_mulai) ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input
                        type="date"
                        name="tanggal_selesai"
                        class="form-control"
                        value="<?= e($tanggal_selesai) ?>"
                        required
                    >
                </div>

                <div class="mb-4">
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

                <button type="submit" name="update" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="menu4.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </main>

    </div>
</div>

</body>
</html>
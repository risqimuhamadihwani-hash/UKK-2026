<?php
session_start();
require_once "config/koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID guru tidak valid.");
}

$stmt = $koneksi->prepare("SELECT * FROM t_guru WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$guru = $result->fetch_assoc();

$stmt->close();

if (!$guru) {
    die("Data guru tidak ditemukan.");
}

$error = "";

$nip = $guru['nip'];
$nama = $guru['nama'];
$email = $guru['email'] ?? "";
$status = (string)$guru['status_aktif'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {

    $nip = trim($_POST["nip"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $status = $_POST["status_aktif"] ?? "";

    if ($nip === "" || $nama === "") {
        $error = "NIP dan Nama Guru wajib diisi.";
    } elseif (!ctype_digit($nip) || strlen($nip) > 18) {
        $error = "NIP hanya boleh berisi angka dan maksimal 18 karakter.";
    } elseif (!in_array($status, ["0", "1"], true)) {
        $error = "Status guru tidak valid.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {

        // Cek apakah NIP sudah digunakan oleh guru lain di database
        $stmtCek = $koneksi->prepare("SELECT id FROM t_guru WHERE nip = ? AND id != ?");
        $stmtCek->bind_param("si", $nip, $id);
        $stmtCek->execute();
        $stmtCek->store_result();

        if ($stmtCek->num_rows > 0) {
            $error = "NIP sudah terdaftar oleh guru lain. Gunakan NIP yang berbeda.";
            $stmtCek->close();
        } else {
            $stmtCek->close();

            $stmt = $koneksi->prepare("
                UPDATE t_guru
                SET nip = ?, nama = ?, email = NULLIF(?, ''), status_aktif = ?
                WHERE id = ?
            ");

            if (!$stmt) {
                die("Query gagal: " . $koneksi->error);
            }

            $statusInt = (int)$status;

            $stmt->bind_param(
                "sssii",
                $nip,
                $nama,
                $email,
                $statusInt,
                $id
            );

            if ($stmt->execute()) {
                $stmt->close();
                $koneksi->close();

                header("Location: Kelola_Guru.php?pesan=diubah");
                exit;

            } else {
                if ($stmt->errno == 1062) {
                    $error = "NIP atau data unik lainnya sudah terdaftar.";
                } else {
                    $error = "Gagal mengubah data guru: " . $stmt->error;
                }
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
    <title>Edit Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white fw-bold mb-4">
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
                    <a href="Kelola_Guru.php" class="nav-link active">
                    <h7 class="fw-bold mb-4">Kelola Guru</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Kelas</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
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

            <h3 class="fw-bold mb-4">Edit Guru</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIP (Maksimal 18 Digit Angka)</label>
                            <input
                                type="text"
                                name="nip"
                                class="form-control"
                                placeholder="Masukkan NIP (maks. 18 digit)"
                                value="<?= e($nip) ?>"
                                maxlength="18"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Guru</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan Nama Guru"
                                value="<?= e($nama) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Masukkan Email"
                                value="<?= e($email) ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $status === "1" ? "selected" : "" ?>>Aktif</option>
                                <option value="0" <?= $status === "0" ? "selected" : "" ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="update" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="Kelola_Guru.php" class="btn btn-dark">
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
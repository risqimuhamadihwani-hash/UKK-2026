<?php
session_start();
require_once 'config/koneksi.php'; // Sesuaikan jalur file koneksi jika perlu

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Koneksi database fallback jika $conn/$koneksi dari config berbeda
$koneksi = $conn ?? $koneksi ?? new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$error = "";
$nip = "";
$nama = "";
$email = "";
$status = "1";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan"])) {

    $nip = trim($_POST["nip"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $konfirmasi_password = $_POST["konfirmasi_password"] ?? "";
    $status = $_POST["status_aktif"] ?? "1";

    // Validasi input kosong, format NIP, password, dan konfirmasi password
    if ($nip === "" || $nama === "" || $email === "" || $password === "" || $konfirmasi_password === "") {
        $error = "Semua kolom wajib diisi, termasuk konfirmasi password.";
    } elseif (!ctype_digit($nip) || strlen($nip) > 18) {
        $error = "NIP hanya boleh berisi angka dan maksimal 18 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter.";
    } elseif ($password !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak cocok dengan password.";
    } elseif (!in_array($status, ["0", "1"], true)) {
        $error = "Status guru tidak valid.";
    } else {

        // Cek apakah NIP sudah terdaftar sebelumnya
        $stmtCekNip = $koneksi->prepare("SELECT id FROM t_guru WHERE nip = ?");
        $stmtCekNip->bind_param("s", $nip);
        $stmtCekNip->execute();
        $stmtCekNip->store_result();

        if ($stmtCekNip->num_rows > 0) {
            $error = "NIP sudah terdaftar. Gunakan NIP yang berbeda.";
            $stmtCekNip->close();
        } else {
            $stmtCekNip->close();

            $koneksi->begin_transaction();

            try {
                // Hash password yang diinputkan sendiri
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $role = "guru";
                $rememberToken = "";

                // 1. Membuat akun user di tabel t_users agar bisa login
                $stmtUser = $koneksi->prepare("
                    INSERT INTO t_users
                    (name, email, password, remember_token, role)
                    VALUES (?, ?, ?, ?, ?)
                ");

                if (!$stmtUser) {
                    throw new Exception($koneksi->error);
                }

                $stmtUser->bind_param(
                    "sssss",
                    $nama,
                    $email,
                    $passwordHash,
                    $rememberToken,
                    $role
                );

                if (!$stmtUser->execute()) {
                    throw new Exception($stmtUser->error);
                }

                $user_id = $koneksi->insert_id;
                $stmtUser->close();

                // 2. Menyimpan data guru di tabel t_guru dan menghubungkannya dengan user_id
                $statusInt = (int)$status;

                $stmtGuru = $koneksi->prepare("
                    INSERT INTO t_guru
                    (nip, nama, email, status_aktif, user_id)
                    VALUES (?, ?, ?, ?, ?)
                ");

                if (!$stmtGuru) {
                    throw new Exception($koneksi->error);
                }

                $stmtGuru->bind_param(
                    "sssii",
                    $nip,
                    $nama,
                    $email,
                    $statusInt,
                    $user_id
                );

                if (!$stmtGuru->execute()) {
                    throw new Exception($stmtGuru->error);
                }

                $stmtGuru->close();

                $koneksi->commit();

                // Redirect kembali ke halaman kelola guru dengan pesan sukses
                header("Location: Kelola_Guru.php?pesan=berhasil");
                exit;

            } catch (Exception $e) {
                $koneksi->rollback();

                if ($koneksi->errno == 1062) {
                    $error = "NIP atau Email sudah terdaftar di database.";
                } else {
                    $error = "Gagal menambahkan guru: " . $e->getMessage();
                }
            }
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
    <title>Tambah Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

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

            <h3 class="fw-bold mb-4">Tambah Guru</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                            <?= e($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                placeholder="Masukkan Email untuk Login"
                                value="<?= e($email) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan Password Minimal 6 Karakter"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input
                                type="password"
                                name="konfirmasi_password"
                                class="form-control"
                                placeholder="Ulangi Password yang Sama"
                                required
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
                            <button type="submit" name="simpan" class="btn btn-primary px-3 py-1 text-white" style="background-color: #0d6efd;">
                                Tambah
                            </button>

                            <a href="Kelola_Guru.php" class="btn btn-secondary px-3 py-1" style="background-color: #000000;">
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
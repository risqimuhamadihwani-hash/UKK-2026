<?php
session_start();
require_once "config/koneksi.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("ID guru tidak valid.");
}

$stmt = $koneksi->prepare("SELECT * FROM t_guru WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$guru = $stmt->get_result()->fetch_assoc();
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
    $password_baru = $_POST["password_baru"] ?? "";
    $konfirmasi_password = $_POST["konfirmasi_password"] ?? "";

    if ($nip === "" || $nama === "" || $email === "") {
        $error = "NIP, Nama Guru, dan Email wajib diisi.";
    } elseif (strlen($nip) > 30) {
        $error = "NIP maksimal 30 karakter.";
    } elseif (!in_array($status, ["0", "1"], true)) {
        $error = "Status guru tidak valid.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif ($password_baru !== "" && strlen($password_baru) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } elseif ($password_baru !== $konfirmasi_password) {
        $error = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        $stmtCek = $koneksi->prepare("SELECT id FROM t_guru WHERE nip = ? AND id != ?");
        $stmtCek->bind_param("si", $nip, $id);
        $stmtCek->execute();
        $stmtCek->store_result();

        if ($stmtCek->num_rows > 0) {
            $error = "NIP sudah terdaftar oleh guru lain.";
            $stmtCek->close();
        } else {
            $stmtCek->close();
            $user_id = (int)($guru['user_id'] ?? 0);

            try {
                $koneksi->begin_transaction();
                $statusInt = (int)$status;

                // 1. Update data guru utama
                $stmtGuru = $koneksi->prepare("UPDATE t_guru SET nip = ?, nama = ?, email = ?, status_aktif = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmtGuru->bind_param("sssii", $nip, $nama, $email, $statusInt, $id);
                $stmtGuru->execute();
                $stmtGuru->close();

                // 2. SINKRONISASI: Otomatis ubah status di t_wali_kelas agar ikut nyambung
                $stmtWali = $koneksi->prepare("UPDATE t_wali_kelas SET status_aktif = ? WHERE guru_id = ?");
                $stmtWali->bind_param("ii", $statusInt, $id);
                $stmtWali->execute();
                $stmtWali->close();

                // Akun login berada di t_users dan terhubung melalui t_guru.user_id.
                if ($user_id <= 0) {
                    throw new Exception("Akun login guru tidak terhubung. Periksa user_id pada tabel t_guru.");
                }

                if ($password_baru !== "") {
                    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    $stmtUser = $koneksi->prepare("UPDATE t_users SET name = ?, email = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmtUser->bind_param("sssi", $nama, $email, $password_hash, $user_id);
                } else {
                    $stmtUser = $koneksi->prepare("UPDATE t_users SET name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmtUser->bind_param("ssi", $nama, $email, $user_id);
                }
                $stmtUser->execute();
                if ($stmtUser->affected_rows === 0 && $stmtUser->errno) {
                    throw new Exception($stmtUser->error);
                }
                $stmtUser->close();

                $koneksi->commit();
                header("Location: Kelola_Guru.php?pesan=diubah");
                exit;
            } catch (Throwable $e) {
                $koneksi->rollback();
                $error = "Gagal mengubah data guru: " . $e->getMessage();
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
    <title>Edit Data Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top" style="top: 0; height: 100vh; overflow-y: auto;">
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

            <h3 class="fw-bold mb-4">Edit Data Guru</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input
                                type="text"
                                name="nip"
                                class="form-control"
                                placeholder="Masukkan NIP"
                                value="<?= e($nip) ?>"
                                maxlength="30"
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
                            <label class="form-label">Password Baru</label>
                            <input
                                type="password"
                                name="password_baru"
                                class="form-control"
                                placeholder="Masukkan password baru jika ingin mengganti"
                                minlength="6"
                                autocomplete="new-password"
                            >
                            <small class="text-muted">Jika diisi dan disimpan, password baru otomatis menggantikan password lama. Minimal 6 karakter.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                name="konfirmasi_password"
                                class="form-control"
                                placeholder="Ulangi password baru"
                                minlength="6"
                                autocomplete="new-password"
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

                            <a href="Kelola_Guru.php" class="btn btn-secondary">
                                Batal
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
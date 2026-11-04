<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

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
    $status = $_POST["status_aktif"] ?? "1";

    // Validasi kosong dan panjang NIP maksimal 12 karakter
    if ($nip === "" || $nama === "" || $email === "") {
        $error = "NIP, Nama Guru, dan Email wajib diisi.";
    } elseif (strlen($nip) > 12) {
        $error = "NIP tidak boleh lebih dari 12 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (!in_array($status, ["0", "1"], true)) {
        $error = "Status guru tidak valid.";
    } else {

        // Cek apakah NIP sudah terdaftar di database sebelumnya
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
                // Membuat password otomatis
                $passwordOtomatis = bin2hex(random_bytes(8));
                $passwordHash = password_hash($passwordOtomatis, PASSWORD_DEFAULT);

                $role = "guru";
                $rememberToken = "";

                // Membuat akun di t_users
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

                // Menyimpan data guru
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
                $koneksi->close();

                header("Location: Kelola_Guru.php?pesan=berhasil");
                exit;

            } catch (Exception $e) {
                $koneksi->rollback();

                if ($koneksi->errno == 1062) {
                    $error = "NIP atau email sudah terdaftar.";
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
                    <a href="Kelola_Guru.php" class="nav-link active">
                        Kelola Guru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu3.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu4.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu5.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu6.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu7.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu8.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu9.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu10.php" class="nav-link text-white">
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

            <h1 class="mb-4">Tambah Guru</h1>

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
                    <label class="form-label">NIP (Maksimal 12 Karakter)</label>

                    <input
                        type="text"
                        name="nip"
                        class="form-control"
                        placeholder="Masukkan NIP (maks. 12 digit)"
                        value="<?= e($nip) ?>"
                        maxlength="12"
                        required
                    >
                </div>

                <div class="mb-4">
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

                <div class="mb-4">
                    <label class="form-label">Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan Email"
                        value="<?= e($email) ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>

                    <select name="status_aktif" class="form-select" required>

                        <option value="1" <?= $status === "1" ? "selected" : "" ?>>
                            Aktif
                        </option>

                        <option value="0" <?= $status === "0" ? "selected" : "" ?>>
                            Tidak Aktif
                        </option>

                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Guru
                </button>

                <a href="Kelola_Guru.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </main>

    </div>
</div>

</body>
</html>
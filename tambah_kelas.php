
<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$pesan = "";

$nama = "";
$tingkat = "";
$jurusan = "";
$status_aktif = "1";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan"])) {

    $nama = trim($_POST["nama"] ?? "");
    $tingkat = trim($_POST["tingkat"] ?? "");
    $jurusan = trim($_POST["jurusan"] ?? "");
    $status_aktif = $_POST["status_aktif"] ?? "";

    if ($nama === "" || $tingkat === "" || $jurusan === "") {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (!in_array($tingkat, ["X", "XI", "XII"], true)) {
        $pesan = "Tingkat kelas tidak valid.";
    } elseif (!in_array($status_aktif, ["0", "1"], true)) {
        $pesan = "Status kelas tidak valid.";
    } else {

        $status_aktif = (int)$status_aktif;

        $sql = "INSERT INTO t_kelas
                (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())";

        $stmt = $koneksi->prepare($sql);

        if (!$stmt) {
            die("Query gagal: " . $koneksi->error);
        }

        $stmt->bind_param(
            "sssi",
            $nama,
            $tingkat,
            $jurusan,
            $status_aktif
        );

        if ($stmt->execute()) {
            $stmt->close();
            $koneksi->close();

            header("Location: menu3.php");
            exit;
        } else {
            $pesan = "Gagal menyimpan data kelas: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Data Kelas</title>

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
                    <a href="Kelola_Kelas.php" class="nav-link active">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
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

            <h1 class="mb-4">Tambah Data Kelas</h1>

            <?php if ($pesan !== ""): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
                    <label class="form-label">Nama Kelas</label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Contoh: X RPL 1"
                        maxlength="100"
                        value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">Tingkat</label>

                    <select name="tingkat" class="form-select" required>
                        <option value="">-- Pilih Tingkat --</option>

                        <option value="X" <?= $tingkat === "X" ? "selected" : "" ?>>
                            X (Sepuluh)
                        </option>

                        <option value="XI" <?= $tingkat === "XI" ? "selected" : "" ?>>
                            XI (Sebelas)
                        </option>

                        <option value="XII" <?= $tingkat === "XII" ? "selected" : "" ?>>
                            XII (Dua Belas)
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Jurusan</label>

                    <input
                        type="text"
                        name="jurusan"
                        class="form-control"
                        placeholder="Contoh: RPL, TSM, TKJ"
                        maxlength="100"
                        value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">Status Kelas</label>

                    <select name="status_aktif" class="form-select" required>
                        <option value="1" <?= $status_aktif === "1" ? "selected" : "" ?>>
                            Aktif
                        </option>

                        <option value="0" <?= $status_aktif === "0" ? "selected" : "" ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan
                </button>

                <a href="menu3.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </main>

    </div>
</div>

</body>
</html>

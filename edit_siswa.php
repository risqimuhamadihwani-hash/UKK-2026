<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("ID siswa tidak valid.");
}

$stmt = $koneksi->prepare("SELECT * FROM t_siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$siswa = $result->fetch_assoc();
$stmt->close();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

$pesan = "";

$nis = $siswa['nis'];
$nisn = $siswa['nisn'];
$nama = $siswa['nama'];
$jenis_kelamin = $siswa['jenis_kelamin'];
$tanggal_lahir = $siswa['tanggal_lahir'];
$alamat = $siswa['alamat'];
$status_aktif = (string) $siswa['status_aktif'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan"])) {

    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    if ($nis === "" || $nama === "" || $jenis_kelamin === "") {
        $pesan = "NIS, nama, dan jenis kelamin wajib diisi.";
    } elseif (!preg_match('/^[0-9]{1,8}$/', $nis)) {
        $pesan = "NIS hanya boleh angka dan maksimal 8 digit.";
    } elseif ($nisn !== "" && !preg_match('/^[0-9]{1,10}$/', $nisn)) {
        $pesan = "NISN hanya boleh angka dan maksimal 10 digit.";
    } elseif (!in_array($jenis_kelamin, ["L", "P"], true)) {
        $pesan = "Jenis kelamin tidak valid.";
    } elseif (!in_array($status_aktif, ["0", "1"], true)) {
        $pesan = "Status siswa tidak valid.";
    } else {

        // Cek apakah NIS atau NISN sudah digunakan oleh siswa *lain* (id != $id)
        if ($nisn !== "") {
            $stmtCek = $koneksi->prepare("SELECT id FROM t_siswa WHERE (nis = ? OR nisn = ?) AND id != ?");
            $stmtCek->bind_param("ssi", $nis, $nisn, $id);
        } else {
            $stmtCek = $koneksi->prepare("SELECT id FROM t_siswa WHERE nis = ? AND id != ?");
            $stmtCek->bind_param("si", $nis, $id);
        }

        $stmtCek->execute();
        $stmtCek->store_result();

        if ($stmtCek->num_rows > 0) {
            $pesan = "NIS atau NISN sudah digunakan oleh siswa lain. Gunakan nomor yang berbeda.";
            $stmtCek->close();
        } else {
            $stmtCek->close();

            $statusInt = (int) $status_aktif;

            $sql = "UPDATE t_siswa SET
                    nis = ?,
                    nisn = ?,
                    nama = ?,
                    jenis_kelamin = ?,
                    tanggal_lahir = NULLIF(?, ''),
                    alamat = ?,
                    status_aktif = ?
                    WHERE id = ?";

            $stmt = $koneksi->prepare($sql);

            if (!$stmt) {
                $pesan = "Query gagal: " . $koneksi->error;
            } else {

                $stmt->bind_param(
                    "ssssssii",
                    $nis,
                    $nisn,
                    $nama,
                    $jenis_kelamin,
                    $tanggal_lahir,
                    $alamat,
                    $statusInt,
                    $id
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    $koneksi->close();

                    header("Location: Kelola_Siswa.php");
                    exit;
                } else {
                    if ($stmt->errno == 1062) {
                        $pesan = "NIS atau NISN sudah digunakan siswa lain.";
                    } else {
                        $pesan = "Gagal mengubah data: " . $stmt->error;
                    }
                }

                $stmt->close();
            }
        }
    }
}

function e($data) {
    return htmlspecialchars((string) ($data ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">JusticeAdmin</h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link active">
                        Kelola Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu2.php" class="nav-link text-white">
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

            <h1 class="mb-4">Edit Siswa</h1>

            <?php if ($pesan !== ""): ?>
                <div class="alert alert-danger">
                    <?= e($pesan) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_siswa.php?id=<?= $id ?>">

                <div class="mb-3">
                    <label class="form-label">NIS</label>
                    <input
                        type="text"
                        name="nis"
                        class="form-control"
                        placeholder="Masukkan NIS (maksimal 8 digit)"
                        maxlength="8"
                        pattern="[0-9]{1,8}"
                        inputmode="numeric"
                        value="<?= e($nis) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">NISN</label>
                    <input
                        type="text"
                        name="nisn"
                        class="form-control"
                        placeholder="Masukkan NISN (maksimal 10 digit)"
                        maxlength="10"
                        pattern="[0-9]{1,10}"
                        inputmode="numeric"
                        value="<?= e($nisn) ?>"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama siswa"
                        value="<?= e($nama) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" <?= $jenis_kelamin === "L" ? "selected" : "" ?>>Laki-laki</option>
                        <option value="P" <?= $jenis_kelamin === "P" ? "selected" : "" ?>>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input
                        type="date"
                        name="tanggal_lahir"
                        class="form-control"
                        value="<?= e($tanggal_lahir) ?>"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea
                        name="alamat"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan alamat siswa"
                    ><?= e($alamat) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Aktif</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1" <?= $status_aktif === "1" ? "selected" : "" ?>>Aktif</option>
                        <option value="0" <?= $status_aktif === "0" ? "selected" : "" ?>>Tidak Aktif</option>
                    </select>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="simpan" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="Kelola_Siswa.php" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </main>

    </div>
</div>

</body>
</html>
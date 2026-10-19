
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if (
        $nis === '' || $nama === '' ||
        !in_array($jenis_kelamin, ['L', 'P'], true) ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Data belum lengkap atau tidak valid.";
    } else {
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
        $stmt->bind_param(
            "ssssssii",
            $nis,
            $nisn,
            $nama,
            $jenis_kelamin,
            $tanggal_lahir,
            $alamat,
            $status_aktif,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            $koneksi->close();
            header("Location: menu1.php");
            exit;
        } else {
            $pesan = "Gagal mengubah data: " . $stmt->error;
        }

        $stmt->close();
    }

    $siswa['nis'] = $nis;
    $siswa['nisn'] = $nisn;
    $siswa['nama'] = $nama;
    $siswa['jenis_kelamin'] = $jenis_kelamin;
    $siswa['tanggal_lahir'] = $tanggal_lahir;
    $siswa['alamat'] = $alamat;
    $siswa['status_aktif'] = $status_aktif;
}

function e($nilai) {
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Siswa - JusticeAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #f8f9fc;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: #212529;
            min-height: 100vh;
            padding: 20px 12px;
        }

        .sidebar h4 {
            color: white;
            margin-bottom: 25px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #0d6efd;
        }

        .content {
            padding: 30px 24px;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .content {
                padding: 20px 12px;
            }
        }
    </style>
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
                    <a href="menu1.php" class="nav-link active">
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

            <h1>Edit Siswa</h1>
            <p class="text-secondary">Halaman untuk mengubah data siswa.</p>

            <?php if ($pesan !== '') { ?>
                <div class="alert alert-danger">
                    <?= e($pesan); ?>
                </div>
            <?php } ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <form method="POST"
                          action="edit_siswa.php?id=<?= $id; ?>">

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">NIS:</label>
                                <input type="text" name="nis"
                                       class="form-control"
                                       value="<?= e($siswa['nis']); ?>"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">NISN:</label>
                                <input type="text" name="nisn"
                                       class="form-control"
                                       value="<?= e($siswa['nisn']); ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nama Siswa:</label>
                                <input type="text" name="nama"
                                       class="form-control"
                                       value="<?= e($siswa['nama']); ?>"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Kelamin:</label>
                                <select name="jenis_kelamin"
                                        class="form-select" required>
                                    <option value="L"
                                        <?= $siswa['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
                                        Laki-laki
                                    </option>
                                    <option value="P"
                                        <?= $siswa['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Lahir:</label>
                                <input type="date" name="tanggal_lahir"
                                       class="form-control"
                                       value="<?= e($siswa['tanggal_lahir']); ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status Aktif:</label>
                                <select name="status_aktif"
                                        class="form-select" required>
                                    <option value="1"
                                        <?= $siswa['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>
                                    <option value="0"
                                        <?= $siswa['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Alamat:</label>
                                <textarea name="alamat" class="form-control"
                                          rows="3"><?= e($siswa['alamat']); ?></textarea>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="menu1.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>

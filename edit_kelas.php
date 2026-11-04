
<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$pesan = "";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: menu3.php");
    exit;
}

// AMBIL DATA KELAS
$sql = "SELECT id, nama, tingkat, jurusan, status_aktif
        FROM t_kelas
        WHERE id = ?";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    die("Query gagal: " . $koneksi->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$kelas = $result->fetch_assoc();

$stmt->close();

if (!$kelas) {
    $koneksi->close();
    header("Location: menu3.php");
    exit;
}

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    if ($nama === '' || $tingkat === '' || $jurusan === '') {

        $pesan = "Semua kolom wajib diisi.";

    } elseif (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {

        $pesan = "Tingkat kelas tidak valid.";

    } elseif (!in_array($status_aktif, ['0', '1'], true)) {

        $pesan = "Status kelas tidak valid.";

    } else {

        $status_aktif = (int)$status_aktif;

        $sql = "UPDATE t_kelas
                SET nama = ?,
                    tingkat = ?,
                    jurusan = ?,
                    status_aktif = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $stmt = $koneksi->prepare($sql);

        if (!$stmt) {

            $pesan = "Query gagal: " . $koneksi->error;

        } else {

            $stmt->bind_param(
                "sssii",
                $nama,
                $tingkat,
                $jurusan,
                $status_aktif,
                $id
            );

            if ($stmt->execute()) {

                $stmt->close();
                $koneksi->close();

                header("Location: menu3.php");
                exit;

            } else {

                $pesan = "Gagal mengubah data kelas: " . $stmt->error;

            }

            $stmt->close();
        }
    }

    // PERTAHANKAN INPUT JIKA VALIDASI GAGAL
    $kelas['nama'] = $nama;
    $kelas['tingkat'] = $tingkat;
    $kelas['jurusan'] = $jurusan;
    $kelas['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Data Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

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
                    <a href="menu1.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu2.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="menu3.php" class="nav-link active">
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

            <h1 class="mb-4">Edit Data Kelas</h1>

            <!-- PESAN ERROR -->
            <?php if ($pesan !== ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <!-- FORM EDIT KELAS -->
            <form method="POST"
                  action="edit_kelas.php?id=<?= (int)$id ?>"
                  autocomplete="off">

                <!-- INPUT TERSEMBUNYI UNTUK AUTOFILL -->
                <input
                    type="text"
                    name="username_fake"
                    autocomplete="username"
                    tabindex="-1"
                    aria-hidden="true"
                    style="position:absolute;left:-9999px;"
                >

                <input
                    type="password"
                    name="password_fake"
                    autocomplete="new-password"
                    tabindex="-1"
                    aria-hidden="true"
                    style="position:absolute;left:-9999px;"
                >

                <!-- NAMA KELAS -->
                <div class="mb-4">

                    <label class="form-label">
                        Nama Kelas
                    </label>

                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        class="form-control"
                        placeholder="Contoh: X RPL 1"
                        maxlength="100"
                        value="<?= htmlspecialchars($kelas['nama'], ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="off"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                        required
                    >

                </div>

                <!-- TINGKAT -->
                <div class="mb-4">

                    <label class="form-label">
                        Tingkat
                    </label>

                    <select
                        name="tingkat"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Tingkat --
                        </option>

                        <option value="X"
                            <?= $kelas['tingkat'] === 'X' ? 'selected' : '' ?>>
                            X (Sepuluh)
                        </option>

                        <option value="XI"
                            <?= $kelas['tingkat'] === 'XI' ? 'selected' : '' ?>>
                            XI (Sebelas)
                        </option>

                        <option value="XII"
                            <?= $kelas['tingkat'] === 'XII' ? 'selected' : '' ?>>
                            XII (Dua Belas)
                        </option>

                    </select>

                </div>

                <!-- JURUSAN -->
                <div class="mb-4">

                    <label class="form-label">
                        Jurusan
                    </label>

                    <input
                        type="text"
                        name="jurusan"
                        class="form-control"
                        placeholder="Contoh: RPL, TSM, TKJ"
                        maxlength="100"
                        value="<?= htmlspecialchars($kelas['jurusan'], ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="off"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                        required
                    >

                </div>

                <!-- STATUS KELAS -->
                <div class="mb-4">

                    <label class="form-label">
                        Status Kelas
                    </label>

                    <select
                        name="status_aktif"
                        class="form-select"
                        required
                    >

                        <option value="1"
                            <?= (string)$kelas['status_aktif'] === '1' ? 'selected' : '' ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?= (string)$kelas['status_aktif'] === '0' ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>

                    </select>

                </div>

                <!-- TOMBOL -->
                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary"
                >
                    Simpan Perubahan
                </button>

                <a href="menu3.php"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

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
    header("Location: Kelola_Kelas.php");
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
    header("Location: Kelola_Kelas.php");
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

                header("Location: Kelola_Kelas.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas</title>
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
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                    <h7 class="fw-bold mb-4">Kelola Guru</h7> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link active">
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

            <h3 class="fw-bold mb-4">Edit Kelas</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <!-- PESAN ERROR -->
                    <?php if ($pesan !== ''): ?>
                        <div class="alert alert-danger py-2">
                            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM EDIT KELAS -->
                    <form method="POST" action="edit_kelas.php?id=<?= (int)$id ?>" autocomplete="off">

                        <!-- INPUT TERSEMBUNYI UNTUK AUTOFILL -->
                        <input type="text" name="username_fake" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;">
                        <input type="password" name="password_fake" autocomplete="new-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;">

                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
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

                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="X" <?= $kelas['tingkat'] === 'X' ? 'selected' : '' ?>>X (Sepuluh)</option>
                                <option value="XI" <?= $kelas['tingkat'] === 'XI' ? 'selected' : '' ?>>XI (Sebelas)</option>
                                <option value="XII" <?= $kelas['tingkat'] === 'XII' ? 'selected' : '' ?>>XII (Dua Belas)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
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

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= (string)$kelas['status_aktif'] === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= (string)$kelas['status_aktif'] === '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary px-3 py-1 text-white" style="background-color: #0d6efd;">
                                Simpan Perubahan
                            </button>

                            <a href="Kelola_Kelas.php" class="btn btn-secondary px-3 py-1" style="background-color: #6c757d;">
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

<?php
// INISIASI SESSION & KONEKSI DATABASE
session_start();
require_once __DIR__ . '/config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$siswa_id = "";
$kelas_id = "";
$status = "1";

// =====================================
// AMBIL TAHUN AJARAN AKTIF
// =====================================

$tahun_aktif = null;

try {
    $queryTahun = $koneksi->query("
        SELECT id, nama, tanggal_mulai, tanggal_selesai
        FROM t_tahun_ajaran
        WHERE status_aktif = 1
        ORDER BY tanggal_mulai DESC
        LIMIT 1
    ");

    $tahun_aktif = $queryTahun->fetch_assoc();

} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    $error = "Gagal mengambil data tahun ajaran.";
}

// =====================================
// PROSES SIMPAN PENEMPATAN SISWA
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan"])) {

    $siswa_id = trim($_POST["siswa_id"] ?? "");
    $kelas_id = trim($_POST["kelas_id"] ?? "");
    $status = $_POST["status_aktif"] ?? "1";

    if (!$tahun_aktif) {

        $error = "Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.";

    } elseif ($siswa_id === "" || $kelas_id === "") {

        $error = "Silakan pilih siswa dan kelas terlebih dahulu.";

    } elseif (!ctype_digit($siswa_id) || !ctype_digit($kelas_id)) {

        $error = "Data siswa atau kelas tidak valid.";

    } elseif (!in_array($status, ["0", "1"], true)) {

        $error = "Status penempatan tidak valid.";

    } else {

        try {

            $siswaInt = (int) $siswa_id;
            $kelasInt = (int) $kelas_id;
            $tahunInt = (int) $tahun_aktif["id"];
            $statusInt = (int) $status;

            $tanggal_mulai = $tahun_aktif["tanggal_mulai"];
            $tanggal_selesai = $tahun_aktif["tanggal_selesai"];

            // 1. CEK SISWA
            $stmtSiswa = $koneksi->prepare("
                SELECT id
                FROM t_siswa
                WHERE id = ? AND status_aktif = 1
                LIMIT 1
            ");

            $stmtSiswa->bind_param("i", $siswaInt);
            $stmtSiswa->execute();
            $stmtSiswa->store_result();

            if ($stmtSiswa->num_rows === 0) {

                $error = "Siswa tidak ditemukan atau sudah tidak aktif.";

            } else {

                // 2. CEK KELAS
                $stmtKelas = $koneksi->prepare("
                    SELECT id
                    FROM t_kelas
                    WHERE id = ?
                    AND tahun_ajaran_id = ?
                    AND status_aktif = 1
                    LIMIT 1
                ");

                $stmtKelas->bind_param("ii", $kelasInt, $tahunInt);
                $stmtKelas->execute();
                $stmtKelas->store_result();

                if ($stmtKelas->num_rows === 0) {

                    $error = "Kelas tidak ditemukan atau tidak aktif.";

                } else {

                    // 3. CEK PENEMPATAN SISWA
                    $stmtCek = $koneksi->prepare("
                        SELECT id
                        FROM t_kelas_siswa
                        WHERE siswa_id = ?
                        AND tahun_ajaran_id = ?
                        LIMIT 1
                    ");

                    $stmtCek->bind_param("ii", $siswaInt, $tahunInt);
                    $stmtCek->execute();
                    $stmtCek->store_result();

                    if ($stmtCek->num_rows > 0) {

                        $error = "Siswa tersebut sudah mendapatkan penempatan pada tahun ajaran ini.";

                    } else {

                        // 4. SIMPAN DATA
                        $stmtSimpan = $koneksi->prepare("
                            INSERT INTO t_kelas_siswa
                            (
                                siswa_id,
                                tahun_ajaran_id,
                                kelas_id,
                                tanggal_mulai,
                                tanggal_selesai,
                                status_aktif
                            )
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");

                        $stmtSimpan->bind_param(
                            "iiissi",
                            $siswaInt,
                            $tahunInt,
                            $kelasInt,
                            $tanggal_mulai,
                            $tanggal_selesai,
                            $statusInt
                        );

                        $stmtSimpan->execute();

                        $stmtSimpan->close();
                        $stmtCek->close();
                        $stmtKelas->close();
                        $stmtSiswa->close();

                        header("Location: Kelola_Penempatan_Siswa.php?pesan=berhasil");
                        exit;
                    }

                    $stmtCek->close();
                }

                $stmtKelas->close();
            }

            $stmtSiswa->close();

        } catch (mysqli_sql_exception $e) {

            error_log($e->getMessage());
            $error = "Gagal menambahkan penempatan siswa. Periksa kembali data database.";
        }
    }
}

// =====================================
// AMBIL DAFTAR SISWA
// =====================================

$siswa_list = [];

if ($tahun_aktif) {

    try {

        $tahunInt = (int) $tahun_aktif["id"];

        $stmtSiswaList = $koneksi->prepare("
            SELECT s.id, s.nis, s.nama
            FROM t_siswa s
            WHERE s.status_aktif = 1
            AND NOT EXISTS (
                SELECT 1
                FROM t_kelas_siswa ks
                WHERE ks.siswa_id = s.id
                AND ks.tahun_ajaran_id = ?
            )
            ORDER BY s.nama ASC
        ");

        $stmtSiswaList->bind_param("i", $tahunInt);
        $stmtSiswaList->execute();

        $stmtSiswaList->bind_result($idSiswa, $nisSiswa, $namaSiswa);

        while ($stmtSiswaList->fetch()) {
            $siswa_list[] = [
                "id" => $idSiswa,
                "nis" => $nisSiswa,
                "nama" => $namaSiswa
            ];
        }

        $stmtSiswaList->close();

    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        $error = "Gagal mengambil daftar siswa.";
    }
}

// =====================================
// AMBIL DAFTAR KELAS
// =====================================

$kelas_list = [];

if ($tahun_aktif) {

    try {

        $tahunInt = (int) $tahun_aktif["id"];

        $stmtKelasList = $koneksi->prepare("
            SELECT id, nama
            FROM t_kelas
            WHERE status_aktif = 1
            AND tahun_ajaran_id = ?
            ORDER BY nama ASC
        ");

        $stmtKelasList->bind_param("i", $tahunInt);
        $stmtKelasList->execute();

        $stmtKelasList->bind_result($idKelas, $namaKelas);

        while ($stmtKelasList->fetch()) {
            $kelas_list[] = [
                "id" => $idKelas,
                "nama" => $namaKelas
            ];
        }

        $stmtKelasList->close();

    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        $error = "Gagal mengambil daftar kelas.";
    }
}

// =====================================
// FUNGSI ESCAPE
// =====================================

function e($nilai) {
    return htmlspecialchars(
        (string)($nilai ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sticky-top"
             style="top: 0; height: 100vh; overflow-y: auto;">

            <h4 class="text-white fw-bold mb-4">
                Pelanggaran Siswa
            </h4>

            <ul class="nav nav-pills flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <span class="fw-bold">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Siswa</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Guru</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Kelas</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Tahun Ajaran</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link active">
                        <span class="fw-bold">Kelola Penempatan Siswa</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Wali Kelas</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Kategori Pelanggaran</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link text-white">
                        <span class="fw-bold">Kelola Jenis Pelanggaran</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        <span class="fw-bold">Laporan</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        <span class="fw-bold">About Me</span>
                    </a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        <span class="fw-bold">Logout</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h3 class="fw-bold mb-4">Tambah Penempatan Siswa</h3>

            <div class="card shadow-sm border"
                 style="background-color: #ffffff; border-color: #dee2e6 !important;">

                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">

                            <?= e($error) ?>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close">
                            </button>

                        </div>
                    <?php endif; ?>

                    <?php if (!$tahun_aktif): ?>
                        <div class="alert alert-warning py-2">
                            Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.
                        </div>
                    <?php endif; ?>

                    <!-- FORM TAMBAH -->
                    <form method="POST" autocomplete="off">

                        <!-- PILIH SISWA -->
                        <div class="mb-3">

                            <label class="form-label">Siswa</label>

                            <select name="siswa_id" class="form-select" required>

                                <option value="">-- Pilih Siswa --</option>

                                <?php foreach ($siswa_list as $s): ?>

                                    <option
                                        value="<?= (int)$s['id'] ?>"
                                        <?= ($siswa_id == $s['id']) ? "selected" : "" ?>
                                    >
                                        <?= e($s['nis'] . " - " . $s['nama']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <?php if ($tahun_aktif && empty($siswa_list)): ?>
                                <div class="form-text text-danger">
                                    Semua siswa aktif sudah mendapatkan penempatan pada tahun ajaran ini.
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">

                            <label class="form-label">Tahun Ajaran</label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= e($tahun_aktif['nama'] ?? 'Belum ada Tahun Ajaran aktif') ?>"
                                readonly
                            >

                        </div>

                        <!-- PILIH KELAS -->
                        <div class="mb-3">

                            <label class="form-label">Kelas</label>

                            <select name="kelas_id" class="form-select" required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php foreach ($kelas_list as $k): ?>

                                    <option
                                        value="<?= (int)$k['id'] ?>"
                                        <?= ($kelas_id == $k['id']) ? "selected" : "" ?>
                                    >
                                        <?= e($k['nama']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <?php if ($tahun_aktif && empty($kelas_list)): ?>
                                <div class="form-text text-danger">
                                    Belum ada kelas aktif pada tahun ajaran ini.
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-3">

                            <label class="form-label">Status Aktif</label>

                            <select name="status_aktif" class="form-select" required>

                                <option value="1" <?= $status === "1" ? "selected" : "" ?>>
                                    Aktif
                                </option>

                                <option value="0" <?= $status === "0" ? "selected" : "" ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2 mt-4">

                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-primary px-3 py-1 text-white"
                                style="background-color: #0d6efd;"
                                <?= (!$tahun_aktif || empty($siswa_list) || empty($kelas_list)) ? 'disabled' : '' ?>
                            >
                                Tambah
                            </button>

                            <a
                                href="Kelola_Penempatan_Siswa.php"
                                class="btn btn-secondary px-3 py-1"
                                style="background-color: #000000;"
                            >
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

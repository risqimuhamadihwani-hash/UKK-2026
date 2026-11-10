<?php
session_start();
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// PROSES SIMPAN
if (isset($_POST['simpan'])) {

    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $guru_id = (int) $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $tahun_ajaran_id <= 0 ||
        $kelas_id <= 0 ||
        $guru_id <= 0 ||
        $tanggal_mulai == "" ||
        $tanggal_selesai == "" ||
        $tanggal_selesai < $tanggal_mulai
    ) {

        $pesan = "Data tidak valid. Periksa kembali form.";

    } else {

        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_wali_kelas
             WHERE tahun_ajaran_id = ?
             AND kelas_id = ?
             AND status_aktif = 1"
        );

        mysqli_stmt_bind_param(
            $cek, "ii",
            $tahun_ajaran_id,
            $kelas_id
        );

        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if ($status_aktif == 1 && mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kelas tersebut sudah memiliki wali kelas aktif.";

        } else {

            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO t_wali_kelas
                (
                    tahun_ajaran_id,
                    kelas_id,
                    guru_id,
                    tanggal_mulai,
                    tanggal_selesai,
                    status_aktif
                )
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt, "iiissi",
                $tahun_ajaran_id,
                $kelas_id,
                $guru_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);

                header("Location: Kelola_Wali_Kelas.php");
                exit;

            } else {

                $pesan = "Gagal menyimpan data wali kelas.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

// DATA TAHUN AJARAN (Hanya mengambil yang aktif, sesuaikan kolom status jika di database bernama 'status' atau 'status_aktif')
$tahun = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1 
     LIMIT 1"
);
$tahun_aktif = mysqli_fetch_assoc($tahun);

// DATA KELAS
$kelas = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_kelas
     ORDER BY nama ASC"
);

// DATA GURU
$guru = mysqli_query($koneksi,
    "SELECT id, nip, nama
     FROM t_guru
     ORDER BY nama ASC"
);

if (!$kelas || !$guru) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wali Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

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
                    <a href="Kelola_Wali_Kelas.php" class="nav-link active">
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

            <h3 class="fw-bold mb-4">Tambah Wali Kelas</h3>

            <div class="card shadow-sm border" style="background-color: #ffffff; border-color: #dee2e6 !important;">
                <div class="card-body p-4">

                    <?php if ($pesan != ""): ?>
                        <div class="alert alert-danger py-2">
                            <?= htmlspecialchars($pesan) ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM TAMBAH WALI KELAS -->
                    <form method="POST" action="tambah_wali_kelas.php">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <?php if ($tahun_aktif): ?>
                                <input type="hidden" name="tahun_ajaran_id" value="<?= $tahun_aktif['id'] ?>">
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($tahun_aktif['nama']) ?>" readonly>
                            <?php else: ?>
                                <input type="text" class="form-control bg-light text-danger" value="Tidak ada tahun ajaran yang aktif saat ini!" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $k['id'] ?>">
                                        <?= htmlspecialchars($k['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)): ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= htmlspecialchars($g['nip'] . ' - ' . $g['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-primary px-3 py-1 text-white"
                                style="background-color: #0d6efd;"
                                <?= !$tahun_aktif ? 'disabled' : '' ?>
                            >
                                Tambah
                            </button>

                            <a href="Kelola_Wali_Kelas.php" class="btn btn-secondary px-3 py-1" style="background-color: #6c757d;">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

</body>
</html>
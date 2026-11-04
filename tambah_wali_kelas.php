
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

                header("Location: menu6.php");
                exit;

            } else {

                $pesan = "Gagal menyimpan data wali kelas.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

// DATA TAHUN AJARAN
$tahun = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     ORDER BY id DESC"
);

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

if (!$tahun || !$kelas || !$guru) {
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

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
                    <a href="menu6.php" class="nav-link active">
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

            <h1>Tambah Wali Kelas</h1>

            <?php if ($pesan != ""): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>

            <?php endif; ?>

            <!-- FORM TAMBAH WALI KELAS -->
            <form method="POST" action="tambah_wali_kelas.php">

                <div class="mb-4">

                    <label class="form-label">
                        Tahun Ajaran
                    </label>

                    <select name="tahun_ajaran_id" class="form-select" required>

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        <?php while ($t = mysqli_fetch_assoc($tahun)): ?>

                            <option value="<?= $t['id'] ?>">
                                <?= htmlspecialchars($t['nama']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Kelas
                    </label>

                    <select name="kelas_id" class="form-select" required>

                        <option value="">
                            -- Pilih Kelas --
                        </option>

                        <?php while ($k = mysqli_fetch_assoc($kelas)): ?>

                            <option value="<?= $k['id'] ?>">
                                <?= htmlspecialchars($k['nama']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Guru
                    </label>

                    <select name="guru_id" class="form-select" required>

                        <option value="">
                            -- Pilih Guru --
                        </option>

                        <?php while ($g = mysqli_fetch_assoc($guru)): ?>

                            <option value="<?= $g['id'] ?>">
                                <?= htmlspecialchars($g['nip'] . ' - ' . $g['nama']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        name="tanggal_selesai"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Status Aktif
                    </label>

                    <select name="status_aktif" class="form-select" required>

                        <option value="1">Aktif</option>

                        <option value="0">Tidak Aktif</option>

                    </select>

                </div>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary"
                >
                    Simpan
                </button>

                <a href="menu6.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </main>

    </div>

</div>

</body>
</html>

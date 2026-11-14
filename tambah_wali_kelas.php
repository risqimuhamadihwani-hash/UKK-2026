<?php
session_start();
include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// DATA TAHUN AJARAN: Mengambil id, nama, tanggal_mulai, dan tanggal_selesai yang aktif
$tahun = mysqli_query($koneksi,
    "SELECT id, nama, tanggal_mulai, tanggal_selesai
     FROM t_tahun_ajaran
     WHERE status_aktif = 1 
     LIMIT 1"
);
$tahun_aktif = mysqli_fetch_assoc($tahun);
$tahun_ajaran_aktif_id = $tahun_aktif ? (int)$tahun_aktif['id'] : 0;

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

                // Sinkronkan status_aktif guru di tabel t_guru agar ikut berubah sesuai pilihan wali kelas
                $update_guru = mysqli_prepare($koneksi, 
                    "UPDATE t_guru SET status_aktif = ? WHERE id = ?"
                );
                mysqli_stmt_bind_param($update_guru, "ii", $status_aktif, $guru_id);
                mysqli_stmt_execute($update_guru);
                mysqli_stmt_close($update_guru);

                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);

                // DITAMBAHKAN ?pesan=berhasil AGAR NOTIFIKASI MUNCUL DI KELOLA WALI KELAS
                header("Location: Kelola_Wali_Kelas.php?pesan=berhasil");
                exit;

            } else {

                $pesan = "Gagal menyimpan data wali kelas.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

// DATA KELAS
$kelas = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_kelas
     ORDER BY nama ASC"
);

// DATA GURU: Hanya menampilkan guru yang BELUM menjadi wali kelas aktif di tahun ajaran aktif ini
$guru = mysqli_query($koneksi,
    "SELECT g.id, g.nip, g.nama, g.status_aktif
     FROM t_guru g
     LEFT JOIN t_wali_kelas w ON g.id = w.guru_id AND w.status_aktif = 1 AND w.tahun_ajaran_id = $tahun_ajaran_aktif_id
     WHERE w.id IS NULL
     ORDER BY g.nama ASC"
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
                    <a href="Kelola_Penempatan_Siswa.php" class="nav-link text-white">
                    <span class="fw-bold">Kelola Penempatan Siswa</span> 
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link active">
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
                            <select name="guru_id" class="form-select select2-guru" required>
                                <option value="">-- Pilih atau Cari Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)): ?>
                                    <?php 
                                        $bersihNip = str_ireplace('NIP', '', $g['nip'] ?? ''); 
                                        $statusLabel = ((int)$g['status_aktif'] === 1) ? ' (Aktif)' : ' (Tidak Aktif)';
                                    ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= htmlspecialchars(trim($bersihNip) . ' - ' . $g['nama'] . $statusLabel) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control bg-light"
                                value="<?= $tahun_aktif ? htmlspecialchars($tahun_aktif['tanggal_mulai']) : '' ?>"
                                readonly
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control bg-light"
                                value="<?= $tahun_aktif ? htmlspecialchars($tahun_aktif['tanggal_selesai']) : '' ?>"
                                readonly
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
                            >Tambah
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

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-guru').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih atau Cari Guru --',
        allowClear: true,
        width: '100%'
    });
});
</script>

</body>
</html>
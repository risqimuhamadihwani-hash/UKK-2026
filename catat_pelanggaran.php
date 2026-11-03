
<?php
session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE GURU
if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// KONEKSI DATABASE
$koneksi = $conn ?? $koneksi ?? null;

if (!$koneksi instanceof mysqli) {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php.");
}

$pesan = '';
$error = '';

$guru_id = 0;
$nama_guru = $_SESSION['name'] ?? 'Guru';

// CARI DATA GURU
$stmtGuru = $koneksi->prepare(
    "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
);

$stmtGuru->bind_param("i", $_SESSION['id_user']);
$stmtGuru->execute();

$dataGuru = $stmtGuru->get_result()->fetch_assoc();
$stmtGuru->close();

if ($dataGuru) {
    $guru_id = (int) $dataGuru['id'];
    $nama_guru = $dataGuru['nama'] ?: $nama_guru;
}

// PROSES SIMPAN PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $tahun_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);

        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $tindakan = trim($_POST['tindakan'] ?? '');

        if (
            $guru_id < 1 ||
            $tahun_id < 1 ||
            $siswa_id < 1 ||
            $pelanggaran_id < 1 ||
            $keterangan === '' ||
            $tindakan === ''
        ) {
            throw new Exception("Lengkapi semua data terlebih dahulu.");
        }

        // VALIDASI TANGGAL
        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);

        if (!$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new Exception("Format tanggal tidak valid.");
        }

        // CEK TAHUN AJARAN
        $stmt = $koneksi->prepare(
            "SELECT nama FROM t_tahun_ajaran WHERE id = ? LIMIT 1"
        );

        $stmt->bind_param("i", $tahun_id);
        $stmt->execute();

        $tahun = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$tahun) {
            throw new Exception("Tahun ajaran tidak ditemukan.");
        }

        // CEK SISWA DAN KELAS
        $stmt = $koneksi->prepare(
            "SELECT
                s.id,
                s.nama,
                ks.kelas_id,
                k.nama AS nama_kelas
            FROM t_kelas_siswa ks
            JOIN t_siswa s ON s.id = ks.siswa_id
            JOIN t_kelas k ON k.id = ks.kelas_id
            WHERE s.id = ?
            AND ks.tahun_ajaran_id = ?
            AND ks.status_aktif = 1
            AND s.status_aktif = 1
            LIMIT 1"
        );

        $stmt->bind_param("ii", $siswa_id, $tahun_id);
        $stmt->execute();

        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            throw new Exception(
                "Siswa belum memiliki penempatan kelas aktif pada tahun ajaran tersebut."
            );
        }

        // CEK JENIS PELANGGARAN
        $stmt = $koneksi->prepare(
            "SELECT id, nama, poin, pelanggaran_kategori_id
            FROM t_pelanggaran
            WHERE id = ?
            AND status_aktif = 1
            LIMIT 1"
        );

        $stmt->bind_param("i", $pelanggaran_id);
        $stmt->execute();

        $pelanggaran = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$pelanggaran) {
            throw new Exception(
                "Jenis pelanggaran tidak ditemukan atau tidak aktif."
            );
        }

        // SIAPKAN DATA
        $nama_siswa = $siswa['nama'];
        $kelas_id = (int) $siswa['kelas_id'];
        $nama_kelas = $siswa['nama_kelas'];

        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) ($pelanggaran['pelanggaran_kategori_id'] ?? 0);
        $poin = (int) $pelanggaran['poin'];

        $status = 'tercatat';

        // SIMPAN KE DATABASE
        $stmt = $koneksi->prepare(
            "INSERT INTO t_pelanggaran_siswa
            (
                tahun_ajaran_id,
                siswa_id,
                nama_siswa,
                kelas_id,
                nama_kelas,
                pelanggaran_id,
                nama_pelanggaran,
                pelanggaran_kategori_id,
                guru_id,
                nama_guru,
                tanggal,
                keterangan,
                poin,
                tindakan,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iisisiisiississ",
            $tahun_id,
            $siswa_id,
            $nama_siswa,
            $kelas_id,
            $nama_kelas,
            $pelanggaran_id,
            $nama_pelanggaran,
            $kategori_id,
            $guru_id,
            $nama_guru,
            $tanggal,
            $keterangan,
            $poin,
            $tindakan,
            $status
        );

        $stmt->execute();
        $stmt->close();

        // KEMBALI KE HALAMAN INI
        header("Location: Catat_Pelanggaran.php?berhasil=1");
        exit;

    } catch (Throwable $e) {

        $error = "Gagal menyimpan: " . $e->getMessage();

    }
}

// PESAN BERHASIL
if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
}

// DAFTAR TAHUN AJARAN
$tahunAjaran = $koneksi->query(
    "SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC"
);

// DAFTAR SISWA
$siswaList = $koneksi->query(
    "SELECT DISTINCT
        s.id,
        s.nis,
        s.nama,
        ks.tahun_ajaran_id,
        k.nama AS nama_kelas,
        ta.nama AS tahun
    FROM t_kelas_siswa ks
    JOIN t_siswa s ON s.id = ks.siswa_id
    JOIN t_kelas k ON k.id = ks.kelas_id
    JOIN t_tahun_ajaran ta ON ta.id = ks.tahun_ajaran_id
    WHERE ks.status_aktif = 1
    AND s.status_aktif = 1
    ORDER BY s.nama ASC"
);

// DAFTAR PELANGGARAN
$pelanggaranList = $koneksi->query(
    "SELECT id, kode, nama, poin
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama ASC"
);

// RIWAYAT PELANGGARAN
$riwayat = $koneksi->query(
    "SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catat Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
    href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

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
                    <a href="guru.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link active">
                        Catat Pelanggaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">
                        Riwayat
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

            <h2>Catat Pelanggaran Siswa</h2>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-success">
                    <?= e($pesan) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <!-- FORM CATAT PELANGGARAN -->
            <form method="POST" action="Catat_Pelanggaran.php">

                <div class="mb-3">

                    <label class="form-label">
                        Tahun Ajaran
                    </label>

                    <select name="tahun_ajaran_id" class="form-select" required>

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        <?php while ($ta = $tahunAjaran->fetch_assoc()): ?>

                            <option value="<?= (int) $ta['id'] ?>">
                                <?= e($ta['nama']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Siswa
                    </label>

                    <select name="siswa_id" class="form-select" required>

                        <option value="">
                            -- Pilih Siswa --
                        </option>

                        <?php while ($s = $siswaList->fetch_assoc()): ?>

                            <option
                                value="<?= (int) $s['id'] ?>"
                                data-tahun="<?= (int) $s['tahun_ajaran_id'] ?>"
                            >
                                <?= e(
                                    $s['nis'] . ' - ' .
                                    $s['nama'] . ' (' .
                                    $s['nama_kelas'] . ' / ' .
                                    $s['tahun'] . ')'
                                ) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Jenis Pelanggaran
                    </label>

                    <select name="pelanggaran_id" class="form-select" required>

                        <option value="">
                            -- Pilih Pelanggaran --
                        </option>

                        <?php while ($p = $pelanggaranList->fetch_assoc()): ?>

                            <option value="<?= (int) $p['id'] ?>">
                                <?= e(
                                    $p['kode'] . ' - ' .
                                    $p['nama'] . ' (' .
                                    $p['poin'] . ' poin)'
                                ) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Tindakan
                    </label>

                    <textarea
                        name="tindakan"
                        class="form-control"
                        rows="3"
                        required
                    ></textarea>

                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Pelanggaran
                </button>

                <button type="reset" class="btn btn-secondary">
                    Reset
                </button>

            </form>

            <hr>

            <!-- RIWAYAT CATATAN -->
            <h2 class="mb-3">
                Riwayat Catatan Pelanggaran
            </h2>

            <div class="table-responsive">

                <table
                    id="tabelPelanggaran"
                    class="table table-hover table-striped table-bordered align-middle"
                >

                    <thead class="table-dark">

                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($riwayat->num_rows > 0): ?>

                            <?php $no = 1; ?>

                            <?php while ($r = $riwayat->fetch_assoc()): ?>

                                <tr>

                                    <td><?= $no++ ?></td>

                                    <td><?= e($r['tanggal']) ?></td>

                                    <td><?= e($r['nama_siswa']) ?></td>

                                    <td><?= e($r['nama_kelas']) ?></td>

                                    <td><?= e($r['nama_pelanggaran']) ?></td>

                                    <td><?= (int) $r['poin'] ?></td>

                                    <td><?= e($r['tindakan']) ?></td>

                                    <td><?= e($r['status']) ?></td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" class="text-center">
                                    Belum ada catatan pelanggaran.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>

</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,
        searching: true,
        paging: true,
        info: true,

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada catatan pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});
</script>

</body>
</html>

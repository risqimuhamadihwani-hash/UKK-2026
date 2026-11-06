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
} else {
    $guru_id = (int) ($_SESSION['id_user'] ?? 1);
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

        // CEK SISWA
        $stmt = $koneksi->prepare(
            "SELECT id, nama FROM t_siswa WHERE id = ? LIMIT 1"
        );

        $stmt->bind_param("i", $siswa_id);
        $stmt->execute();

        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }

        // CEK KELAS SISWA
        $stmtKelas = $koneksi->prepare(
            "SELECT k.id AS kelas_id, k.nama AS nama_kelas
             FROM t_kelas_siswa ks
             JOIN t_kelas k ON k.id = ks.kelas_id
             WHERE ks.siswa_id = ? AND ks.tahun_ajaran_id = ? AND ks.status_aktif = 1
             LIMIT 1"
        );
        $stmtKelas->bind_param("ii", $siswa_id, $tahun_id);
        $stmtKelas->execute();
        $kelasData = $stmtKelas->get_result()->fetch_assoc();
        $stmtKelas->close();

        // Menggunakan NULL jika siswa belum memiliki penempatan kelas di tahun ajaran tersebut
        $kelas_id = ($kelasData && !empty($kelasData['kelas_id'])) ? (int) $kelasData['kelas_id'] : null;
        $nama_kelas = $kelasData ? $kelasData['nama_kelas'] : '-';

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
            throw new Exception("Jenis pelanggaran tidak ditemukan atau tidak aktif.");
        }

        // SIAPKAN DATA
        $nama_siswa = $siswa['nama'];
        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) ($pelanggaran['pelanggaran_kategori_id'] ?? 0);
        $poin = (int) $pelanggaran['poin'];

        $status = 'tercatat';

        // SIMPAN KE DATABASE (Menggunakan kuesioner dengan penanganan kelas_id NULL)
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

        // Menggunakan tipe parameter yang sesuai, bungkus kelas_id dengan variabel agar bisa dikirim sebagai NULL jika kosong
        $stmt->bind_param(
            "iisiisiississis",
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
    "SELECT 
        s.id,
        s.nis,
        s.nama,
        COALESCE(MAX(k.nama), 'Tanpa Kelas') AS nama_kelas
    FROM t_siswa s
    LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON k.id = ks.kelas_id
    GROUP BY s.id, s.nis, s.nama
    ORDER BY s.id ASC"
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">JusticeGuru</h4>
            
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="guru.php" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Catat_Pelanggaran.php" class="nav-link active">Catat Pelanggaran</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link text-white">Tindakan</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
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
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-select" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php while ($ta = $tahunAjaran->fetch_assoc()): ?>
                            <option value="<?= (int) $ta['id'] ?>">
                                <?= e($ta['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Siswa</label>
                    <select name="siswa_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = $siswaList->fetch_assoc()): ?>
                            <option value="<?= (int) $s['id'] ?>">
                                <?= e($s['nis'] . ' - ' . $s['nama'] . ' (' . $s['nama_kelas'] . ')') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Pelanggaran</label>
                    <select name="pelanggaran_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggaran --</option>
                        <?php while ($p = $pelanggaranList->fetch_assoc()): ?>
                            <option value="<?= (int) $p['id'] ?>">
                                <?= e($p['kode'] . ' - ' . $p['nama'] . ' (' . $p['poin'] . ' poin)') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tindakan</label>
                    <textarea
                        name="tindakan"
                        class="form-control"
                        rows="3"
                        required
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pelanggaran</button>
                <button type="reset" class="btn btn-secondary">Reset</button>

            </form>

            <hr>

            <!-- RIWAYAT CATATAN -->
            <h2 class="mb-3">Riwayat Catatan Pelanggaran</h2>

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
    $.fn.dataTable.ext.errMode = 'none';

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
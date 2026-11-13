<?php
// Tampilkan error jika ada untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

// CEK ROLE GURU / WALI KELAS
if ($role !== 'guru' && $role !== 'wali_kelas' && $role !== 'walikelas') {
    header("Location: dashboard.php");
    exit;
}

// Jika role wali kelas, samakan penanganan hak aksesnya seperti guru
if ($role === 'wali_kelas' || $role === 'walikelas') {
    $role = 'guru';
}

function e($data) {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

$session_id = (int)($_SESSION['id_user'] ?? 0);

// Ambil tahun ajaran aktif
$tahunQuery = $koneksi->query("SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
$tahunData = $tahunQuery ? $tahunQuery->fetch_assoc() : null;
$tahun_id = $tahunData ? (int)$tahunData['id'] : 1;

// DETEKSI APAKAH GURU INI WALI KELAS
$is_wali_kelas = false;
$stmtWali = $koneksi->prepare(
    "SELECT k.nama AS nama_kelas 
     FROM t_guru g
     JOIN t_wali_kelas wk ON wk.guru_id = g.id AND wk.tahun_ajaran_id = ? AND wk.status_aktif = 1
     JOIN t_kelas k ON k.id = wk.kelas_id
     WHERE g.user_id = ? OR g.id = ?
     LIMIT 1"
);
$stmtWali->bind_param("iii", $tahun_id, $session_id, $session_id);
$stmtWali->execute();
$resWali = $stmtWali->get_result()->fetch_assoc();
$stmtWali->close();

if ($resWali && !empty($resWali['nama_kelas'])) {
    $is_wali_kelas = true;
}

$pesan = '';
$error = '';

// PROSES SIMPAN TINDAKAN & STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_tindakan'])) {

    $id = (int) ($_POST['id'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($id <= 0 || $tindakan === '' || !in_array($status, ['tercatat', 'diproses', 'selesai'], true)) {
        $error = "Data tindakan atau status tidak valid.";
    } else {
        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran_siswa
            SET tindakan = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");

        mysqli_stmt_bind_param($stmt, "ssi", $tindakan, $status, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: Tindakan.php?pesan=berhasil");
            exit;
        } else {
            $error = "Gagal menyimpan tindakan.";
        }

        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil') {
    $pesan = "Tindakan berhasil diperbarui.";
}

// PENCARIAN & JOIN KE TABEL KELAS DAN PELANGGARAN
$cari = trim($_GET['cari'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$sql = "
    SELECT
        p.id,
        p.nama_siswa,
        COALESCE(k.nama, p.nama_kelas, '-') AS nama_kelas,
        COALESCE(tp.nama, p.nama_pelanggaran, '-') AS nama_pelanggaran,
        p.nama_guru,
        p.tanggal,
        p.poin,
        p.tindakan,
        p.status
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_kelas k ON k.id = p.kelas_id
    LEFT JOIN t_pelanggaran tp ON tp.id = p.pelanggaran_id
    WHERE 1=1
";

$params = [];
$types = '';

if (in_array($status_filter, ['tercatat', 'diproses', 'selesai'], true)) {
    $sql .= " AND p.status = ?";
    $params[] = $status_filter;
    $types .= "s";
} else {
    $sql .= " AND p.status != 'selesai'";
}

if ($cari !== '') {
    $sql .= " AND (
        p.nama_siswa LIKE ?
        OR k.nama LIKE ?
        OR tp.nama LIKE ?
        OR p.nama_guru LIKE ?
    )";

    $keyword = "%$cari%";
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $types .= "ssss";
}

$sql .= " ORDER BY p.id DESC";

$stmt = mysqli_prepare($koneksi, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tindakan - Sistem Informasi Pelanggaran Siswa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
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
                    <a href="dashboard.php" class="nav-link text-white"><span class="fw-bold">Dashboard</span></a>
                </li>
                <li class="nav-item mb-2">
                    <a href="catatan_pelanggaran.php" class="nav-link text-white"><span class="fw-bold">Catatan Pelanggaran</span></a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Tindakan.php" class="nav-link active"><span class="fw-bold">Tindakan</span></a>
                </li>
                <li class="nav-item mb-2">
                    <a href="rekap_poin.php" class="nav-link text-white"><span class="fw-bold">Rekap Poin</span></a>
                </li>
                <li class="nav-item mb-2">
                    <a href="riwayat.php" class="nav-link text-white"><span class="fw-bold">Riwayat</span></a>
                </li>
                
                <!-- MENU LAPORAN HANYA MUNCUL JIKA WALI KELAS -->
                <?php if ($is_wali_kelas): ?>
                    <li class="nav-item mb-2">
                        <a href="Laporan.php" class="nav-link text-white"><span class="fw-bold">Laporan</span></a>
                    </li>
                <?php endif; ?>

                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger"><span class="fw-bold">Logout</span></a>
                </li>
            </ul>
        </div>
        
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="container-fluid p-0">

                <div class="mb-4">
                    <h3>Kelola Tindakan</h3>
                    <p class="text-muted">Kelola tindakan penanganan pelanggaran siswa.</p>
                </div>

                <?php if ($pesan): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= e($pesan) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- TABEL TINDAKAN -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Data Tindakan Pelanggaran</h5>
                        <div class="table-responsive">
                            <table id="tabelTindakan" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Pelanggaran</th>
                                        <th>Guru</th>
                                        <th>Tanggal</th>
                                        <th>Poin</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= e($row['nama_siswa']) ?></td>
                                            <td><?= e($row['nama_kelas']) ?></td>
                                            <td><?= e($row['nama_pelanggaran']) ?></td>
                                            <td><?= e($row['nama_guru']) ?></td>
                                            <td>
                                                <?= (!empty($row['tanggal']) && $row['tanggal'] !== '0000-00-00') ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= (int)$row['poin'] ?> Poin
                                                </span>
                                            </td>

                                            <form method="POST">
                                                <td>
                                                    <select name="tindakan" class="form-select form-select-sm" required>
                                                        <option value="" disabled <?= empty($row['tindakan']) ? 'selected' : '' ?>>-- Pilih Tindakan --</option>
                                                        <option value="Teguran Lisan" <?= $row['tindakan'] === 'Teguran Lisan' ? 'selected' : '' ?>>Teguran Lisan</option>
                                                        <option value="Peringatan Tertulis (SP 1)" <?= $row['tindakan'] === 'Peringatan Tertulis (SP 1)' ? 'selected' : '' ?>>Peringatan Tertulis (SP 1)</option>
                                                        <option value="Peringatan Tertulis (SP 2)" <?= $row['tindakan'] === 'Peringatan Tertulis (SP 2)' ? 'selected' : '' ?>>Peringatan Tertulis (SP 2)</option>
                                                        <option value="Peringatan Tertulis (SP 3)" <?= $row['tindakan'] === 'Peringatan Tertulis (SP 3)' ? 'selected' : '' ?>>Peringatan Tertulis (SP 3)</option>
                                                        <option value="Panggilan Orang Tua / Wali" <?= $row['tindakan'] === 'Panggilan Orang Tua / Wali' ? 'selected' : '' ?>>Panggilan Orang Tua / Wali</option>
                                                        <option value="Skorsing" <?= $row['tindakan'] === 'Skorsing' ? 'selected' : '' ?>>Skorsing</option>
                                                        <option value="Bimbingan Konseling (BK)" <?= $row['tindakan'] === 'Bimbingan Konseling (BK)' ? 'selected' : '' ?>>Bimbingan Konseling (BK)</option>
                                                        <option value="Kerja Bakti / Sanksi Edukatif" <?= $row['tindakan'] === 'Kerja Bakti / Sanksi Edukatif' ? 'selected' : '' ?>>Kerja Bakti / Sanksi Edukatif</option>
                                                    </select>
                                                </td>

                                                <td>
                                                    <select name="status" class="form-select form-select-sm" required>
                                                        <option value="tercatat" <?= strtolower($row['status']) === 'tercatat' ? 'selected' : '' ?>>Tercatat / Perlu Tindakan</option>
                                                        <option value="diproses" <?= strtolower($row['status']) === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                        <option value="selesai" <?= strtolower($row['status']) === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                    </select>
                                                </td>

                                                <td>
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button
                                                        type="submit"
                                                        name="simpan_tindakan"
                                                        class="btn btn-primary btn-sm text-nowrap"
                                                    >
                                                        Simpan
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelTindakan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3 align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        columnDefs: [
            {
                targets: 9,
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Data tindakan tidak ditemukan",
            emptyTable: "Belum ada data tindakan pelanggaran",
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
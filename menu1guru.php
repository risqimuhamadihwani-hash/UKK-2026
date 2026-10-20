<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (strtolower($_SESSION['role'] ?? '') !== 'guru') {
    header("Location: login.php");
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

$nama_guru = $_SESSION['name'] ?? $_SESSION['nama'] ?? 'Guru';
$pesan = '';
$jenis = 'danger';

/* Tahun ajaran aktif */
$result_tahun = $koneksi->query(
    "SELECT id FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC LIMIT 1"
);

$tahun = $result_tahun ? $result_tahun->fetch_assoc() : null;
$tahun_id = (int)($tahun['id'] ?? 0);

/* Simpan pelanggaran */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');

    if (!$tahun_id) {
        $pesan = "Tahun ajaran aktif belum tersedia.";
    } elseif (
        !$siswa_id || !$pelanggaran_id || !$tanggal ||
        !$keterangan || !$tindakan
    ) {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
        $tanggal > date('Y-m-d') ||
        date('Y-m-d', strtotime($tanggal)) !== $tanggal
    ) {
        $pesan = "Tanggal kejadian tidak valid.";
    } else {
        /* Validasi siswa dan kelas */
        $stmt = $koneksi->prepare(
            "SELECT s.id, s.nama, k.id AS kelas_id,
                    k.nama AS nama_kelas
             FROM t_siswa s
             INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
             INNER JOIN t_kelas k ON k.id = ks.kelas_id
             WHERE s.id = ?
               AND s.status_aktif = 1
               AND ks.status_aktif = 1
               AND ks.tahun_ajaran_id = ?
               AND k.status_aktif = 1
             LIMIT 1"
        );

        $stmt->bind_param("ii", $siswa_id, $tahun_id);
        $stmt->execute();
        $siswa = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        /* Validasi jenis pelanggaran */
        $stmt = $koneksi->prepare(
            "SELECT id, nama, poin, pelanggaran_kategori_id
             FROM t_pelanggaran
             WHERE id = ? AND status_aktif = 1
             LIMIT 1"
        );

        $stmt->bind_param("i", $pelanggaran_id);
        $stmt->execute();
        $pelanggaran = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$siswa) {
            $pesan = "Siswa tidak ditemukan atau belum memiliki kelas aktif pada tahun ajaran ini.";
        } elseif (!$pelanggaran) {
            $pesan = "Jenis pelanggaran tidak ditemukan atau tidak aktif.";
        } else {
            $nama_siswa = $siswa['nama'];
            $kelas_id = (int)$siswa['kelas_id'];
            $nama_kelas = $siswa['nama_kelas'];
            $nama_pelanggaran = $pelanggaran['nama'];
            $kategori_id = (int)$pelanggaran['pelanggaran_kategori_id'];
            $poin = (int)$pelanggaran['poin'];
            $status = 'tercatat';

            $stmt = $koneksi->prepare(
                "INSERT INTO t_pelanggaran_siswa (
                    tahun_ajaran_id,
                    siswa_id,
                    nama_siswa,
                    kelas_id,
                    nama_kelas,
                    pelanggaran_id,
                    nama_pelanggaran,
                    pelanggaran_kategori_id,
                    nama_guru,
                    tanggal,
                    keterangan,
                    poin,
                    tindakan,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "iisisisisssiss",
                $tahun_id,
                $siswa_id,
                $nama_siswa,
                $kelas_id,
                $nama_kelas,
                $pelanggaran_id,
                $nama_pelanggaran,
                $kategori_id,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status
            );

            try {
                $stmt->execute();
                $stmt->close();

                header("Location: catat_pelanggaran.php?sukses=1");
                exit;
            } catch (Throwable $err) {
                $stmt->close();
                $pesan = "Gagal menyimpan pelanggaran. Periksa struktur tabel database.";
            }
        }
    }
}

if (isset($_GET['sukses'])) {
    $pesan = "Pelanggaran berhasil dicatat.";
    $jenis = 'success';
}

/* Ambil daftar siswa */
$siswa_list = [];

if ($tahun_id > 0) {
    $stmt = $koneksi->prepare(
        "SELECT s.id, s.nis, s.nama, k.nama AS nama_kelas
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
         INNER JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE s.status_aktif = 1
           AND ks.status_aktif = 1
           AND ks.tahun_ajaran_id = ?
           AND k.status_aktif = 1
         ORDER BY s.nama ASC"
    );

    $stmt->bind_param("i", $tahun_id);
    $stmt->execute();
    $hasil_siswa = $stmt->get_result();

    while ($row = $hasil_siswa->fetch_assoc()) {
        $siswa_list[] = $row;
    }

    $stmt->close();
}

/* Daftar pelanggaran */
$pelanggaran_list = $koneksi->query(
    "SELECT id, kode, nama, poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

/* Riwayat pelanggaran */
$riwayat = $koneksi->query(
    "SELECT id, nama_siswa, nama_kelas, nama_pelanggaran,
            tanggal, poin, nama_guru, tindakan, status
     FROM t_pelanggaran_siswa
     ORDER BY id DESC
     LIMIT 50"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catat Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .sidebar {
            background: #212529;
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: #e9ecef;
            border-radius: 8px;
            margin-bottom: 6px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .card-header {
            border-bottom: 1px solid #eee;
            padding: 16px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            font-weight: 600;
        }

        #tabelRiwayat th,
        #tabelRiwayat td {
            vertical-align: middle;
        }

        .table-responsive {
            padding: 16px 20px;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h4 class="text-white mb-4">JusticeAdmin</h4>

            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="catat_pelanggaran.php" class="nav-link active">
                        Catat Pelanggaran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="menu2guru.php" class="nav-link">Tindakan</a>
                </li>
                <li class="nav-item">
                    <a href="menu3guru.php" class="nav-link">Rekap Poin</a>
                </li>
                <li class="nav-item">
                    <a href="menu4guru.php" class="nav-link">Riwayat</a>
                </li>
                <li class="nav-item">
                    <a href="about_me.php" class="nav-link">About Me</a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </li>
            </ul>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">Catat Pelanggaran Siswa</h3>
                    <p class="text-muted mb-0">
                        Selamat datang, <?= e($nama_guru) ?>
                    </p>
                </div>
            </div>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-<?= e($jenis) ?> alert-dismissible fade show">
                    <?= e($pesan) ?>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM CATAT PELANGGARAN -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Catat Pelanggaran</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="catat_pelanggaran.php">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="siswa_id" class="form-label">
                                    Nama Siswa
                                </label>

                                <select name="siswa_id" id="siswa_id"
                                        class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>

                                    <?php foreach ($siswa_list as $s): ?>
                                        <option value="<?= (int)$s['id'] ?>"
                                            <?= ((int)($_POST['siswa_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                                            <?= e($s['nama']) ?>
                                            (<?= e($s['nis']) ?>) -
                                            <?= e($s['nama_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if (!$tahun_id): ?>
                                    <small class="text-danger">
                                        Tahun ajaran aktif belum tersedia.
                                    </small>
                                <?php elseif (empty($siswa_list)): ?>
                                    <small class="text-danger">
                                        Data siswa kosong. Periksa status siswa,
                                        penempatan kelas, dan tahun ajaran.
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label for="pelanggaran" class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <select name="pelanggaran_id" id="pelanggaran"
                                        class="form-select" required>
                                    <option value="">-- Pilih Pelanggaran --</option>

                                    <?php if ($pelanggaran_list): ?>
                                        <?php while ($p = $pelanggaran_list->fetch_assoc()): ?>
                                            <option
                                                value="<?= (int)$p['id'] ?>"
                                                data-poin="<?= (int)$p['poin'] ?>"
                                                <?= ((int)($_POST['pelanggaran_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>>
                                                <?= e($p['kode']) ?> -
                                                <?= e($p['nama']) ?>
                                                (<?= (int)$p['poin'] ?> poin)
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="poin" class="form-label">Poin</label>
                                <input type="number" id="poin"
                                       class="form-control" value="0" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">
                                    Tanggal Kejadian
                                </label>
                                <input type="date" name="tanggal" id="tanggal"
                                       class="form-control"
                                       value="<?= e($_POST['tanggal'] ?? date('Y-m-d')) ?>"
                                       max="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="keterangan" class="form-label">
                                    Keterangan
                                </label>
                                <textarea name="keterangan" id="keterangan"
                                          class="form-control" rows="3"
                                          required
                                          placeholder="Tuliskan keterangan kejadian"><?= e($_POST['keterangan'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12">
                                <label for="tindakan" class="form-label">
                                    Tindakan
                                </label>
                                <select name="tindakan" id="tindakan"
                                        class="form-select" required>
                                    <option value="">-- Pilih Tindakan --</option>

                                    <?php
                                    $daftar_tindakan = [
                                        'Teguran lisan',
                                        'Pembinaan',
                                        'Pembinaan oleh wali kelas',
                                        'Pemanggilan siswa',
                                        'Pemanggilan orang tua'
                                    ];

                                    foreach ($daftar_tindakan as $t):
                                    ?>
                                        <option value="<?= e($t) ?>"
                                            <?= (($_POST['tindakan'] ?? '') === $t) ? 'selected' : '' ?>>
                                            <?= e($t) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    Simpan Pelanggaran
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    Reset
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- RIWAYAT PELANGGARAN -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Riwayat Pelanggaran</h5>
                </div>

                <div class="table-responsive">
                    <table id="tabelRiwayat"
                           class="table table-bordered table-striped table-hover w-100">

                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Guru</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($riwayat): ?>
                                <?php $no = 1; ?>
                                <?php while ($r = $riwayat->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= e($r['nama_siswa']) ?></td>
                                        <td><?= e($r['nama_kelas']) ?></td>
                                        <td><?= e($r['nama_pelanggaran']) ?></td>
                                        <td><?= e($r['tanggal']) ?></td>
                                        <td><?= (int)$r['poin'] ?></td>
                                        <td><?= e($r['nama_guru']) ?></td>
                                        <td><?= e($r['tindakan']) ?></td>
                                        <td><?= e($r['status']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectPelanggaran = document.getElementById('pelanggaran');
    const inputPoin = document.getElementById('poin');

    function updatePoin() {
        const option = selectPelanggaran.options[
            selectPelanggaran.selectedIndex
        ];

        inputPoin.value = option ? (option.dataset.poin || 0) : 0;
    }

    selectPelanggaran.addEventListener('change', updatePoin);
    updatePoin();

    if (window.jQuery && $.fn.DataTable) {
        $('#tabelRiwayat').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            ordering: true,
            searching: true,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data yang dicari tidak ditemukan',
                emptyTable: 'Belum ada catatan pelanggaran',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });
    }
});
</script>

</body>
</html>

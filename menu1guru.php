
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

/* Ambil tahun ajaran aktif */
$tahun = $koneksi->query(
    "SELECT id FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC LIMIT 1"
)->fetch_assoc();

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
    } elseif (!$siswa_id || !$pelanggaran_id ||
              !$tanggal || !$keterangan || !$tindakan) {
        $pesan = "Semua kolom wajib diisi.";
    } elseif ($tanggal > date('Y-m-d')) {
        $pesan = "Tanggal tidak boleh melebihi hari ini.";
    } else {
        /* Cari siswa dan kelasnya */
        $stmt = $koneksi->prepare(
            "SELECT s.id, s.nama, k.id AS kelas_id, k.nama AS nama_kelas
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

        /* Cari jenis pelanggaran dan poin */
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
            $pesan = "Siswa belum memiliki kelas aktif pada tahun ajaran ini.";
        } elseif (!$pelanggaran) {
            $pesan = "Jenis pelanggaran tidak ditemukan.";
        } else {
            $nama_siswa = $siswa['nama'];
            $kelas_id = (int)$siswa['kelas_id'];
            $nama_kelas = $siswa['nama_kelas'];
            $nama_pelanggaran = $pelanggaran['nama'];
            $kategori_id = (int)$pelanggaran['pelanggaran_kategori_id'];
            $poin = (int)$pelanggaran['poin'];
            $status = 'tercatat';

            /*
             * guru_id dibiarkan NULL sementara karena akun login
             * belum terhubung dengan profil t_guru.
             * Nama guru tetap disimpan dari session login.
             */
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
                $pesan = "Gagal menyimpan pelanggaran. Periksa struktur tabel database.";
                $stmt->close();
            }
        }
    }
}

if (isset($_GET['sukses'])) {
    $pesan = "Pelanggaran berhasil dicatat.";
    $jenis = 'success';
}

/* Daftar siswa */
$siswa_list = $koneksi->prepare(
    "SELECT s.id, s.nis, s.nama, k.nama AS nama_kelas
     FROM t_siswa s
     INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
     INNER JOIN t_kelas k ON k.id = ks.kelas_id
     WHERE s.status_aktif = 1
       AND ks.status_aktif = 1
       AND ks.tahun_ajaran_id = ?
       AND k.status_aktif = 1
     ORDER BY s.nama"
);
$siswa_list->bind_param("i", $tahun_id);
$siswa_list->execute();
$siswa_list = $siswa_list->get_result();

/* Daftar pelanggaran */
$pelanggaran_list = $koneksi->query(
    "SELECT id, kode, nama, poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama"
);

/* Riwayat catatan */
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catat Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
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
                    <a href="menu1guru.php" class="nav-link active">
                        Catat pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu2guru.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu3guru.php" class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu4guru.php" class="nav-link text-white">
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
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Catat Pelanggaran Siswa</h3>
            <p class="text-muted mb-0">
                 <?= e($nama_guru) ?>
            </p>
        </div>
        <a href="menu1guru.php" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-<?= e($jenis) ?>">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Form Catat Pelanggaran</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = $siswa_list->fetch_assoc()): ?>
                                <option value="<?= (int)$s['id'] ?>">
                                    <?= e($s['nama']) ?>
                                    (<?= e($s['nis']) ?>) -
                                    <?= e($s['nama_kelas']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Pelanggaran</label>
                        <select name="pelanggaran_id" id="pelanggaran"
                                class="form-select" required>
                            <option value="">-- Pilih Pelanggaran --</option>
                            <?php while ($p = $pelanggaran_list->fetch_assoc()): ?>
                                <option value="<?= (int)$p['id'] ?>"
                                        data-poin="<?= (int)$p['poin'] ?>">
                                    <?= e($p['kode']) ?> -
                                    <?= e($p['nama']) ?>
                                    (<?= (int)$p['poin'] ?> poin)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Poin</label>
                        <input type="number" id="poin"
                               class="form-control" value="0" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Kejadian</label>
                        <input type="date" name="tanggal"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>"
                               max="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"
                                  rows="3" required
                                  placeholder="Tuliskan keterangan kejadian"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Tindakan</label>
                        <select name="tindakan" class="form-select" required>
                            <option value="">-- Pilih Tindakan --</option>
                            <option value="Teguran lisan">Teguran lisan</option>
                            <option value="Pembinaan">Pembinaan</option>
                            <option value="Pembinaan oleh wali kelas">
                                Pembinaan oleh wali kelas
                            </option>
                            <option value="Pemanggilan siswa">
                                Pemanggilan siswa
                            </option>
                            <option value="Pemanggilan orang tua">
                                Pemanggilan orang tua
                            </option>
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

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Riwayat Pelanggaran</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                    <?php if ($riwayat->num_rows): ?>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                Belum ada catatan pelanggaran.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('pelanggaran').addEventListener('change', function () {
    const option = this.options[this.selectedIndex];
    document.getElementById('poin').value = option.dataset.poin || 0;
});
</script>

</body>
</html>


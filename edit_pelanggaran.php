<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: Kelola_Jenis_Pelanggaran.php");
    exit;
}

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Jenis pelanggaran tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int)($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int)($_POST['status_aktif'] ?? -1);

    if ($kode === '' || $nama === '' || $poin < 0 || !in_array($status_aktif, [0, 1], true)) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $update = mysqli_prepare($koneksi,
            "UPDATE t_pelanggaran
             SET kode = ?, nama = ?, poin = ?, deskripsi = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update, "ssisii", $kode, $nama, $poin, $deskripsi, $status_aktif, $id
        );

        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            header("Location: menu8.php");
            exit;
        } else {
            if (mysqli_stmt_errno($update) == 1062) {
                $pesan = "Kode pelanggaran sudah terdaftar.";
            } else {
                $pesan = "Gagal mengubah jenis pelanggaran: " . mysqli_stmt_error($update);
            }
        }

        mysqli_stmt_close($update);
    }

    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['poin'] = $poin;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}

function e($data) {
    return htmlspecialchars((string)($data ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jenis Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
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
                    <a href="Kelola_Siswa.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Guru.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kelas.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Tahun_Ajaran.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                 <li class="nav-item mb-2">
                    <a href="Penempatan_Siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Wali_Kelas.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Kategori_Pelanggaran.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_Jenis_Pelanggaran.php" class="nav-link active">
                        Kelola Jenis Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Cetak_Export.php" class="nav-link text-white">
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

            <h2 class="mb-4">Edit Jenis Pelanggaran</h2>

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesan) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" name="kode" maxlength="50"
                           class="form-control"
                           placeholder="Masukkan kode pelanggaran"
                           value="<?= e($data['kode']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggaran</label>
                    <input type="text" name="nama" maxlength="150"
                           class="form-control"
                           placeholder="Masukkan nama pelanggaran"
                           value="<?= e($data['nama']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Poin</label>
                    <input type="number" name="poin"
                           class="form-control"
                           placeholder="Masukkan jumlah poin"
                           value="<?= (int)$data['poin'] ?>"
                           min="0"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi"
                              class="form-control"
                              rows="3"
                              placeholder="Masukkan deskripsi (opsional)"><?= e($data['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Aktif</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                            Aktif
                        </option>
                        <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="simpan" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                    <a href="Kelola_Jenis_Pelanggaran.php" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </main>

    </div>
</div>

</body>
</html>
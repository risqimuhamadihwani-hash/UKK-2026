
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

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
            $tahun_ajaran_id, $kelas_id
        );

        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if ($status_aktif == 1 &&
            mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Kelas tersebut sudah memiliki wali kelas aktif.";
        } else {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO t_wali_kelas
                (tahun_ajaran_id, kelas_id, guru_id,
                 tanggal_mulai, tanggal_selesai, status_aktif)
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

$tahun = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     ORDER BY id DESC"
);

$kelas = mysqli_query($koneksi,
    "SELECT id, nama
     FROM t_kelas
     ORDER BY nama ASC"
);

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
</head>
<body>

<h2>Tambah Wali Kelas</h2>

<?php if ($pesan != ""): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Tahun Ajaran</label><br>
    <select name="tahun_ajaran_id" required>
        <option value="">-- Pilih Tahun Ajaran --</option>

        <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
            <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Kelas</label><br>
    <select name="kelas_id" required>
        <option value="">-- Pilih Kelas --</option>

        <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= $k['id'] ?>">
                <?= htmlspecialchars($k['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Guru</label><br>
    <select name="guru_id" required>
        <option value="">-- Pilih Guru --</option>

        <?php while ($g = mysqli_fetch_assoc($guru)): ?>
            <option value="<?= $g['id'] ?>">
                <?= htmlspecialchars($g['nip'] . ' - ' . $g['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="tanggal_mulai" required>

    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="tanggal_selesai" required>

    <br><br>

    <label>Status Aktif</label><br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>

    <br><br>

    <button type="submit" name="simpan">Simpan Wali Kelas</button>
    <a href="menu6.php">Kembali</a>

</form>

</body>
</html>

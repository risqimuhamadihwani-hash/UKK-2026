<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $siswa_id = (int)$_POST['siswa_id'];
    $tahun_ajaran_id = (int)$_POST['tahun_ajaran_id'];
    $kelas_id = (int)$_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int)$_POST['status_aktif'];

    if (
        $siswa_id <= 0 || $tahun_ajaran_id <= 0 ||
        $kelas_id <= 0 || $tanggal_mulai == "" ||
        $tanggal_selesai == "" ||
        $tanggal_selesai < $tanggal_mulai
    ) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_kelas_siswa
             WHERE siswa_id = ? AND tahun_ajaran_id = ?"
        );

        mysqli_stmt_bind_param(
            $cek, "ii", $siswa_id, $tahun_ajaran_id
        );
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Siswa sudah ditempatkan pada tahun ajaran tersebut.";
        } else {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO t_kelas_siswa
                (siswa_id, tahun_ajaran_id, kelas_id,
                 tanggal_mulai, tanggal_selesai, status_aktif)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt, "iiissi",
                $siswa_id, $tahun_ajaran_id, $kelas_id,
                $tanggal_mulai, $tanggal_selesai, $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);
                header("Location: menu5.php");
                exit;
            } else {
                $pesan = "Gagal menyimpan data.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

$siswa = mysqli_query($koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC");

$tahun = mysqli_query($koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");

$kelas = mysqli_query($koneksi,
    "SELECT id, nama, tingkat, jurusan FROM t_kelas
     WHERE status_aktif = 1 ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penempatan Siswa</title>
</head>
<body>

<h2>Tambah Penempatan Siswa</h2>

<?php if ($pesan != ""): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Siswa</label><br>
    <select name="siswa_id" required>
        <option value="">-- Pilih Siswa --</option>
        <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

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
                <?= htmlspecialchars(
                    $k['nama'] . ' - ' .
                    $k['tingkat'] . ' - ' . $k['jurusan']
                ) ?>
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

    <button type="submit" name="simpan">Simpan Penempatan</button>
    <a href="menu5.php">Kembali</a>
</form>

</body>
</html>

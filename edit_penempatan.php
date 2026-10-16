<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_kelas_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$d) {
    header("Location: menu5.php");
    exit;
}

$pesan = "";

if (isset($_POST['update'])) {
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
             WHERE siswa_id = ?
             AND tahun_ajaran_id = ?
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cek, "iii", $siswa_id, $tahun_ajaran_id, $id
        );
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Siswa sudah ditempatkan pada tahun ajaran tersebut.";
        } else {
            $update = mysqli_prepare($koneksi,
                "UPDATE t_kelas_siswa
                 SET siswa_id = ?, tahun_ajaran_id = ?,
                     kelas_id = ?, tanggal_mulai = ?,
                     tanggal_selesai = ?, status_aktif = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update, "iiissii",
                $siswa_id, $tahun_ajaran_id, $kelas_id,
                $tanggal_mulai, $tanggal_selesai,
                $status_aktif, $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                mysqli_stmt_close($cek);
                header("Location: menu5.php");
                exit;
            } else {
                $pesan = "Gagal memperbarui data.";
            }

            mysqli_stmt_close($update);
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
     WHERE status_aktif = 1 OR id = " . (int)$d['kelas_id'] . "
     ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penempatan Siswa</title>
</head>
<body>

<h2>Edit Penempatan Siswa</h2>

<?php if ($pesan != ""): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Siswa</label><br>
    <select name="siswa_id" required>
        <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
            <option value="<?= $s['id'] ?>"
                <?= $d['siswa_id'] == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tahun Ajaran</label><br>
    <select name="tahun_ajaran_id" required>
        <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
            <option value="<?= $t['id'] ?>"
                <?= $d['tahun_ajaran_id'] == $t['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Kelas</label><br>
    <select name="kelas_id" required>
        <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= $k['id'] ?>"
                <?= $d['kelas_id'] == $k['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    $k['nama'] . ' - ' .
                    $k['tingkat'] . ' - ' . $k['jurusan']
                ) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="tanggal_mulai"
        value="<?= htmlspecialchars($d['tanggal_mulai']) ?>" required>

    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="tanggal_selesai"
        value="<?= htmlspecialchars($d['tanggal_selesai']) ?>" required>

    <br><br>

    <label>Status Aktif</label><br>
    <select name="status_aktif" required>
        <option value="1"
            <?= $d['status_aktif'] == 1 ? 'selected' : '' ?>>
            Aktif
        </option>
        <option value="0"
            <?= $d['status_aktif'] == 0 ? 'selected' : '' ?>>
            Tidak Aktif
        </option>
    </select>

    <br><br>

    <button type="submit" name="update">Update</button>
    <a href="menu5.php">Kembali</a>
</form>

</body>
</html>

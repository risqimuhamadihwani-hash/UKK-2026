
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: menu6.php");
    exit;
}

$pesan = "";

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data wali kelas tidak ditemukan.");
}

if (isset($_POST['simpan'])) {
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $guru_id = (int) $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = (int) ($_POST['status_aktif'] ?? -1);

    if (
        $tahun_ajaran_id <= 0 ||
        $kelas_id <= 0 ||
        $guru_id <= 0 ||
        $tanggal_mulai == "" ||
        $tanggal_selesai == "" ||
        $tanggal_selesai < $tanggal_mulai ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Data tidak valid. Periksa kembali form.";
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_wali_kelas
             WHERE tahun_ajaran_id = ?
             AND kelas_id = ?
             AND status_aktif = 1
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cek, "iii",
            $tahun_ajaran_id, $kelas_id, $id
        );
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (
            $status_aktif == 1 &&
            mysqli_stmt_num_rows($cek) > 0
        ) {
            $pesan = "Kelas tersebut sudah memiliki wali kelas aktif.";
            mysqli_stmt_close($cek);
        } else {
            mysqli_stmt_close($cek);

            $update = mysqli_prepare($koneksi,
                "UPDATE t_wali_kelas
                 SET tahun_ajaran_id = ?,
                     kelas_id = ?,
                     guru_id = ?,
                     tanggal_mulai = ?,
                     tanggal_selesai = ?,
                     status_aktif = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update, "iiissii",
                $tahun_ajaran_id,
                $kelas_id,
                $guru_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: menu6.php");
                exit;
            } else {
                $pesan = "Gagal mengubah data wali kelas.";
                mysqli_stmt_close($update);
            }
        }
    }

    $data['tahun_ajaran_id'] = $tahun_ajaran_id;
    $data['kelas_id'] = $kelas_id;
    $data['guru_id'] = $guru_id;
    $data['tanggal_mulai'] = $tanggal_mulai;
    $data['tanggal_selesai'] = $tanggal_selesai;
    $data['status_aktif'] = $status_aktif;
}

$tahun = mysqli_query($koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$kelas = mysqli_query($koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$guru = mysqli_query($koneksi,
    "SELECT id, nip, nama FROM t_guru ORDER BY nama ASC"
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
    <title>Edit Wali Kelas</title>
</head>
<body>

<h2>Edit Wali Kelas</h2>

<?php if ($pesan != ""): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Tahun Ajaran</label><br>
    <select name="tahun_ajaran_id" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
            <option value="<?= (int) $t['id'] ?>"
                <?= $data['tahun_ajaran_id'] == $t['id']
                    ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Kelas</label><br>
    <select name="kelas_id" required>
        <option value="">-- Pilih Kelas --</option>
        <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= (int) $k['id'] ?>"
                <?= $data['kelas_id'] == $k['id']
                    ? 'selected' : '' ?>>
                <?= htmlspecialchars($k['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Guru</label><br>
    <select name="guru_id" required>
        <option value="">-- Pilih Guru --</option>
        <?php while ($g = mysqli_fetch_assoc($guru)): ?>
            <option value="<?= (int) $g['id'] ?>"
                <?= $data['guru_id'] == $g['id']
                    ? 'selected' : '' ?>>
                <?= htmlspecialchars($g['nip'] . ' - ' . $g['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="tanggal_mulai"
        value="<?= htmlspecialchars($data['tanggal_mulai']) ?>"
        required>

    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="tanggal_selesai"
        value="<?= htmlspecialchars($data['tanggal_selesai']) ?>"
        required>

    <br><br>

    <label>Status Aktif</label><br>
    <select name="status_aktif" required>
        <option value="1"
            <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
            Aktif
        </option>
        <option value="0"
            <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
            Tidak Aktif
        </option>
    </select>

    <br><br>

    <button type="submit" name="simpan">Simpan Perubahan</button>
    <a href="menu6.php">Kembali</a>

</form>

</body>
</html>

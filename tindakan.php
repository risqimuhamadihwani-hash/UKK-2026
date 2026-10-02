<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Hanya guru
if ($_SESSION['role'] != 'guru') {
    header("Location: dashboard.php");
    exit;
}

include "config/koneksi.php";

$pesan = "";

// =============================
// PROSES SIMPAN TINDAKAN
// =============================
if (isset($_POST['simpan'])) {

    $id = $_POST['id'];
    $tindakan = $_POST['tindakan'];

    $query = "
        UPDATE t_pelanggaran_siswa
        SET tindakan = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $tindakan,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Tindakan berhasil disimpan.";
    } else {
        $pesan = "Gagal menyimpan tindakan: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
}


// =============================
// AMBIL DATA PELANGGARAN
// =============================
$query = "
    SELECT
        ps.id,
        s.nama AS nama_siswa,
        pk.nama AS nama_pelanggaran,
        ps.tanggal,
        ps.keterangan,
        ps.tindakan
    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_siswa s
        ON ps.siswa_id = s.id

    LEFT JOIN t_pelanggaran_kategori pk
        ON ps.pelanggaran_kategori_id = pk.id

    ORDER BY ps.tanggal DESC, ps.id DESC
";

$data = mysqli_query($koneksi, $query);

if (!$data) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tindakan Pelanggaran</title>
</head>

<body>

<h2>Tindakan Pelanggaran</h2>

<p>
    Selamat datang,
    <b><?= htmlspecialchars($_SESSION['name']); ?></b>
</p>

<hr>

<?php if ($pesan != "") : ?>

    <p>
        <b><?= htmlspecialchars($pesan); ?></b>
    </p>

<?php endif; ?>


<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Siswa</th>
        <th>Pelanggaran</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Tindakan</th>
        <th>Aksi</th>
    </tr>

<?php

$no = 1;

if (mysqli_num_rows($data) > 0):

    while ($row = mysqli_fetch_assoc($data)):

?>

    <tr>

        <td>
            <?= $no++; ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['tanggal']); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['keterangan'] ?? '-'); ?>
        </td>

        <td>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $row['id']; ?>"
                >

                <textarea
                    name="tindakan"
                    rows="3"
                    cols="25"
                    placeholder="Masukkan tindakan..."
                ><?= htmlspecialchars($row['tindakan'] ?? ''); ?></textarea>

        </td>

        <td>

                <button
                    type="submit"
                    name="simpan"
                >
                    Simpan
                </button>

            </form>

        </td>

    </tr>

<?php

    endwhile;

else:

?>

    <tr>
        <td colspan="7">
            Belum ada data pelanggaran.
        </td>
    </tr>

<?php endif; ?>

</table>

<br>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

</body>
</html>
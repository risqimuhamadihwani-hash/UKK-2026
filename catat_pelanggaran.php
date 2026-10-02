<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Hanya guru yang boleh mencatat pelanggaran
if ($_SESSION['role'] != 'guru') {
    header("Location: dashboard.php");
    exit;
}

include "config/koneksi.php";

$pesan = "";

// =========================
// PROSES SIMPAN
// =========================
if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $pelanggaran_kategori_id = $_POST['pelanggaran_kategori_id'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];

    // Simpan ke database
    $query = "INSERT INTO t_pelanggaran_siswa
              (siswa_id, pelanggaran_kategori_id, tanggal, keterangan, created_at, updated_at)
              VALUES (?, ?, ?, ?, NOW(), NOW())";

    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "iiss",
        $siswa_id,
        $pelanggaran_kategori_id,
        $tanggal,
        $keterangan
    );

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Data pelanggaran berhasil disimpan.";
    } else {
        $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
}


// =========================
// AMBIL DATA SISWA
// =========================
$data_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);


// =========================
// AMBIL DATA KATEGORI
// =========================
$data_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Catat Pelanggaran</title>
</head>

<body>

<h2>Catat Pelanggaran</h2>

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


<h3>Form Catat Pelanggaran</h3>

<form method="POST">

    <label>Siswa</label><br>

    <select name="siswa_id" required>

        <option value="">-- Pilih Siswa --</option>

        <?php while ($siswa = mysqli_fetch_assoc($data_siswa)) : ?>

            <option value="<?= $siswa['id']; ?>">
                <?= htmlspecialchars($siswa['nama']); ?>
            </option>

        <?php endwhile; ?>

    </select>

    <br><br>


    <label>Kategori Pelanggaran</label><br>

    <select name="pelanggaran_kategori_id" required>

        <option value="">
            -- Pilih Pelanggaran --
        </option>

        <?php while ($kategori = mysqli_fetch_assoc($data_kategori)) : ?>

            <option value="<?= $kategori['id']; ?>">
                <?= htmlspecialchars($kategori['nama']); ?>
            </option>

        <?php endwhile; ?>

    </select>

    <br><br>


    <label>Tanggal</label><br>

    <input
        type="date"
        name="tanggal"
        value="<?= date('Y-m-d'); ?>"
        required
    >

    <br><br>


    <label>Keterangan</label><br>

    <textarea
        name="keterangan"
        rows="4"
        cols="40"
        placeholder="Masukkan keterangan pelanggaran..."
    ></textarea>

    <br><br>


    <button type="submit" name="simpan">
        Simpan
    </button>

</form>

<br>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

</body>
</html>
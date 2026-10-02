<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID siswa tidak valid.");
}

/* Ambil data siswa */
$sql = "SELECT * FROM t_siswa WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

/* Proses update */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $sql = "UPDATE t_siswa SET
            nis = ?,
            nisn = ?,
            nama = ?,
            jenis_kelamin = ?,
            tanggal_lahir = ?,
            alamat = ?,
            status_aktif = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";

    $stmt = $koneksi->prepare($sql);

    $stmt->bind_param(
        "ssssssii",
        $nis,
        $nisn,
        $nama,
        $jenis_kelamin,
        $tanggal_lahir,
        $alamat,
        $status_aktif,
        $id
    );

    if ($stmt->execute()) {
        header("Location: menu1.php");
        exit;
    } else {
        echo "Gagal mengubah data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
</head>

<body>

<h1>Edit Siswa</h1>

<form method="POST">

    NIS:<br>
    <input type="text" name="nis"
           value="<?= htmlspecialchars($siswa['nis']); ?>"
           required>

    <br><br>

    NISN:<br>
    <input type="text" name="nisn"
           value="<?= htmlspecialchars($siswa['nisn']); ?>">

    <br><br>

    Nama Siswa:<br>
    <input type="text" name="nama"
           value="<?= htmlspecialchars($siswa['nama']); ?>"
           required>

    <br><br>

    Jenis Kelamin:<br>
    <select name="jenis_kelamin" required>

        <option value="L"
            <?= $siswa['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
            Laki-laki
        </option>

        <option value="P"
            <?= $siswa['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
            Perempuan
        </option>

    </select>

    <br><br>

    Tanggal Lahir:<br>
    <input type="date" name="tanggal_lahir"
           value="<?= htmlspecialchars($siswa['tanggal_lahir']); ?>">

    <br><br>

    Alamat:<br>
    <textarea name="alamat"><?= htmlspecialchars($siswa['alamat']); ?></textarea>

    <br><br>

    Status Aktif:<br>
    <select name="status_aktif">

        <option value="1"
            <?= $siswa['status_aktif'] == 1 ? 'selected' : ''; ?>>
            Aktif
        </option>

        <option value="0"
            <?= $siswa['status_aktif'] == 0 ? 'selected' : ''; ?>>
            Tidak Aktif
        </option>

    </select>

    <br><br>

    <button type="submit">Simpan Perubahan</button>

    <a href="menu1.php">Kembali</a>

</form>

</body>
</html>
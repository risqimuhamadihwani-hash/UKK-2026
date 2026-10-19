<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("ID kelas tidak valid. <a href='menu3.php'>Kembali</a>");
}

$sql = "SELECT id, nama, tingkat, jurusan, status_aktif
        FROM t_kelas
        WHERE id = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$kelas = $result->fetch_assoc();
$stmt->close();

if (!$kelas) {
    die("Data kelas tidak ditemukan. <a href='menu3.php'>Kembali</a>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $status_aktif = (int) $_POST['status_aktif'];

    $sql = "UPDATE t_kelas
            SET nama = ?, tingkat = ?, jurusan = ?,
                status_aktif = ?, updated_at = NOW()
            WHERE id = ?";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param(
        "sssii",
        $nama,
        $tingkat,
        $jurusan,
        $status_aktif,
        $id
    );

    if ($stmt->execute()) {
        $stmt->close();
        $koneksi->close();
        header("Location: menu3.php");
        exit;
    } else {
        echo "Gagal mengubah data: " . $stmt->error;
    }

    $stmt->close();

    $kelas['nama'] = $nama;
    $kelas['tingkat'] = $tingkat;
    $kelas['jurusan'] = $jurusan;
    $kelas['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas</title>
</head>
<body>

<h2>Edit Data Kelas</h2>

<form method="POST" action="edit_kelas.php?id=<?= $id; ?>">

    <label>Nama Kelas</label><br>
    <input type="text" name="nama"
        value="<?= htmlspecialchars($kelas['nama']); ?>" required>
    <br><br>

    <label>Tingkat</label><br>
    <select name="tingkat" required>
    <option value="">-- Pilih Tingkat --</option>

    <option value="X"
        <?= $kelas['tingkat'] == 'X' ? 'selected' : ''; ?>>
        X
    </option>

    <option value="XI"
        <?= $kelas['tingkat'] == 'XI' ? 'selected' : ''; ?>>
        XI
    </option>

    <option value="XII"
        <?= $kelas['tingkat'] == 'XII' ? 'selected' : ''; ?>>
        XII
    </option>
</select>

<br><br>

    <label>Jurusan</label><br>
    <input type="text" name="jurusan"
        value="<?= htmlspecialchars($kelas['jurusan']); ?>" required>
    <br><br>

    <label>Status</label><br>
    <select name="status_aktif" required>
        <option value="1"
            <?= $kelas['status_aktif'] == 1 ? 'selected' : ''; ?>>
            Aktif
        </option>

        <option value="0"
            <?= $kelas['status_aktif'] == 0 ? 'selected' : ''; ?>>
            Tidak Aktif
        </option>
    </select>

    <br><br>

    <button type="submit">Simpan Perubahan</button>
    <a href="menu3.php">Kembali</a>

</form>

</body>
</html>

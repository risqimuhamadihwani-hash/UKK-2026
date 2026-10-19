<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: menu1.php");
    exit;
}

$nis = trim($_POST['nis'] ?? '');
$nisn = trim($_POST['nisn'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$alamat = trim($_POST['alamat'] ?? '');
$status_aktif = (int)($_POST['status_aktif'] ?? 1);

if (
    $nis === '' || $nama === '' ||
    !in_array($jenis_kelamin, ['L', 'P'], true) ||
    $tanggal_lahir === '' ||
    !in_array($status_aktif, [0, 1], true)
) {
    die("Data belum lengkap atau tidak valid. Silakan kembali dan periksa form.");
}

$sql = "INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    die("Query gagal: " . $koneksi->error);
}

$stmt->bind_param(
    "ssssssi",
    $nis,
    $nisn,
    $nama,
    $jenis_kelamin,
    $tanggal_lahir,
    $alamat,
    $status_aktif
);

if ($stmt->execute()) {
    header("Location: menu1.php");
    exit;
} else {
    echo "Gagal menambahkan siswa: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$koneksi->close();
?>

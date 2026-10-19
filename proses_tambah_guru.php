
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: menu2.php");
    exit;
}

$nip = trim($_POST['nip'] ?? '');
$nama_guru = trim($_POST['nama_guru'] ?? '');
$email = trim($_POST['email'] ?? '');
$status_aktif = isset($_POST['status_aktif'])
    ? (int) $_POST['status_aktif']
    : 1;

if (
    $nip === '' ||
    $nama_guru === '' ||
    !in_array($status_aktif, [0, 1], true) ||
    ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
) {
    die("Data tidak valid. Silakan kembali dan periksa form.");
}

/* Simpan data guru */
$sql = "INSERT INTO t_guru (nip, nama_guru, email, status_aktif)
        VALUES (?, ?, NULLIF(?, ''), ?)";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    die("Query gagal: " . $koneksi->error);
}

$stmt->bind_param(
    "sssi",
    $nip,
    $nama_guru,
    $email,
    $status_aktif
);

if ($stmt->execute()) {
    $stmt->close();
    $koneksi->close();

    header("Location: menu2.php");
    exit;
} else {
    echo "Gagal menambahkan guru: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$koneksi->close();
?>

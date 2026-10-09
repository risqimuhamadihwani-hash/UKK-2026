<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

<h2>Dashboard</h2>

<p>Selamat datang, <?= htmlspecialchars($name); ?></p>
<p>Role: <?= htmlspecialchars($role); ?></p>

<hr>

<?php if ($role == 'admin') : ?>

    <h3>Menu Admin</h3>

    <a href="menu1.php">Kelola Siswa</a><br>
    <a href="menu2.php">Kelola Guru</a><br>
    <a href="menu3.php">Kelola Kelas</a><br>
    <a href="menu4.php">Kelola Tahun Ajaran</a><br>
    <a href="menu5.php">Kelola Wali Kelas</a><br>
    <a href="menu6.php">Penempatan Siswa</a><br>
    <a href="menu7.php">Kelola Kategori Pelanggaran</a><br>

<?php elseif ($role == 'guru') : ?>

    <h3>Menu Guru</h3>

    <a href="catat_pelanggaran.php">Catat Pelanggaran</a><br>
    <a href="tindakan.php">Tindakan</a><br>
    <a href="riwayat.php">Riwayat</a><br>
    <a href="rekap_poin.php">Rekap Poin</a><br>

<?php elseif ($role == 'wali_kelas') : ?>

    <h3>Menu Wali Kelas</h3>

    <a href="laporan.php">Laporan</a><br>
    <a href="riwayat.php">Riwayat</a><br>
    <a href="rekap_poin.php">Rekap Poin</a><br>

<?php else : ?>

    <p>Role tidak dikenali.</p>

<?php endif; ?>

<br>

<a href="logout.php">Logout</a>

</body>
</html>
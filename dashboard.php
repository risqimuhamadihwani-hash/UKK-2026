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

    <a href="siswa.php">Kelola guru</a><br>
    <a href="menu2.php">kelola siswa</a><br>
    <a href="menu3.php">Menu 3</a><br>
    <a href="menu4.php">Menu 4</a><br>

<?php elseif ($role == 'guru') : ?>

    <a href="menu3.php">Menu 3</a><br>
    <a href="menu4.php">Menu 4</a><br>

<?php endif; ?>

<br>
<a href="logout.php">Logout</a>

</body>
</html>
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $status_aktif = (int) $_POST['status_aktif'];
    $user_id = (int) $_POST['user_id'];

    if ($nip == "" || $nama == "" || $email == "" || $user_id <= 0) {
        $pesan = "Semua data wajib diisi dan akun guru harus dipilih.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid.";
    } else {
        // Cek apakah NIP sudah terdaftar
        $cek = $koneksi->prepare(
            "SELECT id FROM t_guru WHERE nip = ?"
        );
        $cek->bind_param("s", $nip);
        $cek->execute();
        $hasil = $cek->get_result();

        if ($hasil->num_rows > 0) {
            $pesan = "NIP sudah terdaftar.";
        } else {
            // Cek apakah akun tersedia
            $akun = $koneksi->prepare(
                "SELECT id FROM t_users
                 WHERE id = ? AND role = 'guru'"
            );
            $akun->bind_param("i", $user_id);
            $akun->execute();
            $hasil_akun = $akun->get_result();

            if ($hasil_akun->num_rows == 0) {
                $pesan = "Akun guru tidak ditemukan.";
            } else {
                // Cek apakah akun sudah terhubung ke guru lain
                $cek_user = $koneksi->prepare(
                    "SELECT id FROM t_guru WHERE user_id = ?"
                );
                $cek_user->bind_param("i", $user_id);
                $cek_user->execute();
                $hasil_user = $cek_user->get_result();

                if ($hasil_user->num_rows > 0) {
                    $pesan = "Akun tersebut sudah digunakan guru lain.";
                } else {
                    $stmt = $koneksi->prepare(
                        "INSERT INTO t_guru
                        (nip, nama, email, status_aktif, user_id,
                         created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
                    );

                    $stmt->bind_param(
                        "sssii",
                        $nip,
                        $nama,
                        $email,
                        $status_aktif,
                        $user_id
                    );

                    if ($stmt->execute()) {
                        $stmt->close();
                        $cek_user->close();
                        $akun->close();
                        $cek->close();
                        $koneksi->close();

                        header("Location: menu2.php");
                        exit;
                    } else {
                        $pesan = "Gagal menyimpan data guru: "
                               . $stmt->error;
                    }

                    $stmt->close();
                }

                $cek_user->close();
            }

            $akun->close();
        }

        $cek->close();
    }
}

// Ambil akun yang memiliki role guru dan belum terhubung ke guru
$data_akun = $koneksi->query(
    "SELECT u.id, u.name
     FROM t_users u
     LEFT JOIN t_guru g ON g.user_id = u.id
     WHERE u.role = 'guru' AND g.id IS NULL
     ORDER BY u.id ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Guru</title>
</head>
<body>

<h1>Tambah Data Guru</h1>

<?php if ($pesan != "") { ?>
    <p style="color:red;">
        <?= htmlspecialchars($pesan); ?>
    </p>
<?php } ?>

<form method="POST">
    <label>NIP</label><br>
    <input
        type="text"
        name="nip"
        maxlength="30"
        value="<?= htmlspecialchars($_POST['nip'] ?? ''); ?>"
        required
    >
    <br><br>

    <label>Nama Guru</label><br>
    <input
        type="text"
        name="nama"
        maxlength="100"
        value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
        required
    >
    <br><br>

    <label>Email</label><br>
    <input
        type="email"
        name="email"
        maxlength="100"
        value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
        required
    >
    <br><br>

    <label>Akun Guru</label><br>
    <select name="user_id" required>
        <option value="">-- Pilih Akun Guru --</option>

        <?php while ($akun = $data_akun->fetch_assoc()) { ?>
            <option
                value="<?= $akun['id']; ?>"
                <?= (isset($_POST['user_id']) &&
                    $_POST['user_id'] == $akun['id'])
                    ? 'selected' : ''; ?>
            >
                <?= htmlspecialchars($akun['name']); ?>
                (ID: <?= $akun['id']; ?>)
            </option>
        <?php } ?>
    </select>
    <br><br>

    <label>Status</label><br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>
    <br><br>

    <button type="submit" name="simpan">Simpan Guru</button>
    <a href="menu2.php"></a>
</form>

</body>
</html>

<?php
$koneksi->close();
?>

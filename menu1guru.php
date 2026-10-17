
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026", 3306);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");
$pesan = "";

// TAMBAH PELANGGARAN
if (isset($_POST["tambah"])) {
    $kategori = (int)($_POST["kategori"] ?? 0);
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $poin = (int)($_POST["poin"] ?? 0);
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $status = (int)($_POST["status_aktif"] ?? 1);

    if ($kategori <= 0 || $kode === "" || $nama === "") {
        $pesan = "Kategori, kode, dan nama pelanggaran wajib diisi.";
    } else {
        $stmt = $koneksi->prepare(
            "INSERT INTO t_pelanggaran
            (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issisi",
            $kategori, $kode, $nama, $poin, $deskripsi, $status
        );

        if ($stmt->execute()) {
            header("Location: catatan_pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal menambahkan data: " . $stmt->error;
        }

        $stmt->close();
    }
}

// HAPUS PELANGGARAN
if (isset($_GET["hapus"])) {
    $id = (int)$_GET["hapus"];

    // Jangan menghapus jika sudah dipakai pada catatan siswa.
    $stmt = $koneksi->prepare(
        "SELECT COUNT(*) AS jumlah
         FROM t_pelanggaran_siswa
         WHERE pelanggaran_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $jumlah = $stmt->get_result()->fetch_assoc()["jumlah"];
    $stmt->close();

    if ((int)$jumlah > 0) {
        $pesan = "Pelanggaran ini sudah dipakai dalam catatan siswa, jadi tidak dihapus.";
    } else {
        $stmt = $koneksi->prepare(
            "DELETE FROM t_pelanggaran WHERE id = ?"
        );
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: catatan_pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal menghapus data: " . $stmt->error;
        }

        $stmt->close();
    }
}

// AMBIL KATEGORI
$kategoriData = $koneksi->query(
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// AMBIL DAFTAR PELANGGARAN
$data = $koneksi->query(
    "SELECT p.id, p.pelanggaran_kategori_id, p.kode,
            p.nama, p.poin, p.deskripsi, p.status_aktif,
            k.nama AS nama_kategori
     FROM t_pelanggaran p
     LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
     ORDER BY p.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Catat Pelanggaran</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                JusticeAdmin
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu1guru.php" class="nav-link active">
                        Catat pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu2guru.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu3guru.php" class="nav-link text-white">
                        Rekap Poin
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu4guru.php" class="nav-link text-white">
                        Riwayat
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
<h2>Tambah Pelanggaran</h2>

<?php if ($pesan !== ""): ?>
    <p><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <table>
        <tr>
            <td>Kategori</td>
            <td>:</td>
            <td>
                <select name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php if ($kategoriData): ?>
                        <?php while ($k = $kategoriData->fetch_assoc()): ?>
                            <option value="<?= (int)$k["id"] ?>">
                                <?= htmlspecialchars($k["nama"] ?? "") ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Kode</td>
            <td>:</td>
            <td><input type="text" name="kode" placeholder="PG-001" required></td>
        </tr>
        <tr>
            <td>Nama Pelanggaran</td>
            <td>:</td>
            <td><input type="text" name="nama" required></td>
        </tr>
        <tr>
            <td>Poin</td>
            <td>:</td>
            <td><input type="number" name="poin" min="0" required></td>
        </tr>
        <tr>
            <td>Deskripsi</td>
            <td>:</td>
            <td><textarea name="deskripsi" rows="5"></textarea></td>
        </tr>
        <tr>
            <td>Status Aktif</td>
            <td>:</td>
            <td>
                <select name="status_aktif">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button type="submit" name="tambah">Tambah Pelanggaran</button>
            </td>
        </tr>
    </table>
</form>

<hr>

<h2>Data Pelanggaran</h2>

<div class="table-responsive">
    <table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Kode</th>
            <th>Nama Pelanggaran</th>
            <th>Poin</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($data): ?>
        <?php $no = 1; ?>
        <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row["nama_kategori"] ?? "-") ?></td>
                <td><?= htmlspecialchars($row["kode"] ?? "") ?></td>
                <td><?= htmlspecialchars($row["nama"] ?? "") ?></td>
                <td><?= (int)$row["poin"] ?></td>
                <td><?= htmlspecialchars($row["deskripsi"] ?? "") ?></td>
                <td>
                    <?= (int)$row["status_aktif"] === 1
                        ? "Aktif" : "Tidak Aktif" ?>
                </td>
                <td>
                    <a href="edit_pelanggaran.php?id=<?= (int)$row["id"] ?>">
                         <button type="button" class="btn btn-warning">Edit</button>
                    </a>

                    <a href="catatan_pelanggaran.php?hapus=<?= (int)$row["id"] ?>"
                       onclick="return confirm('Yakin ingin menghapus pelanggaran ini?')"
                        <button type="button" class="btn btn-danger">Hapus</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">
                Error membaca data: <?= htmlspecialchars($koneksi->error) ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>

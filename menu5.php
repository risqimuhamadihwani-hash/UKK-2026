
<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

include "config/koneksi.php";

$pesan = "";

// TAMBAH WALI KELAS
if (isset($_POST['simpan'])) {
    $tahun = (int) $_POST['tahun_ajaran_id'];
    $kelas = (int) $_POST['kelas_id'];
    $guru = (int) $_POST['guru_id'];
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $status = (int) $_POST['status_aktif'];

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO t_wali_kelas
        (tahun_ajaran_id, kelas_id, guru_id,
         tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt, "iiissi",
        $tahun, $kelas, $guru, $mulai, $selesai, $status
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: menu5.php");
        exit;
    } else {
        $pesan = "Gagal menyimpan: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// EDIT WALI KELAS
if (isset($_POST['ubah'])) {
    $id = (int) $_POST['id'];
    $tahun = (int) $_POST['tahun_ajaran_id'];
    $kelas = (int) $_POST['kelas_id'];
    $guru = (int) $_POST['guru_id'];
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $status = (int) $_POST['status_aktif'];

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE t_wali_kelas
         SET tahun_ajaran_id = ?,
             kelas_id = ?,
             guru_id = ?,
             tanggal_mulai = ?,
             tanggal_selesai = ?,
             status_aktif = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt, "iiissii",
        $tahun, $kelas, $guru, $mulai, $selesai, $status, $id
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: menu5.php");
        exit;
    } else {
        $pesan = "Gagal mengubah: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// HAPUS WALI KELAS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_wali_kelas WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: menu5.php");
        exit;
    } else {
        $pesan = "Gagal menghapus: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// DATA REFERENSI
$tahun_list = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$kelas_list = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama"
);

$guru_list = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama"
);

// DATA UNTUK EDIT
$edit = null;

if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM t_wali_kelas WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_edit);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $edit = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($stmt);
}

// DAFTAR WALI KELAS
$data = mysqli_query(
    $koneksi,
    "SELECT
        w.id,
        w.tahun_ajaran_id,
        w.kelas_id,
        w.guru_id,
        w.tanggal_mulai,
        w.tanggal_selesai,
        w.status_aktif,
        ta.nama AS tahun,
        k.nama AS kelas,
        g.nama AS guru
     FROM t_wali_kelas w
     LEFT JOIN t_tahun_ajaran ta
        ON w.tahun_ajaran_id = ta.id
     LEFT JOIN t_kelas k
        ON w.kelas_id = k.id
     LEFT JOIN t_guru g
        ON w.guru_id = g.id
     ORDER BY w.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Wali Kelas</title>
</head>
<body>

<h2>Kelola Wali Kelas</h2>

<p>
    Selamat datang,
    <b><?= htmlspecialchars($_SESSION['name']); ?></b>
</p>

<hr>

<?php if ($pesan != "") : ?>
    <p><?= htmlspecialchars($pesan); ?></p>
<?php endif; ?>

<h3><?= $edit ? 'Edit Data Wali Kelas' : 'Tambah Wali Kelas'; ?></h3>

<form method="POST" action="menu5.php">

    <?php if ($edit) : ?>
        <input type="hidden" name="id"
               value="<?= (int) $edit['id']; ?>">
    <?php endif; ?>

    <p>
        Tahun Ajaran<br>
        <select name="tahun_ajaran_id" required>
            <option value="">-- Pilih Tahun Ajaran --</option>
            <?php while ($t = mysqli_fetch_assoc($tahun_list)) : ?>
                <option value="<?= $t['id']; ?>"
                    <?= $edit && $edit['tahun_ajaran_id'] == $t['id']
                        ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($t['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        Kelas<br>
        <select name="kelas_id" required>
            <option value="">-- Pilih Kelas --</option>
            <?php while ($k = mysqli_fetch_assoc($kelas_list)) : ?>
                <option value="<?= $k['id']; ?>"
                    <?= $edit && $edit['kelas_id'] == $k['id']
                        ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($k['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        Guru<br>
        <select name="guru_id" required>
            <option value="">-- Pilih Guru --</option>
            <?php while ($g = mysqli_fetch_assoc($guru_list)) : ?>
                <option value="<?= $g['id']; ?>"
                    <?= $edit && $edit['guru_id'] == $g['id']
                        ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($g['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        Tanggal Mulai<br>
        <input type="date" name="tanggal_mulai"
               value="<?= $edit ? htmlspecialchars($edit['tanggal_mulai']) : ''; ?>"
               required>
    </p>

    <p>
        Tanggal Selesai<br>
        <input type="date" name="tanggal_selesai"
               value="<?= $edit && $edit['tanggal_selesai'] != '0000-00-00'
                   ? htmlspecialchars($edit['tanggal_selesai']) : ''; ?>"
               required>
    </p>

    <p>
        Status<br>
        <select name="status_aktif" required>
            <option value="1"
                <?= $edit && $edit['status_aktif'] == 1
                    ? 'selected' : ''; ?>>Aktif</option>
            <option value="0"
                <?= $edit && $edit['status_aktif'] == 0
                    ? 'selected' : ''; ?>>Tidak Aktif</option>
        </select>
    </p>

    <?php if ($edit) : ?>
        <button type="submit" name="ubah">Simpan Perubahan</button>
        <a href="menu5.php">Batal</a>
    <?php else : ?>
        <button type="submit" name="simpan">Simpan</button>
    <?php endif; ?>
    
    <br>

    <a href="dashboard.php">Kembali ke Dashboard</a>

</form>

<hr>

<h3>Daftar Wali Kelas</h3>

<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Guru</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php $no = 1; ?>

    <?php while ($row = mysqli_fetch_assoc($data)) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['tahun'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['kelas'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['guru'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
            <td><?= $row['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?></td>
            <td>
                <a href="menu5.php?edit=<?= (int) $row['id']; ?>">Edit</a> |
                <a href="menu5.php?hapus=<?= (int) $row['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                    Hapus
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>

</body>
</html>


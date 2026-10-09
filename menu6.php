
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
$edit = null;

// TAMBAH KATEGORI
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $poin = (int) $_POST['poin'];

    if ($nama == "" || $poin < 0) {
        $pesan = "Nama kategori wajib diisi dan poin tidak boleh negatif.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran_kategori (nama, poin)
             VALUES (?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "si", $nama, $poin);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: Kelola_Kategori_Pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal menambahkan kategori: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

// AMBIL DATA UNTUK DIEDIT
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama, poin
         FROM t_pelanggaran_kategori
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $hasil_edit = mysqli_stmt_get_result($stmt);
    $edit = mysqli_fetch_assoc($hasil_edit);

    mysqli_stmt_close($stmt);
}

// UPDATE KATEGORI
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama']);
    $poin = (int) $_POST['poin'];

    if ($nama == "" || $poin < 0) {
        $pesan = "Nama kategori wajib diisi dan poin tidak boleh negatif.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_kategori
             SET nama = ?, poin = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "sii", $nama, $poin, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: Kelola%20Kategori%20Pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal mengubah kategori: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

// HAPUS KATEGORI
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // Cek apakah kategori masih dipakai oleh data pelanggaran
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_pelanggaran_siswa
         WHERE pelanggaran_kategori_id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);

    $hasil_cek = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil_cek) > 0) {
        $pesan = "Kategori tidak bisa dihapus karena sudah digunakan pada data pelanggaran.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_stmt_close($cek);

            header("Location: Kelola%20Kategori%20Pelanggaran.php");
            exit;
        } else {
            $pesan = "Gagal menghapus kategori: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($cek);
}

// DAFTAR KATEGORI
$data = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin
     FROM t_pelanggaran_kategori
     ORDER BY id DESC"
);

if (!$data) {
    die("Query kategori gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori Pelanggaran</title>
</head>
<body>

<h2>Kelola Kategori Pelanggaran</h2>

<p>
    Selamat datang,
    <b><?= htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></b>
</p>

<hr>

<?php if ($pesan != "") : ?>
    <p><?= htmlspecialchars($pesan); ?></p>
<?php endif; ?>

<h3><?= $edit ? 'Edit Kategori Pelanggaran' : 'Tambah Kategori Pelanggaran'; ?></h3>

<form method="POST" action="Kelola Kategori Pelanggaran.php">
    <?php if ($edit) : ?>
        <input type="hidden" name="id" value="<?= (int) $edit['id']; ?>">
    <?php endif; ?>

    <label>Nama Kategori</label><br>
    <input
        type="text"
        name="nama"
        maxlength="100"
        value="<?= htmlspecialchars($edit['nama'] ?? ''); ?>"
        required
    >
    <br><br>

    <label>Poin</label><br>
    <input
        type="number"
        name="poin"
        min="0"
        value="<?= htmlspecialchars((string) ($edit['poin'] ?? '')); ?>"
        required
    >
    <br><br>

    <?php if ($edit) : ?>
        <button type="submit" name="update">Update</button>
        <a href="Kelola Kategori Pelanggaran.php">Batal</a>
    <?php else : ?>
        <button type="submit" name="simpan">Simpan</button>
    <?php endif; ?>
</form>

<hr>

<h3>Daftar Kategori Pelanggaran</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Kategori</th>
        <th>Poin</th>
        <th>Aksi</th>
    </tr>

    <?php $no = 1; ?>

    <?php while ($row = mysqli_fetch_assoc($data)) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= (int) $row['poin']; ?></td>
            <td>
                <a href="Kelola Kategori Pelanggaran.php?edit=<?= (int) $row['id']; ?>">
                    Edit
                </a>

                |

                <a
                    href="Kelola Kategori Pelanggaran.php?hapus=<?= (int) $row['id']; ?>"
                    onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                >
                    Hapus
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>

<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>
```

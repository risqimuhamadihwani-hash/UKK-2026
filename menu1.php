<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$sql = "
    SELECT
        id,
        nis,
        nisn,
        nama,
        jenis_kelamin,
        tanggal_lahir,
        alamat,
        status_aktif
    FROM t_siswa
    ORDER BY id ASC
";

$result = $koneksi->query($sql);

if (!$result) {
    die("Query gagal: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet"
    href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
    <meta charset="UTF-8">

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
                    <a href="menu1.php" class= "nav-link active">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu2.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu3.php" class="nav-link text-white">
                        Kelola Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu4.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu5.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu6.php" class="nav-link text-white">
                        Kelola Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu7.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu8.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
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
<h1>Kelola Siswa</h1>

<p>Halaman untuk mengelola data siswa</p>

<!-- FORM TAMBAH SISWA -->
<h2 class="mb-3">Tambah Siswa</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="proses_tambah_siswa.php" method="POST">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">NIS:</label>
                    <input type="text" name="nis" class="form-control"
                           placeholder="Masukkan NIS" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">NISN:</label>
                    <input type="text" name="nisn" class="form-control"
                           placeholder="Masukkan NISN">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Siswa:</label>
                    <input type="text" name="nama" class="form-control"
                           placeholder="Masukkan Nama Siswa" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Jenis Kelamin:</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Lahir:</label>
                    <input type="date" name="tanggal_lahir"
                           class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Alamat:</label>
                    <textarea name="alamat" class="form-control"
                              rows="2" placeholder="Masukkan Alamat"></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Status Aktif:</label>
                    <select name="status_aktif" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                + Tambah Siswa
            </button>

            <button type="reset" class="btn btn-secondary">
                Reset
            </button>
        </form>
    </div>
</div>

<hr class="my-4">

<table id="tabelSiswa" class="table table-striped table-bordered table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>NIS</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($siswa = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($siswa['id']); ?></td>
            <td><?= htmlspecialchars($siswa['nis']); ?></td>
            <td><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($siswa['nama']); ?></td>

            <td>
                <?php
                if ($siswa['jenis_kelamin'] == 'L') {
                    echo "Laki-laki";
                } elseif ($siswa['jenis_kelamin'] == 'P') {
                    echo "Perempuan";
                } else {
                    echo "-";
                }
                ?>
            </td>

            <td><?= htmlspecialchars($siswa['tanggal_lahir'] ?? '-'); ?></td>
            <td><?= htmlspecialchars($siswa['alamat'] ?? '-'); ?></td>

            <td>
                <?php if ($siswa['status_aktif'] == 1) { ?>
                    <span class="badge bg-success">Aktif</span>
                <?php } else { ?>
                    <span class="badge bg-secondary">Tidak Aktif</span>
                <?php } ?>
            </td>

            <td>
                <a href="edit_siswa.php?id=<?= (int)$siswa['id']; ?>"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <a href="hapus_siswa.php?id=<?= (int)$siswa['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus siswa ini?')"
                   class="btn btn-danger btn-sm">
                    Hapus
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        language: {
            search: "Cari siswa:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
            infoEmpty: "Tidak ada data siswa",
            zeroRecords: "Data siswa tidak ditemukan",
            emptyTable: "Belum ada data siswa",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>
</body>
</html>
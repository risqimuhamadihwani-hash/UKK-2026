<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pesan = "";

if ($id <= 0) {
    header("Location: menu3.php");
    exit;
}

// Ambil data kelas
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
    $koneksi->close();
    header("Location: menu3.php");
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    if ($nama === '' || $tingkat === '' || $jurusan === '') {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
        $pesan = "Tingkat kelas tidak valid.";
    } elseif (!in_array($status_aktif, ['0', '1'], true)) {
        $pesan = "Status kelas tidak valid.";
    } else {
        $status_aktif = (int) $status_aktif;

        $sql = "UPDATE t_kelas
                SET nama = ?,
                    tingkat = ?,
                    jurusan = ?,
                    status_aktif = ?,
                    updated_at = NOW()
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
            $pesan = "Gagal mengubah data kelas.";
        }

        $stmt->close();
    }

    // Pertahankan input jika validasi gagal
    $kelas['nama'] = $nama;
    $kelas['tingkat'] = $tingkat;
    $kelas['jurusan'] = $jurusan;
    $kelas['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f1f5f9;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #1e293b;
        }

        .topbar {
            background: #ffffff;
            padding: 18px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
        }

        .page-container {
            max-width: 900px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h1 {
            font-size: 29px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #64748b;
            margin: 0;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
        }

        .card-heading {
            padding: 24px 28px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-heading h2 {
            font-size: 19px;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .card-heading p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .form-content {
            padding: 28px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 9px;
        }

        .form-control,
        .form-select {
            min-height: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 11px 13px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.13);
        }

        .form-text {
            color: #64748b;
            font-size: 12px;
            margin-top: 7px;
        }

        .status-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 24px;
            margin-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            border-radius: 9px;
            padding: 11px 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .required {
            color: #dc2626;
        }

        @media (max-width: 576px) {
            .page-container {
                margin: 25px auto;
                padding: 0 14px;
            }

            .page-heading h1 {
                font-size: 24px;
            }

            .card-heading,
            .form-content {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
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
                    <a href="menu1.php" class="nav-link text-white">
                        Kelola Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu2.php" class="nav-link text-white">
                        Kelola Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="menu3.php" class="nav-link active">
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

<main class="page-container">

    <div class="page-heading">
        <h1>Edit Data Kelas</h1>
        <p>Perbarui informasi kelas yang sudah tersimpan dalam sistem.</p>
    </div>

    <div class="form-card">

        <div class="card-heading">
            <h2>Informasi Kelas</h2>
            <p>Ubah data di bawah ini sesuai informasi terbaru.</p>
        </div>

        <div class="form-content">

            <?php if ($pesan !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="edit_kelas.php?id=<?= (int) $id; ?>">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label for="nama" class="form-label">
                            Nama Kelas <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nama"
                            name="nama"
                            placeholder="Contoh: X RPL 1"
                            value="<?= htmlspecialchars($kelas['nama'], ENT_QUOTES, 'UTF-8'); ?>"
                            maxlength="100"
                            required
                        >

                        <div class="form-text">
                            Masukkan nama kelas sesuai data sekolah.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="tingkat" class="form-label">
                            Tingkat <span class="required">*</span>
                        </label>

                        <select
                            class="form-select"
                            id="tingkat"
                            name="tingkat"
                            required
                        >
                            <option value="">-- Pilih Tingkat --</option>

                            <option value="X"
                                <?= $kelas['tingkat'] === 'X' ? 'selected' : ''; ?>>
                                X (Sepuluh)
                            </option>

                            <option value="XI"
                                <?= $kelas['tingkat'] === 'XI' ? 'selected' : ''; ?>>
                                XI (Sebelas)
                            </option>

                            <option value="XII"
                                <?= $kelas['tingkat'] === 'XII' ? 'selected' : ''; ?>>
                                XII (Dua Belas)
                            </option>
                        </select>

                        <div class="form-text">
                            Pilih tingkat kelas siswa.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="jurusan" class="form-label">
                            Jurusan <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="jurusan"
                            name="jurusan"
                            placeholder="Contoh: RPL, TSM, TKJ"
                            value="<?= htmlspecialchars($kelas['jurusan'], ENT_QUOTES, 'UTF-8'); ?>"
                            maxlength="100"
                            required
                        >

                        <div class="form-text">
                            Masukkan nama atau kode jurusan.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="status_aktif" class="form-label">
                            Status Kelas <span class="required">*</span>
                        </label>

                        <div class="status-box">
                            <select
                                class="form-select"
                                id="status_aktif"
                                name="status_aktif"
                                required
                            >
                                <option value="1"
                                    <?= (string) $kelas['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (string) $kelas['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>

                            <div class="form-text">
                                Tentukan status kelas dalam sistem.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-actions">
                    <a href="menu3.php"
                       class="btn btn-outline-secondary">
                        Kembali
                    </a>

                    <button type="reset"
                            class="btn btn-light border">
                        Reset
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</main>

</body>
</html>
```

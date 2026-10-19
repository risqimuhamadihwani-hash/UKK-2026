
<?php
include "config/koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID guru tidak valid.");
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_guru WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$guru) {
    die("Data guru tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['update'])) {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status_aktif = $_POST['status_aktif'] ?? '';

    if (
        $nip === '' ||
        $nama === '' ||
        !in_array($status_aktif, ['0', '1'], true) ||
        ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
    ) {
        $pesan = "Periksa kembali data yang kamu masukkan.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_guru
             SET nip = ?, nama = ?, email = NULLIF(?, ''), status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssii",
            $nip,
            $nama,
            $email,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: menu2.php");
            exit;
        } else {
            $pesan = "Gagal mengubah data guru.";
        }

        mysqli_stmt_close($stmt);
    }

    $guru['nip'] = $nip;
    $guru['nama'] = $nama;
    $guru['email'] = $email;
    $guru['status_aktif'] = $status_aktif;
}

function e($nilai) {
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Guru - JusticeAdmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #f8f9fc;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: #212529;
            min-height: 100vh;
            padding: 20px 12px;
        }

        .sidebar h4 {
            color: white;
            margin-bottom: 25px;
            padding-left: 5px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 6px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #0d6efd;
        }

        .content {
            padding: 30px 24px;
        }

        .form-card {
            border: none;
            border-radius: 8px;
        }

        .form-label {
            font-weight: 500;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .content {
                padding: 20px 12px;
            }
        }
    </style>
</head>
<body>

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
                    <a href="menu2.php" class="nav-link active">
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

            <h1 class="mb-2">Edit Guru</h1>
            <p class="text-secondary">
                Halaman untuk mengubah data guru.
            </p>

            <?php if ($pesan !== '') { ?>
                <div class="alert alert-danger">
                    <?= e($pesan); ?>
                </div>
            <?php } ?>

            <div class="card shadow-sm form-card">
                <div class="card-body p-4">

                    <h5 class="mb-4">Formulir Data Guru</h5>

                    <form method="POST">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip"
                                       class="form-control"
                                       placeholder="Masukkan NIP"
                                       value="<?= e($guru['nip']); ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Guru</label>
                                <input type="text" name="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama guru"
                                       value="<?= e($guru['nama']); ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                       class="form-control"
                                       placeholder="nama@email.com"
                                       value="<?= e($guru['email']); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Guru</label>
                                <select name="status_aktif"
                                        class="form-select" required>
                                    <option value="1"
                                        <?= (string)$guru['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= (string)$guru['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>
                                </select>
                            </div>

                        </div>

                        <hr class="my-3">

                        <button type="submit" name="update"
                                class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="menu2.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>

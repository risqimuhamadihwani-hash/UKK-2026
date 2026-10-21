
<?php
$koneksi = new mysqli("localhost", "root", "", "db_ukk_2026");

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nip = trim($_POST["nip"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $status = $_POST["status_aktif"] ?? "1";

    if ($nip === "" || $nama === "") {
        $error = "NIP dan Nama Guru wajib diisi.";
    } elseif (!in_array($status, ["0", "1"], true)) {
        $error = "Status guru tidak valid.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        try {
            $stmt = $koneksi->prepare(
                "INSERT INTO t_guru (nip, nama, email, status_aktif)
                 VALUES (?, ?, ?, ?)"
            );

            $statusInt = (int) $status;
            $stmt->bind_param("sssi", $nip, $nama, $email, $statusInt);
            $stmt->execute();
            $stmt->close();

            $koneksi->close();

            header("Location: menu2.php?pesan=berhasil");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = "NIP atau data unik lainnya sudah terdaftar.";
            } else {
                $error = "Gagal menyimpan data guru: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Tambah Guru</h4>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        Silakan isi data guru di bawah ini.
                    </p>

                    <?php if ($error !== "") { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
                        </div>
                    <?php } ?>

                    <form action="tambah_guru.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">NIP:</label>
                            <input type="text"
                                   name="nip"
                                   class="form-control"
                                   placeholder="Masukkan NIP"
                                   value="<?= htmlspecialchars($_POST["nip"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Guru:</label>
                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="Masukkan Nama Guru"
                                   value="<?= htmlspecialchars($_POST["nama"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Masukkan Email"
                                   value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <select name="status_aktif" class="form-select">
                                <option value="1"
                                    <?= ($_POST["status_aktif"] ?? "1") === "1" ? "selected" : "" ?>>
                                    Aktif
                                </option>
                                <option value="0"
                                    <?= ($_POST["status_aktif"] ?? "1") === "0" ? "selected" : "" ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Guru
                        </button>

                        <button type="reset" class="btn btn-secondary">
                            Reset
                        </button>

                        <a href="menu2.php" class="btn btn-outline-dark">
                            Kembali
                        </a>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>

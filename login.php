<?php
session_start();

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JusticeAdmin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Mengatur latar belakang layar gelap persis seperti desain */
        body {
            background-color: #ffffff; /* Warna gelap ala dark mode */
        }
        /* Styling kotak form agar sudutnya melengkung halus dan elegan */
        .login-card {
            background-color: #ffffff;
            border-radius: 15px;
            width: 100%;
            max-width: 380px;
        }
        /* Tombol kustom warna gelap/hitam */
        .btn-dark-custom {
            background-color: #212529;
            color: #ffffff;
            border-radius: 20px;
        }
        .btn-dark-custom:hover {
            background-color: #343a40;
            color: #ffffff;
        }
        /* Kotak input abu-abu terang */
        .form-control-custom {
            background-color: #e9ecef;
            border: none;
            border-radius: 5px;
        }
        .form-control-custom:focus {
            background-color: #dee2e6;
            box-shadow: none;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 m-0">

    <div class="login-card p-4 shadow-lg text-center">
        
        <!-- Judul Aplikasi -->
        <h4 class="fw-bold mb-4 text-dark">Sistem Pelanggaran Siswa</h4>

        <!-- Form Login (Mengarah ke proses_login.php dengan method POST) -->
        <form action="proses_login.php" method="POST" class="text-start">

            <div class="mb-3">
                <label for="email" class="form-label text-muted small mb-1">Username</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control form-control-custom py-2" 
                    id="email" 
                    placeholder="Masukkan email..." 
                    required
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-muted small mb-1">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control form-control-custom py-2" 
                    id="password" 
                    placeholder="Masukkan password..." 
                    required
                >
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-dark-custom py-2 fw-semibold">LOGIN</button>
            </div>

        </form>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
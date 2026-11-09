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
    <title>Login - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            height: 100vh;
            background-color: #f8f9fa; /* Senada dengan background content dashboard */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 900px;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        /* Sisi kiri disesuaikan dengan warna sidebar dashboard kamu */
        .left-side {
            background-color: #212529; 
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right-side {
            padding: 40px;
        }
        .form-control {
            border-radius: 0 8px 8px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-right: none;
        }
        /* Tombol menggunakan warna biru persis seperti tombol 'Buka Menu' di dashboard */
        .btn-custom {
            background-color: #0d6efd;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 12px;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .btn-custom:hover {
            background-color: #0b5ed7;
            color: white;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center m-0">

    <div class="row login-container m-3 w-100">
        <!-- Sisi Kiri: Menyesuaikan Sidebar Dashboard (Warna Gelap) -->
        <div class="col-md-6 left-side text-center text-md-start">
            <div class="mb-3">
                <i class="bi bi-shield-exclamation text-primary fs-1"></i>
            </div>
            <h4 class="fw-bold mb-2 text-white">Sistem Pelanggaran Siswa</h4>
            <p class="mb-4 text-white-50 small">Silakan login untuk mengelola data pelanggaran dan ketertiban siswa.</p>
            <div class="d-none d-md-block">
                <small class="text-muted">&copy; 2026 Admin Panel</small>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="col-md-6 right-side d-flex flex-column justify-content-center">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark">Login Administrator</h4>
                <p class="text-muted small">Masukkan kredensial akun Anda</p>
            </div>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label small text-muted fw-semibold">Email / Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control" id="email" placeholder="admin@gmail.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small text-muted fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom shadow-sm">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower($_SESSION['role'] ?? '');

if ($role === 'guru') {
    header("Location: guru.php");
    exit;
}

if ($role !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <title>About Me - JusticeAdmin</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #eaf0f6;
        }

        /* SIDEBAR - DISESUAIKAN DENGAN MENU1.PHP */
        .sidebar {
            background-color: #212529;
            padding: 16px !important;
            min-height: 100vh;
        }

        .sidebar h4 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px !important;
        }

        .sidebar .nav {
            margin: 0;
            padding: 0;
        }

        .sidebar .nav-item {
            margin-bottom: 8px !important;
        }

        .sidebar .nav-link {
            display: block;
            width: 100%;
            padding: 8px 16px;
            font-family: var(--bs-body-font-family);
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            text-align: left;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        .sidebar .nav-link:hover:not(.active) {
            color: #fff;
            background-color: transparent;
        }
    
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        .sidebar .nav-link.text-danger {
            color: #dc3545 !important;
        }

        .sidebar .nav-link.text-danger:hover {
            color: #ff6b78 !important;
        }

        .sidebar hr {
            margin: 16px 0;
        }

        /* KONTEN ABOUT ME */
        .main-content {
            padding: 24px;
        }

        .profile-card {
            background: #fff;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #ddd;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
        }

        .profile-header {
            height: 100px;
            background: #0d6efd;
        }

        .profile-body {
            padding: 0 30px 25px;
            text-align: center;
        }

        .profile {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            background: #e9ecef;
            border: 5px solid #fff;
            margin-top: -55px;
            margin-bottom: 12px;
        }

        .profile-body h2 {
            color: #212529;
            font-size: 27px;
            margin-bottom: 8px;
        }

        .role-badge {
            display: inline-block;
            background: #e7f1ff;
            color: #0d6efd;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .description {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 22px;
        }

        .info-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
            background: #f8f9fa;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .info-icon {
            font-size: 22px;
        }

        .info-label {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #212529;
            font-size: 14px;
            font-weight: bold;
        }

        .section-title {
            color: #212529;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin: 25px 0 12px;
        }

        .skill {
            display: inline-block;
            background: #e7f1ff;
            color: #0d6efd;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin: 3px;
        }

        .social-button {
            display: inline-block;
            margin-top: 22px;
            padding: 11px 24px;
            background: #0d6efd;
            color: #fff;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .social-button:hover {
            background: #0b5ed7;
            color: #fff;
        }

        .footer {
            color: #adb5bd;
            font-size: 12px;
            margin-top: 22px;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
                width: 100%;
            }

            .main-content {
                padding: 16px;
            }

            .profile-body {
                padding-left: 20px;
                padding-right: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3 sidebar">

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
                    <a href="about_me.php"
                       class="nav-link active"
                       aria-current="page">
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

        <!-- KONTEN ABOUT ME -->
        <main class="col-md-9 col-lg-10 main-content">

            <div class="profile-card">

                <div class="profile-header"></div>

                <div class="profile-body">

                    <img
                        src="https://placehold.co/200x200/png?text=Foto"
                        alt="Foto profil"
                        class="profile"
                    >

                    <h2>
                        <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <span class="role-badge">
                        <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                        | JusticeAdmin
                    </span>

                    <p class="description">
                        Halo! Saya pengguna JusticeAdmin yang tertarik
                        mempelajari teknologi dan pemrograman.
                        Saya ingin terus belajar, menambah pengalaman,
                        dan mengembangkan kemampuan saya.
                    </p>

                    <div class="info-box">
                        <span class="info-icon">🎓</span>
                        <div>
                            <div class="info-label">Status</div>
                            <div class="info-value">Pelajar</div>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-icon">💻</span>
                        <div>
                            <div class="info-label">Minat</div>
                            <div class="info-value">
                                Programming dan Teknologi
                            </div>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-icon">🎮</span>
                        <div>
                            <div class="info-label">Hobi</div>
                            <div class="info-value">
                                Gaming dan Mendengarkan Musik
                            </div>
                        </div>
                    </div>

                    <h3 class="section-title">
                        Kemampuan yang Dipelajari
                    </h3>

                    <div>
                        <span class="skill">HTML</span>
                        <span class="skill">CSS</span>
                        <span class="skill">Bootstrap</span>
                        <span class="skill">PHP</span>
                    </div>

                    <a
                        href="https://instagram.com/"
                        class="social-button"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        My Instagram ↗
                    </a>

                    <div class="footer">
                        &copy; 2026 | About Me
                    </div>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

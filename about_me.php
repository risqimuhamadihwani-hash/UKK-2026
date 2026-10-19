<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me | My Portfolio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0b1120;
            color: #f8fafc;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(11, 17, 32, 0.95);
            border-bottom: 1px solid #202b40;
            padding: 18px 0;
        }

        .navbar-brand {
            color: #60a5fa !important;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .nav-link {
            color: #cbd5e1;
            margin-left: 18px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #60a5fa;
        }

        .hero {
            min-height: 88vh;
            display: flex;
            align-items: center;
            padding: 70px 0;
        }

        .badge-custom {
            display: inline-block;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(96, 165, 250, 0.35);
            color: #93c5fd;
            border-radius: 30px;
            padding: 9px 16px;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .hero h1 {
            font-size: clamp(38px, 6vw, 65px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 22px;
        }

        .highlight {
            color: #60a5fa;
        }

        .hero p {
            color: #94a3b8;
            font-size: 16px;
            line-height: 1.9;
            max-width: 550px;
        }

        .profile-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px auto;
        }

        .profile-circle {
            width: 290px;
            height: 290px;
            border-radius: 50%;
            border: 3px solid #60a5fa;
            padding: 9px;
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.2);
        }

        .profile-circle img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #1e293b;
        }

        .floating-card {
            position: absolute;
            bottom: 15px;
            right: 0;
            background: #172338;
            border: 1px solid #334155;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            color: #cbd5e1;
        }

        .floating-card i {
            color: #60a5fa;
            margin-right: 6px;
        }

        .btn-primary-custom {
            display: inline-block;
            text-decoration: none;
            background: #3b82f6;
            color: white;
            padding: 13px 24px;
            border-radius: 9px;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 18px;
            margin-right: 10px;
        }

        .btn-primary-custom:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-custom {
            display: inline-block;
            text-decoration: none;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 12px 24px;
            border-radius: 9px;
            margin-top: 18px;
            transition: 0.3s;
        }

        .btn-outline-custom:hover {
            border-color: #60a5fa;
            color: #60a5fa;
        }

        .section {
            padding: 75px 0;
        }

        .section-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .section-subtitle {
            color: #94a3b8;
            margin-bottom: 40px;
        }

        .info-card {
            background: #111c30;
            border: 1px solid #243149;
            border-radius: 16px;
            padding: 28px;
            height: 100%;
            transition: 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            background: rgba(59, 130, 246, 0.13);
            color: #60a5fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            margin-bottom: 20px;
        }

        .info-card h5 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .info-card p {
            color: #94a3b8;
            line-height: 1.8;
            font-size: 14px;
            margin-bottom: 0;
        }

        .skill {
            display: inline-block;
            background: #172338;
            border: 1px solid #2c3b54;
            color: #bfdbfe;
            padding: 10px 16px;
            border-radius: 8px;
            margin: 5px;
            font-size: 14px;
        }

        .contact-box {
            background: linear-gradient(135deg, #142644, #111c30);
            border: 1px solid #2b4265;
            border-radius: 18px;
            padding: 40px 25px;
            text-align: center;
        }

        .contact-box p {
            color: #94a3b8;
        }

        footer {
            border-top: 1px solid #202b40;
            padding: 25px 0;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .hero {
                text-align: center;
                padding-top: 55px;
            }

            .hero p {
                margin-left: auto;
                margin-right: auto;
            }

            .profile-circle {
                width: 230px;
                height: 230px;
            }

            .floating-card {
                right: 0;
                bottom: 0;
            }

            .navbar .nav-link {
                margin-left: 0;
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
                    <a href="about_me.php" class="nav-link active">
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

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">MY PORTFOLIO.</a>

        <button class="navbar-toggler bg-light"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
                aria-controls="navbarMenu"
                aria-expanded="false"
                aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About Me</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#skills">Skills</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7">
                <span class="badge-custom">
                    <i class="bi bi-code-slash"></i>
                    ABOUT ME & MY JOURNEY
                </span>

                <h1>
                    Halo, Saya<br>
                    <span class="highlight">Risqi Muhamad ihwani</span>
                </h1>

                <p>
                    Saya seorang pelajar yang tertarik pada dunia teknologi,
                    pemrograman, dan pengembangan website. Saya senang
                    mempelajari hal baru dan membangun karya digital
                    yang bermanfaat.
                </p>

                <a href="#about" class="btn-primary-custom">
                    Kenali Saya <i class="bi bi-arrow-down-right"></i>
                </a>

                <a href="#contact" class="btn-outline-custom">
                    Hubungi Saya
                </a>
            </div>

            <div class="col-lg-5">
                <div class="profile-wrapper">

                    <div class="profile-circle">
                        <!-- Ganti foto.jpg dengan foto kamu -->
                        <img src="foto.jpg"
                             alt="Foto profil"
                             onerror="this.style.display='none'; this.parentElement.style.background='#172338';">
                    </div>

                    <div class="floating-card">
                        <i class="bi bi-laptop"></i>
                        Future Developer
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="section" id="about">
    <div class="container">

        <div class="text-center">
            <h2 class="section-title">
                Tentang <span class="highlight">Saya</span>
            </h2>
            <p class="section-subtitle">
                Mengenal saya lebih dekat.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="bi bi-person"></i>
                    </div>
                    <h5>Profil Diri</h5>
                    <p>
                        Saya adalah seorang pelajar yang memiliki semangat
                        untuk terus belajar, berkembang, dan mencoba
                        berbagai hal baru di bidang teknologi.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="bi bi-code-square"></i>
                    </div>
                    <h5>Pengembangan Web</h5>
                    <p>
                        Saya tertarik membuat website menggunakan HTML,
                        CSS, PHP, dan MySQL untuk membangun aplikasi
                        yang fungsional dan mudah digunakan.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="icon-box">
                        <i class="bi bi-rocket-takeoff"></i>
                    </div>
                    <h5>Tujuan Saya</h5>
                    <p>
                        Saya ingin meningkatkan kemampuan pemrograman,
                        menghasilkan proyek yang berkualitas, dan menjadi
                        developer yang terus berkembang.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SKILLS -->
<section class="section" id="skills">
    <div class="container text-center">

        <h2 class="section-title">
            My <span class="highlight">Skills</span>
        </h2>

        <p class="section-subtitle">
            Teknologi yang sedang saya pelajari.
        </p>

        <div>
            <span class="skill"><i class="bi bi-filetype-html"></i> HTML</span>
            <span class="skill"><i class="bi bi-filetype-css"></i> CSS</span>
            <span class="skill"><i class="bi bi-filetype-php"></i> PHP</span>
            <span class="skill"><i class="bi bi-database"></i> MySQL</span>
            <span class="skill"><i class="bi bi-bootstrap"></i> Bootstrap</span>
            <span class="skill"><i class="bi bi-git"></i> Git</span>
        </div>

    </div>
</section>

<!-- CONTACT -->
<section class="section" id="contact">
    <div class="container">

        <div class="contact-box">
            <h2 class="section-title">
                Mari Terhubung!
            </h2>

            <p>
                Punya pertanyaan atau ingin mengenal saya lebih jauh?
                Silakan hubungi saya melalui email.
            </p>

            <!-- Ganti dengan email kamu -->
            <a href="mailto:emailkamu@example.com"
               class="btn-primary-custom">
                <i class="bi bi-envelope"></i>
                Hubungi Saya
            </a>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        &copy; <?= date('Y'); ?> Nama Kamu.
        Dibuat dengan semangat belajar dan kreativitas.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

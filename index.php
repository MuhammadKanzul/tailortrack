<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-bg-pattern"></div>
    <div class="container position-relative">
        <div class="row justify-content-center text-center hero-content">
            <div class="col-lg-8 col-md-10 fade-in">
                <span class="hero-badge">Sistem Manajemen Penjahit</span>
                <h1 class="hero-title">Kelola Bisnis Jahit Anda dengan Lebih Efisien</h1>
                <p class="hero-subtitle">Optimalkan proses bisnis, tingkatkan produktivitas, dan berikan layanan terbaik untuk pelanggan Anda.</p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Mulai Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="hero-shape-bottom">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#f8f9fa" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</div>

<!-- Stats Section -->
<div id="stats" class="container stats-container">
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-info">
                    <h4>150+</h4>
                    <p>Pelanggan Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div class="stats-info">
                    <h4>500+</h4>
                    <p>Pesanan Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-info">
                    <h4>50+</h4>
                    <p>Jenis Bahan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stats-info">
                    <h4>4.8</h4>
                    <p>Rating Kepuasan</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title text-center">Fitur Utama</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <h5>Pelanggan</h5>
                    <p>Kelola data pelanggan dan ukuran baju dengan mudah</p>
                    <a href="pelanggan.php" class="btn btn-primary">Kelola Pelanggan</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tasks fa-2x text-success"></i>
                    </div>
                    <h5>Pesanan</h5>
                    <p>Pantau dan kelola pesanan jahitan dengan efisien</p>
                    <a href="pesanan.php" class="btn btn-success">Kelola Pesanan</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-box fa-2x text-warning"></i>
                    </div>
                    <h5>Stok Bahan</h5>
                    <p>Kontrol persediaan bahan dengan akurat</p>
                    <a href="stok_bahan.php" class="btn btn-warning">Kelola Stok</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section id="benefits" class="benefits-section">
    <div class="container">
        <h2 class="section-title text-center">Mengapa Memilih Kami?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="benefit-card">
                    <i class="fas fa-clock fa-3x text-primary"></i>
                    <h5>Efisiensi Waktu</h5>
                    <p>Sistem otomatis membantu Anda mengelola bisnis dengan lebih efisien</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card">
                    <i class="fas fa-chart-line fa-3x text-success"></i>
                    <h5>Tingkatkan Produktivitas</h5>
                    <p>Pantau kinerja dan tingkatkan produktivitas bisnis Anda</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card">
                    <i class="fas fa-smile fa-3x text-warning"></i>
                    <h5>Kepuasan Pelanggan</h5>
                    <p>Layanan yang lebih baik untuk kepuasan pelanggan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 
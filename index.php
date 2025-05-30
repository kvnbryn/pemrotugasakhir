<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Tes IQ Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>TesIQ<span class="logo-online">Online</span></h1>
            </div>
            
               <nav>
                <ul>
                    <li><a href="index.php" class="active">Beranda</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard_user.php">Dashboard</a></li>
                        <li><a href="ranking.php">Peringkat</a></li>  
                        <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="ranking.php">Peringkat</a></li>  
                    <?php endif; ?>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                     <li><a href="admin/login_admin.php" class="admin-link">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2>Uji Kecerdasanmu, Raih Potensimu!</h2>
                <p>Selamat datang di platform tes IQ online yang dirancang untuk membantumu memahami kemampuan kognitifmu. Mulai tes sekarang dan temukan potensimu!</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard_user.php" class="btn btn-primary">Mulai Tes Sekarang</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Daftar & Mulai Tes</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2 class="section-title">Mengapa Memilih Kami?</h2>
                <div class="feature-grid">
                    <div class="feature-item">
                        <img src="https://placehold.co/100x100/007bff/ffffff?text=Soal+Variatif" alt="[Ikon Soal Variatif]" onerror="this.onerror=null;this.src='https://placehold.co/100x100/cccccc/000000?text=Gagal+Muat';">
                        <h3>Soal yang Variatif</h3>
                        <p>Berbagai jenis soal untuk menguji aspek kecerdasan yang berbeda.</p>
                    </div>
                    <div class="feature-item">
                        <img src="https://placehold.co/100x100/28a745/ffffff?text=Hasil+Cepat" alt="[Ikon Hasil Cepat]" onerror="this.onerror=null;this.src='https://placehold.co/100x100/cccccc/000000?text=Gagal+Muat';">
                        <h3>Hasil Instan</h3>
                        <p>Dapatkan skormu segera setelah menyelesaikan tes.</p>
                    </div>
                    <div class="feature-item">
                        <img src="https://placehold.co/100x100/ffc107/000000?text=Akses+Mudah" alt="[Ikon Akses Mudah]" onerror="this.onerror=null;this.src='https://placehold.co/100x100/cccccc/000000?text=Gagal+Muat';">
                        <h3>Akses Fleksibel</h3>
                        <p>Kerjakan tes kapan saja dan di mana saja melalui perangkatmu.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="call-to-action">
            <div class="container">
                <h2>Siap Mengukur Kecerdasanmu?</h2>
                <p>Jangan ragu untuk mencoba dan melihat sejauh mana kemampuanmu. Hasil tes bisa menjadi panduan berharga untuk pengembangan dirimu.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard_user.php" class="btn btn-secondary">Pilih Level Tes</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Login untuk Memulai</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek [Nama Tim Kamu]. Hak Cipta Dilindungi.</p>
            <p>Untuk keperluan tugas Mata Kuliah [Nama Mata Kuliah Kamu]</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

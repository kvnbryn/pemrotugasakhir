<?php
require_once 'config/db.php'; // Untuk session dan navigasi dinamis
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - TesIQOnline</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/aboutus.css">
    <link rel="stylesheet" href="assets/css/transisi.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="text-decoration: none; color: inherit;">TesIQ<span class="logo-online">Online</span></a></h1>
            </div>
             <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard_user.php">Dashboard</a></li>
                        <li><a href="ranking.php">Peringkat</a></li>  
                        <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="ranking.php">Peringkat</a></li>  
                    <?php endif; ?>
                    <li><a href="tentang_kami.php" class="active">Tentang Kami</a></li>
                    <?php if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] != 'admin')): ?>
                        <li><a href="admin/login_admin.php" class="admin-link">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container" style="padding-top: 30px; padding-bottom: 30px;">
            <div class="about-section">
                <h2 class="section-title" style="text-align:left; margin-bottom:20px;">Tentang Proyek TesIQOnline</h2>
                <p>Selamat datang di TesIQOnline, sebuah platform web inovatif yang dirancang untuk mengukur dan menganalisis kemampuan kognitif Anda melalui serangkaian tes IQ yang komprehensif. Proyek ini dikembangkan sebagai bagian dari pemenuhan tugas mata kuliah PEMROGRAMAN WEB dengan tujuan untuk menerapkan konsep-konsep pengembangan web dinamis menggunakan HTML, CSS, JavaScript, PHP, dan MySQL.</p>
                <p>Kami percaya bahwa pemahaman akan kecerdasan diri sendiri adalah langkah awal untuk pengembangan potensi. Oleh karena itu, TesIQOnline hadir untuk menyediakan alat yang mudah diakses, informatif, dan menarik bagi siapa saja yang ingin mengetahui lebih dalam tentang kemampuan berpikir mereka.</p>
                
                <h3>Tujuan Utama Aplikasi:</h3>
                <ul>
                    <li>Menyediakan platform tes IQ online yang interaktif dan mudah digunakan.</li>
                    <li>Memberikan hasil tes yang dapat memberikan gambaran umum mengenai kemampuan kognitif pengguna.</li>
                    <li>Mengimplementasikan sistem multi-user dengan level akses berbeda (pengguna dan admin).</li>
                    <li>Menyediakan fitur manajemen konten bagi admin untuk mengelola soal-soal tes.</li>
                    <li>Menawarkan berbagai level kesulitan tes untuk mengakomodasi berbagai tingkat kemampuan.</li>
                </ul>

                <h3>Fitur Aplikasi Secara Umum:</h3>
                <ul>
                    <li>Registrasi dan Login Pengguna.</li>
                    <li>Berbagai Pilihan Level Tes (Easy, Medium, Hard, dan Premium untuk pengguna tertentu).</li>
                    <li>Penilaian Otomatis dan Tampilan Skor Setelah Tes.</li>
                    <li>Riwayat Tes Pengguna.</li>
                    <li>Panel Admin untuk Manajemen Soal (Tambah, Edit, Hapus Soal).</li>
                    <li>Halaman Informasi Statis (seperti halaman ini).</li>
                    <li>(Simulasi) Fitur Akun Premium untuk akses konten eksklusif.</li>
                </ul>
                 <h3>Manfaat Aplikasi:</h3>
                <p>Bagi pengguna, aplikasi ini dapat memberikan wawasan awal tentang aspek-aspek tertentu dari kemampuan kognitif mereka. Bagi pengembang (tim kami), proyek ini menjadi sarana pembelajaran praktis dalam membangun aplikasi web full-stack dari awal tanpa menggunakan framework atau library eksternal, mengasah kemampuan problem-solving, dan kerjasama tim.</p>
            </div>

            <h2 class="section-title" style="margin-top: 40px; margin-bottom: 30px;">Tim Pengembang</h2>
            <p style="text-align:center; margin-bottom:30px;">Proyek ini dikerjakan oleh tim yang berdedikasi dan bersemangat untuk belajar dan berinovasi.</p>
            
            <div class="feature-grid">
                <div class="team-member-card">
                    <img src="https://placehold.co/120x120/007bff/ffffff?text=Foto+Anggota+1" alt="[Foto Anggota Tim 1]" onerror="this.onerror=null;this.src='https://placehold.co/120x120/cccccc/000000?text=Gagal+Muat';">
                    <h3>KEVIN BRYAN KALENGKONGAN</h3>
                    <p class="role">[220211060166] - [Berperan aktif sebagai Backend Developer]</p>
                    <p class="description">[Mengembangkan logika server, database, dan manajemen data.]</p>
                </div>
                
                <div class="team-member-card">
                    <img src="https://placehold.co/120x120/28a745/ffffff?text=Foto+Anggota+2" alt="[Foto Anggota Tim 2]" onerror="this.onerror=null;this.src='https://placehold.co/120x120/cccccc/000000?text=Gagal+Muat';">
                    <h3>CLAYTON BEJR JOSHUA WUISAN</h3>
                    <p class="role">[220211060211] - [Berperan aktif sebagai Frontend Developer]</p>
                    <p class="description">[Bertanggung jawab atas desain antarmuka dan interaksi pengguna.]</p>
                </div>
                
                <div class="team-member-card">
                     <img src="https://placehold.co/120x120/ffc107/000000?text=Foto+Anggota+3" alt="[Foto Anggota Tim 3]" onerror="this.onerror=null;this.src='https://placehold.co/120x120/cccccc/000000?text=Gagal+Muat';">
                    <h3>M.GIBRAN ALKATIRI</h3>
                    <p class="role">[220211060093] - [Berperan dalam Tim sebagai Full-stack/Dokumentasi]</p>
                    <p class="description">[Membantu pengembangan frontend dan backend, serta menyusun dokumentasi proyek.]</p>
                </div>

                 <?php /*
                 <div class="team-member-card">
                    <img src="https://placehold.co/120x120/6f42c1/ffffff?text=Foto+Anggota+4" alt="[Foto Anggota Tim 4]" onerror="this.onerror=null;this.src='https://placehold.co/120x120/cccccc/000000?text=Gagal+Muat';">
                    <h3>[Nama Anggota 4]</h3>
                    <p class="role">[NIM Anggota 4] - [Peran dalam Tim]</p>
                    <p class="description">[Deskripsi singkat atau kontribusi anggota 4.]</p>
                </div>
                */ ?>
            </div>
             <div class="about-section" style="margin-top: 40px;">
                <h2 class="section-title" style="text-align:left; margin-bottom:20px;">Users (Aktor) dan Use Case Diagram</h2>
                <p>Aplikasi ini memiliki dua jenis pengguna utama (aktor):</p>
                <ol>
                    <li><strong>Pengguna (User/Member):</strong>
                        <ul>
                            <li>Dapat melakukan registrasi akun baru.</li>
                            <li>Dapat melakukan login ke akun yang sudah ada.</li>
                            <li>Dapat melihat daftar level tes IQ yang tersedia.</li>
                            <li>Dapat memilih dan mengerjakan tes IQ.</li>
                            <li>Dapat melihat hasil (skor) tes setelah menyelesaikan tes.</li>
                            <li>Dapat melihat riwayat tes yang pernah dikerjakan.</li>
                            <li>Dapat melakukan logout.</li>
                            <li>(Opsional) Dapat melakukan simulasi upgrade ke akun premium.</li>
                        </ul>
                    </li>
                    <li><strong>Admin:</strong>
                        <ul>
                            <li>Dapat melakukan login ke panel admin.</li>
                            <li>Dapat mengelola (Tambah, Edit, Hapus) data soal tes IQ untuk berbagai level.</li>
                            <li>Dapat melihat daftar pengguna terdaftar (opsional).</li>
                            <li>Dapat mengelola kategori/level tes (opsional).</li>
                            <li>Dapat melakukan logout.</li>
                        </ul>
                    </li>
                </ol>
                <p style="margin-top:20px;"><strong>Diagram Use Case:</strong></p>
                <p>Untuk visualisasi interaksi antara aktor dan sistem, diagram Use Case akan sangat membantu. Anda dapat membuat diagram ini menggunakan tools seperti draw.io, Lucidchart, atau bahkan menggambarnya secara manual dan menyertakan fotonya dalam dokumentasi akhir.</p>
                <p><em>Contoh elemen dalam Use Case Diagram:</em></p>
                <ul style="list-style-type: square; margin-left:20px;">
                    <li>Aktor: User, Admin</li>
                    <li>Use Cases untuk User: Registrasi, Login, Pilih Tes, Kerjakan Tes, Lihat Hasil, Lihat Riwayat, Logout, dll.</li>
                    <li>Use Cases untuk Admin: Login Admin, Kelola Soal, Kelola User (opsional), Logout Admin, dll.</li>
                </ul>
                 <img src="https://placehold.co/600x400/e9ecef/333333?text=Contoh+Placeholder+Use+Case+Diagram" alt="[Contoh Placeholder Use Case Diagram]" style="max-width:100%; height:auto; margin-top:15px; border-radius:5px; display:block; margin-left:auto; margin-right:auto;" onerror="this.onerror=null;this.src='https://placehold.co/600x400/cccccc/000000?text=Gagal+Muat+Diagram';">
                <p style="text-align:center; font-size:0.9em; color:#666;">(Gantilah gambar di atas dengan Use Case Diagram aktual proyek Anda)</p>
            </div>

        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/transisi.js"></script>
</body>
</html>

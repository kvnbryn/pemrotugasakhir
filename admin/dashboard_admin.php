<?php
// dashboard_admin.php
// Pastikan file ini ada di dalam folder testIQ/admin/
$page_title = "Admin Dashboard - TesIQOnline"; // Judul spesifik untuk halaman ini
require_once 'admin_header.php'; // Include header admin

// Di sini Anda bisa menambahkan logika untuk menampilkan statistik,
// misalnya jumlah user, jumlah soal, jumlah tes yang sudah dikerjakan, dll.

// Contoh query untuk statistik (opsional, bisa dikembangkan)
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];
$total_questions = $conn->query("SELECT COUNT(*) as count FROM questions")->fetch_assoc()['count'];
$total_tests_taken = $conn->query("SELECT COUNT(*) as count FROM user_tests")->fetch_assoc()['count'];

?>

<h2 class="page-title"><?php echo $page_title; ?></h2>

<p style="text-align: center; font-size: 1.2em; margin-bottom: 30px;">
    Selamat datang, <strong><?php echo htmlspecialchars($admin_username); ?></strong>! Gunakan navigasi di atas untuk mengelola konten.
</p>

<div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
    <div class="card" style="background-color: #f0f8ff; border-left: 5px solid #007bff;">
        <h3>Total Pengguna Terdaftar</h3>
        <p style="font-size: 2em; font-weight: bold; color: #007bff;"><?php echo $total_users; ?></p>
        <small>(Tidak termasuk admin)</small>
    </div>
    <div class="card" style="background-color: #e6fff2; border-left: 5px solid #28a745;">
        <h3>Total Soal Tersedia</h3>
        <p style="font-size: 2em; font-weight: bold; color: #28a745;"><?php echo $total_questions; ?></p>
        <a href="manajemen_soal.php" class="btn btn-primary" style="font-size:0.9em; padding: 5px 10px; background-color:#28a745; margin-top:10px;">Kelola Soal</a>
    </div>
    <div class="card" style="background-color: #fff3cd; border-left: 5px solid #ffc107;">
        <h3>Total Tes Dikerjakan</h3>
        <p style="font-size: 2em; font-weight: bold; color: #ffc107;"><?php echo $total_tests_taken; ?></p>
        <small>(Semua level oleh semua pengguna)</small>
    </div>
</div>

<div style="margin-top: 40px; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
    <h4>Aktivitas Cepat:</h4>
    <ul>
        <li><a href="manajemen_soal.php" class="action-button" style="background-color:#007bff;">Lihat & Kelola Semua Soal</a></li>
        <li><a href="tambah_soal.php" class="action-button">Tambah Soal Baru</a></li>
        </ul>
</div>

<?php
require_once 'admin_footer.php'; // Include footer admin
?>
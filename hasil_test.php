<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['last_test_id'])) {
    $_SESSION['message'] = ['type' => 'info', 'text' => 'Tidak ada hasil tes untuk ditampilkan. Silakan selesaikan tes terlebih dahulu.'];
    header("Location: dashboard_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$last_test_id = $_SESSION['last_test_id'];

// Ambil detail hasil tes terakhir dari database
// Kita juga ambil total_questions_attempted dan correct_answers dari user_tests
$stmt_result = $conn->prepare(
    "SELECT ut.score, ut.test_date, ut.time_taken_seconds, 
            ut.total_questions_attempted, ut.correct_answers, 
            l.id as level_id_taken, l.level_name, l.description AS level_description
     FROM user_tests ut 
     JOIN levels l ON ut.level_id = l.id 
     WHERE ut.id = ? AND ut.user_id = ?"
);

if(!$stmt_result){
    error_log("Prepare failed for result: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memuat hasil tes.'];
    header("Location: dashboard_user.php");
    exit();
}

$stmt_result->bind_param("ii", $last_test_id, $user_id);
$stmt_result->execute();
$result_data = $stmt_result->get_result();

if ($result_data->num_rows == 0) {
    unset($_SESSION['last_test_id']);
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Hasil tes tidak ditemukan.'];
    header("Location: dashboard_user.php");
    exit();
}

$test_result = $result_data->fetch_assoc();
$stmt_result->close();

// Hapus ID tes dari sesi setelah ditampilkan
unset($_SESSION['last_test_id']);

$username = $_SESSION['username'];
$score = $test_result['score'];
$level_name = $test_result['level_name'];
$level_id_taken = $test_result['level_id_taken'];
$test_date = date('d F Y, H:i', strtotime($test_result['test_date']));
$time_taken_seconds = $test_result['time_taken_seconds'];
$time_taken_formatted = floor($time_taken_seconds / 60) . " menit " . ($time_taken_seconds % 60) . " detik";

$jumlah_soal_dijawab = $test_result['total_questions_attempted'];
$jumlah_jawaban_benar = $test_result['correct_answers'];

// Interpretasi skor (contoh sederhana, bisa dikembangkan)
// Asumsi skor maksimal per level berbeda-beda tergantung jumlah soal dan poin per soal.
// Kita bisa pakai persentase jawaban benar.
$persentase_benar = 0;
if ($jumlah_soal_dijawab > 0) {
    $persentase_benar = ($jumlah_jawaban_benar / $jumlah_soal_dijawab) * 100;
}

$interpretation = "Setiap tes adalah langkah untuk lebih mengenal diri.";
$iq_category = "Belum Terkategori";

if ($persentase_benar >= 90) {
    $interpretation = "Luar biasa! Pemahaman Anda pada materi level ini sangat tinggi. Ini menunjukkan potensi kecerdasan yang sangat baik.";
    $iq_category = "Sangat Tinggi / Superior";
} elseif ($persentase_benar >= 75) {
    $interpretation = "Bagus sekali! Anda menunjukkan pemahaman yang kuat. Ini adalah indikasi kecerdasan di atas rata-rata.";
    $iq_category = "Tinggi / Di Atas Rata-rata";
} elseif ($persentase_benar >= 50) {
    $interpretation = "Cukup baik! Anda memiliki dasar pemahaman yang memadai. Terus berlatih untuk meningkatkan lagi!";
    $iq_category = "Rata-rata";
} elseif ($persentase_benar >= 30) {
    $interpretation = "Masih ada ruang untuk peningkatan. Jangan menyerah, pelajari kembali dan coba lagi!";
    $iq_category = "Di Bawah Rata-rata";
} else {
    $interpretation = "Perlu banyak belajar lagi. Fokus dan konsentrasi adalah kunci. Ayo coba lagi nanti!";
    $iq_category = "Perlu Perhatian";
}

// Perkiraan Skor IQ (SANGAT SIMULATIF DAN BUKAN STANDAR!)
// Ini hanya untuk memberikan "rasa" skor IQ, BUKAN PENGUKURAN IQ AKTUAL.
// Skala IQ umumnya memiliki rata-rata 100.
$simulated_iq_score = 70 + round($persentase_benar * 0.7); // Misal, 0% benar = 70, 100% benar = 140
if ($simulated_iq_score > 150) $simulated_iq_score = 150; // Batas atas simulasi
if ($jumlah_soal_dijawab == 0) $simulated_iq_score = "-";


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tes IQ: <?php echo htmlspecialchars($level_name); ?> - TesIQOnline</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hasil_test.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="text-decoration: none; color: inherit;">TesIQ<span class="logo-online">Online</span></a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard_user.php">Dashboard</a></li>
                    <li><a href="ranking.php">Peringkat</a></li>
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($username); ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="result-container">
            <h2>Hasil Tes IQ Anda</h2>
            <p class="level-name">Level Tes: <?php echo htmlspecialchars($level_name); ?></p>

            <p style="font-size:0.9em; color:#666;">(Skor berikut adalah simulasi dan bukan merupakan penilaian IQ klinis standar)</p>
            <div class="simulated-iq-display">
                <?php echo $simulated_iq_score; ?>
                <span>Simulasi IQ</span>
            </div>
            
            <div class="result-details" style="margin-top: 20px;">
                <p>Halo, <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
                <p>Anda telah menyelesaikan tes pada tanggal <strong><?php echo $test_date; ?></strong>.</p>
                <p>Waktu pengerjaan: <strong><?php echo $time_taken_formatted; ?></strong>.</p>
                <p>Total Poin Diperoleh: <strong class="score-display" style="font-size:1.5em;"><?php echo $score; ?></strong></p>
                <p>Jumlah soal dijawab: <strong><?php echo $jumlah_soal_dijawab; ?></strong>.</p>
                <p>Jawaban Benar: <strong><?php echo $jumlah_jawaban_benar; ?> dari <?php echo $jumlah_soal_dijawab; ?> soal (<?php echo round($persentase_benar, 2); ?>%)</strong>.</p>
            </div>

            <div class="interpretation-box">
                <h3>Interpretasi Hasil:</h3>
                <p>Kategori Performa Anda (berdasarkan persentase jawaban benar pada level ini): <strong><?php echo htmlspecialchars($iq_category); ?></strong></p>
                <p class="interpretation-text"><?php echo htmlspecialchars($interpretation); ?></p>
            </div>
            

            <div class="action-buttons" style="margin-top: 30px;">
                <a href="dashboard_user.php" class="btn btn-primary">Kembali ke Dashboard</a>
                <a href="test.php?level_id=<?php echo $level_id_taken; ?>" class="btn btn-secondary">Ulangi Level Ini</a>
                <a href="ranking.php" class="btn btn-info" style="background-color:#17a2b8;">Lihat Papan Peringkat</a>
            </div>
        </div>
    </main>

    <footer style="margin-top: 50px;">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
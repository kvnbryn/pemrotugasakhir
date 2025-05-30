<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Anda harus login untuk memulai tes.";
    header("Location: login.php");
    exit();
}

if (!isset($_GET['level_id']) || !is_numeric($_GET['level_id'])) {
    // Jika tidak ada level_id atau bukan angka, kembalikan ke dashboard
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Level tes tidak valid.'];
    header("Location: dashboard_user.php");
    exit();
}

$level_id = intval($_GET['level_id']);
$user_id = $_SESSION['user_id'];

// Ambil detail level
$stmt_level = $conn->prepare("SELECT level_name, description FROM levels WHERE id = ?");
if(!$stmt_level) {
    // Ini adalah error server, bukan input user
    error_log("Prepare failed for level: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan pada server saat memuat level.'];
    header("Location: dashboard_user.php");
    exit();
}
$stmt_level->bind_param("i", $level_id);
$stmt_level->execute();
$result_level = $stmt_level->get_result();
if ($result_level->num_rows == 0) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Level tes tidak ditemukan.'];
    header("Location: dashboard_user.php");
    exit();
}
$level_data = $result_level->fetch_assoc();
$level_name = $level_data['level_name'];
$stmt_level->close();

// Jika level adalah 'Premium' dan user bukan premium, redirect
if ($level_name === 'Premium' && (!isset($_SESSION['is_premium']) || !$_SESSION['is_premium'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Anda harus menjadi pengguna Premium untuk mengakses level ini.'];
    header("Location: dashboard_user.php");
    exit();
}

// Ambil soal berdasarkan level_id, acak urutannya
$jumlah_soal_per_tes = 10; // Pastikan ada cukup soal di database untuk jumlah ini
$questions = [];
// Untuk Debugging jika soal tidak muncul:
// error_log("Mencoba mengambil soal untuk level_id: " . $level_id);

$stmt_questions = $conn->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE level_id = ? ORDER BY RAND() LIMIT ?");
if(!$stmt_questions){
    error_log("Prepare failed for questions: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan pada server saat memuat soal.'];
    header("Location: dashboard_user.php");
    exit();
}
$stmt_questions->bind_param("ii", $level_id, $jumlah_soal_per_tes);
$stmt_questions->execute();
$result_questions = $stmt_questions->get_result();

// Untuk Debugging:
// error_log("Jumlah soal ditemukan untuk level $level_id: " . $result_questions->num_rows);

if ($result_questions->num_rows > 0) {
    while ($row = $result_questions->fetch_assoc()) {
        $questions[] = $row;
    }
}
$stmt_questions->close();

if (empty($questions)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Belum ada soal yang tersedia untuk level ini atau jumlah soal kurang dari yang dibutuhkan. Silakan hubungi admin.'];
    header("Location: dashboard_user.php");
    exit();
}
// Jika jumlah soal yang didapat kurang dari $jumlah_soal_per_tes, bisa jadi masalah.
// Untuk tes yang valid, sebaiknya jumlah soal yang didapat == $jumlah_soal_per_tes.
// Namun, skrip akan tetap berjalan selama $questions tidak empty.

// Perkiraan waktu pengerjaan: misal 1.5 menit per soal (90 detik)
$waktu_per_soal_detik = 90;
$total_waktu_tes_detik = count($questions) * $waktu_per_soal_detik;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes IQ: <?php echo htmlspecialchars($level_name); ?> - TesIQOnline</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .question-block {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .question-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .question-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .options label {
            display: block;
            background-color: #f8f9fa;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
            border: 1px solid #eee;
        }
        .options label:hover {
            background-color: #e9ecef;
        }
        .options input[type="radio"] {
            margin-right: 10px;
            vertical-align: middle;
        }
        .timer {
            position: sticky;
            top: 70px; /* Sesuaikan dengan tinggi header jika header sticky */
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 0 0 8px 8px;
            z-index: 900; /* Di bawah header (jika header 1000) */
            margin-bottom: 20px;
        }
         header { /* Pastikan header punya z-index lebih tinggi jika sticky */
            position: sticky;
            top: 0;
            z-index: 1000;
         }
        .submit-test-btn {
            display: block;
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            margin-top: 30px;
        }
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
            border-radius: .25rem;
            text-align: center;
        }
    </style>
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
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="timer" id="timer">
        Sisa Waktu: <span id="time">--:--</span>
    </div>

    <main>
        <div class="test-container">
            <h2 style="text-align:center; margin-bottom:10px; color:#0056b3;">Tes IQ - Level: <?php echo htmlspecialchars($level_name); ?></h2>
            <p style="text-align:center; margin-bottom:25px; font-size:0.9em; color:#555;">Jumlah Soal: <?php echo count($questions); ?>. Harap jawab semua pertanyaan dengan teliti.</p>
            
            <div class="warning-message">
                <strong>Perhatian!</strong> Jangan merefresh halaman ini atau menutup browser selama tes berlangsung. Jawaban Anda mungkin tidak tersimpan.
            </div>

            <form action="submit_test.php" method="POST" id="testForm">
                <input type="hidden" name="level_id" value="<?php echo $level_id; ?>">
                <input type="hidden" name="start_time" value="<?php echo time(); // Waktu mulai tes ?>">
                
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-block">
                        <p class="question-text"><?php echo ($index + 1) . ". " . htmlspecialchars($q['question_text']); ?></p>
                        <div class="options">
                            <input type="hidden" name="questions_ids[<?php echo $q['id']; ?>]" value="<?php echo $q['id']; ?>"> <label>
                                <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A" required> 
                                A. <?php echo htmlspecialchars($q['option_a']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> 
                                B. <?php echo htmlspecialchars($q['option_b']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> 
                                C. <?php echo htmlspecialchars($q['option_c']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> 
                                D. <?php echo htmlspecialchars($q['option_d']); ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-primary submit-test-btn">Selesai & Kirim Jawaban</button>
            </form>
        </div>
    </main>

    <footer style="margin-top: 50px;">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek [Nama Tim Kamu]</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timerDisplay = document.getElementById('time');
            const testForm = document.getElementById('testForm');
            let timeLeft = <?php echo $total_waktu_tes_detik; ?>;

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds; // Tambah 0 di depan jika < 10
                timerDisplay.textContent = `${minutes}:${seconds}`;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = "Waktu Habis!";
                    if(testForm) {
                        alert("Waktu pengerjaan tes telah habis. Jawaban Anda akan otomatis dikirim.");
                        // Kumpulkan jawaban yang sudah dipilih sebelum submit otomatis
                        // Walaupun browser modern akan mengirimkan apa yang sudah dipilih,
                        // memastikan semua radio button `required` telah diisi bisa jadi problem
                        // Untuk simplisitas, kita submit saja. Jika ada `required` yg belum, browser akan handle.
                        testForm.submit();
                    }
                }
            }

            updateTimer(); 
            const timerInterval = setInterval(updateTimer, 1000);

            // Peringatan sebelum meninggalkan halaman (opsional, bisa jadi mengganggu)
            // window.addEventListener('beforeunload', function (e) {
            //     e.preventDefault();
            //     e.returnValue = 'Apakah Anda yakin ingin meninggalkan halaman ini? Progres tes Anda mungkin tidak tersimpan.';
            // });

            // if(testForm) {
            //     testForm.addEventListener('submit', function() {
            //         window.removeEventListener('beforeunload', function(e){
            //             e.preventDefault();
            //             e.returnValue = '';
            //         });
            //     });
            // }
        });
    </script>
</body>
</html>
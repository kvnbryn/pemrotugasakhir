<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Sesi Anda telah berakhir atau Anda tidak login. Silakan login kembali.";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input dasar
    if (!isset($_POST['level_id'], $_POST['start_time'], $_POST['questions_ids'], $_POST['answers'])) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan. Data tes tidak lengkap saat pengiriman.'];
        header("Location: dashboard_user.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $level_id = intval($_POST['level_id']);
    $start_time = intval($_POST['start_time']);
    // questions_ids adalah array [question_id_displayed => question_id_displayed] dari form
    $displayed_question_ids = array_values($_POST['questions_ids']); // Dapatkan ID soal yang ditampilkan
    $user_answers = $_POST['answers']; // Array jawaban user [question_id => answer_value]

    $end_time = time();
    $time_taken_seconds = $end_time - $start_time;

    $score = 0;
    $total_questions_attempted = 0; // Jumlah soal yang coba dijawab (ada di array answers)
    $correct_answers_count = 0;
    $default_points_per_correct_answer = 10; 

    $correct_db_answers_data = []; // Untuk menyimpan info correct_option dan points dari DB

    // Ambil kunci jawaban dan poin dari database HANYA untuk soal-soal yang DIJAWAB oleh user
    // Ini lebih efisien daripada mengambil semua soal yang ditampilkan jika user tidak menjawab semua.
    $answered_question_ids = array_keys($user_answers);

    if (!empty($answered_question_ids)) {
        // Buat placeholder untuk query IN (...)
        $placeholders = implode(',', array_fill(0, count($answered_question_ids), '?'));
        $types = str_repeat('i', count($answered_question_ids)); // tipe integer untuk IDs
        
        $sql_correct_answers = "SELECT id, correct_option, points FROM questions WHERE id IN ($placeholders)";
        $stmt_correct = $conn->prepare($sql_correct_answers);

        if ($stmt_correct) {
            // Spread operator (...) untuk bind_param jika PHP >= 5.6
            $stmt_correct->bind_param($types, ...$answered_question_ids);
            $stmt_correct->execute();
            $result_correct = $stmt_correct->get_result();

            while ($row = $result_correct->fetch_assoc()) {
                $correct_db_answers_data[$row['id']] = [
                    'correct_option' => strtoupper(trim($row['correct_option'])),
                    'points' => $row['points'] ?? $default_points_per_correct_answer
                ];
            }
            $stmt_correct->close();
        } else {
            error_log("Gagal prepare statement untuk mengambil kunci jawaban: " . $conn->error);
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memproses jawaban Anda. Silakan coba lagi.'];
            header("Location: dashboard_user.php");
            exit();
        }
    }
    
    $detailed_answers_to_store = [];

    // Hitung skor berdasarkan jawaban user
    foreach ($user_answers as $question_id_answered => $user_answer_value) {
        $total_questions_attempted++;
        $is_correct_flag = false; // Default jawaban salah
        $question_id_answered = intval($question_id_answered); // pastikan integer
        $user_answer_value = strtoupper(trim($user_answer_value));

        if (isset($correct_db_answers_data[$question_id_answered])) {
            $correct_data_for_this_q = $correct_db_answers_data[$question_id_answered];
            if ($user_answer_value == $correct_data_for_this_q['correct_option']) {
                $score += $correct_data_for_this_q['points'];
                $correct_answers_count++;
                $is_correct_flag = true;
            }
        }
        $detailed_answers_to_store[] = [
            'question_id' => $question_id_answered,
            'user_selected_option' => $user_answer_value,
            'is_correct' => $is_correct_flag ? 1 : 0 // Simpan sebagai integer 0 atau 1
        ];
    }

    // Simpan hasil tes ke tabel user_tests
    $stmt_insert_test = $conn->prepare("INSERT INTO user_tests (user_id, level_id, score, time_taken_seconds, total_questions_attempted, correct_answers) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt_insert_test) {
        $stmt_insert_test->bind_param("iiiiii", $user_id, $level_id, $score, $time_taken_seconds, $total_questions_attempted, $correct_answers_count);
        
        if ($stmt_insert_test->execute()) {
            $user_test_id = $stmt_insert_test->insert_id; 

            // Simpan jawaban detail ke tabel user_answers
            if (!empty($detailed_answers_to_store)) {
                $sql_insert_detail = "INSERT INTO user_answers (user_test_id, question_id, user_selected_option, is_correct) VALUES (?, ?, ?, ?)";
                $stmt_insert_detail = $conn->prepare($sql_insert_detail);
                if ($stmt_insert_detail) {
                    foreach ($detailed_answers_to_store as $detail) {
                        $stmt_insert_detail->bind_param("iisi", 
                            $user_test_id, 
                            $detail['question_id'], 
                            $detail['user_selected_option'], 
                            $detail['is_correct']
                        );
                        if(!$stmt_insert_detail->execute()){
                             error_log("Gagal execute statement untuk user_answers: " . $stmt_insert_detail->error);
                        }
                    }
                    $stmt_insert_detail->close();
                } else {
                     error_log("Gagal prepare statement untuk user_answers: " . $conn->error);
                }
            }
            
            $_SESSION['last_test_id'] = $user_test_id; 
            header("Location: hasil_test.php");
            exit();

        } else {
            error_log("Gagal menyimpan hasil tes: " . $stmt_insert_test->error);
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menyimpan hasil tes Anda. Error: ' . $stmt_insert_test->error];
            header("Location: dashboard_user.php");
            exit();
        }
        $stmt_insert_test->close();
    } else {
        error_log("Gagal mempersiapkan penyimpanan hasil tes: " . $conn->error);
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mempersiapkan penyimpanan hasil tes. Error: ' . $conn->error];
        header("Location: dashboard_user.php");
        exit();
    }

    $conn->close();

} else {
    header("Location: dashboard_user.php");
    exit();
}
?>
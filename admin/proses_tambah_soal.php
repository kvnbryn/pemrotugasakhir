<?php
// proses_tambah_soal.php
// Pastikan file ini ada di dalam folder testIQ/admin/
require_once '../config/db.php'; // session_start() sudah ada di db.php
require_once 'admin_header.php'; // Untuk otentikasi admin dan variabel $conn

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $level_id = trim($_POST['level_id'] ?? '');
    $question_text = trim($_POST['question_text'] ?? '');
    $option_a = trim($_POST['option_a'] ?? '');
    $option_b = trim($_POST['option_b'] ?? '');
    $option_c = trim($_POST['option_c'] ?? '');
    $option_d = trim($_POST['option_d'] ?? '');
    $correct_option = trim($_POST['correct_option'] ?? '');
    $points = trim($_POST['points'] ?? '');

    // Validasi dasar
    if (empty($level_id) || !is_numeric($level_id)) {
        $errors[] = "Level Tes harus dipilih.";
    }
    if (empty($question_text)) {
        $errors[] = "Teks Pertanyaan tidak boleh kosong.";
    }
    if (empty($option_a)) {
        $errors[] = "Pilihan A tidak boleh kosong.";
    }
    if (empty($option_b)) {
        $errors[] = "Pilihan B tidak boleh kosong.";
    }
    if (empty($option_c)) {
        $errors[] = "Pilihan C tidak boleh kosong.";
    }
    if (empty($option_d)) {
        $errors[] = "Pilihan D tidak boleh kosong.";
    }
    if (empty($correct_option) || !in_array($correct_option, ['A', 'B', 'C', 'D'])) {
        $errors[] = "Kunci Jawaban Benar tidak valid.";
    }
    if (empty($points) || !is_numeric($points) || $points < 1) {
        $errors[] = "Poin harus berupa angka positif.";
    }

    // Hitung nomor urut soal untuk level_id yang dipilih
    if (empty($errors)) {
        // Cek duplikasi soal
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE question_text = ? AND level_id = ?");
        if ($checkStmt) {
            $checkStmt->bind_param("si", $question_text, $level_id);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                $errors[] = "Soal dengan teks yang sama sudah ada di level ini.";
            }
        }

        //Jika tidak ada error, hitung question_number
        if (empty($errors)) {
            $numberStmt = $conn->prepare("SELECT MAX(question_number) FROM questions WHERE level_id = ?");
            $numberStmt->bind_param("i", $level_id);
            $numberStmt->execute();
            $numberStmt->bind_result($last_number);
            $numberStmt->fetch();
            $numberStmt->close();

            $question_number = $last_number ? $last_number + 1 : 1; // Jika null (soal pertama), mulai dari 1
        }
    }



    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO questions (level_id, question_number, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iissssssi", $level_id, $question_number, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $points);
            if ($stmt->execute()) {
                $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Soal berhasil ditambahkan!'];
                header("Location: manajemen_soal.php");
                exit();
            } else {
                // Log error
                error_log("Gagal execute statement penambahan soal: " . $stmt->error);
                $errors[] = "Gagal menyimpan soal ke database. Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Log error
            error_log("Gagal prepare statement penambahan soal: " . $conn->error);
            $errors[] = "Terjadi kesalahan pada server saat persiapan data.";
        }
    }

    // Jika ada error, simpan data form dan error ke session untuk ditampilkan kembali
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: tambah_soal.php");
        exit();
    }

    $conn->close();

} else {
    // Jika diakses langsung tanpa metode POST
    $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Akses tidak sah.'];
    header("Location: tambah_soal.php");
    exit();
}
?>
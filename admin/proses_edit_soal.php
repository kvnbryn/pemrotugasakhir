<?php
// proses_edit_soal.php
// Pastikan file ini ada di dalam folder testIQ/admin/
require_once '../config/db.php'; // session_start() sudah ada di db.php
require_once 'admin_header.php'; // Untuk otentikasi admin dan variabel $conn

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_id = trim($_POST['question_id'] ?? '');
    $level_id = trim($_POST['level_id'] ?? '');
    $question_text = trim($_POST['question_text'] ?? '');
    $option_a = trim($_POST['option_a'] ?? '');
    $option_b = trim($_POST['option_b'] ?? '');
    $option_c = trim($_POST['option_c'] ?? '');
    $option_d = trim($_POST['option_d'] ?? '');
    $correct_option = trim($_POST['correct_option'] ?? '');
    $points = trim($_POST['points'] ?? '');

    // Validasi dasar
    if (empty($question_id) || !is_numeric($question_id)) {
        $errors[] = "ID Soal tidak valid.";
    }
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

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE questions SET level_id = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, points = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("issssssii", $level_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $points, $question_id);
            if ($stmt->execute()) {
                $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Soal berhasil diperbarui!'];
                header("Location: manajemen_soal.php");
                exit();
            } else {
                // Log error
                error_log("Gagal execute statement pembaruan soal: " . $stmt->error);
                $errors[] = "Gagal memperbarui soal di database. Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Log error
            error_log("Gagal prepare statement pembaruan soal: " . $conn->error);
            $errors[] = "Terjadi kesalahan pada server saat persiapan data pembaruan.";
        }
    }

    // Jika ada error, simpan data form dan error ke session untuk ditampilkan kembali di form edit
    if (!empty($errors)) {
        $_SESSION['form_errors_edit'] = $errors;
        $_SESSION['form_data_edit'] = $_POST; // $_POST sudah berisi semua field termasuk question_id
        header("Location: edit_soal.php?id=" . $question_id); // Redirect kembali ke form edit dengan ID
        exit();
    }

    $conn->close();

} else {
    // Jika diakses langsung tanpa metode POST
    $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Akses tidak sah.'];
    header("Location: manajemen_soal.php");
    exit();
}
?>
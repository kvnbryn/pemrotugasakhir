<?php
// hapus_soal.php
// Pastikan file ini ada di dalam folder testIQ/admin/
require_once '../config/db.php'; // session_start() sudah ada di db.php
require_once 'admin_header.php'; // Untuk otentikasi admin dan variabel $conn

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'ID Soal tidak valid untuk dihapus.'];
    header("Location: manajemen_soal.php");
    exit();
}

$question_id = (int)$_GET['id'];

if ($question_id > 0) {
    // Sebelum menghapus soal, mungkin ada baiknya memeriksa apakah soal ini pernah digunakan dalam tes.
    // Untuk simplisitas, kita langsung hapus. Jika ada foreign key constraint yang ketat di user_answers,
    // penghapusan bisa gagal jika soal sudah pernah dijawab.
    // Solusi: ON DELETE SET NULL atau ON DELETE CASCADE pada foreign key di user_answers, atau hapus dulu user_answers terkait.
    // Untuk saat ini, kita asumsikan bisa langsung dihapus atau foreign key di user_answers mengizinkan ON DELETE CASCADE atau SET NULL.

    $stmt_check = $conn->prepare("SELECT id FROM questions WHERE id = ?");
    $stmt_check->bind_param("i", $question_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 1) {
        // Hapus dulu jawaban terkait di user_answers untuk menghindari masalah foreign key constraint
        // jika tidak ada ON DELETE CASCADE pada tabel user_answers.
        $stmt_delete_answers = $conn->prepare("DELETE FROM user_answers WHERE question_id = ?");
        if($stmt_delete_answers){
            $stmt_delete_answers->bind_param("i", $question_id);
            if(!$stmt_delete_answers->execute()){
                 // Log error jika gagal hapus user_answers
                error_log("Gagal menghapus user_answers terkait soal ID " . $question_id . ": " . $stmt_delete_answers->error);
                 $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Gagal menghapus jawaban terkait soal. Soal tidak dihapus. Error: '.$stmt_delete_answers->error];
                $stmt_delete_answers->close();
                header("Location: manajemen_soal.php");
                exit();
            }
            $stmt_delete_answers->close();
        } else {
            error_log("Gagal prepare statement untuk menghapus user_answers terkait soal ID " . $question_id . ": " . $conn->error);
            $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Gagal persiapan hapus jawaban terkait soal. Soal tidak dihapus.'];
            header("Location: manajemen_soal.php");
            exit();
        }


        // Lanjutkan menghapus soal
        $stmt_delete_question = $conn->prepare("DELETE FROM questions WHERE id = ?");
        if ($stmt_delete_question) {
            $stmt_delete_question->bind_param("i", $question_id);
            if ($stmt_delete_question->execute()) {
                $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Soal (ID: ' . $question_id . ') dan jawaban terkait berhasil dihapus.'];
            } else {
                // Log error
                error_log("Gagal execute statement penghapusan soal ID " . $question_id . ": " . $stmt_delete_question->error);
                $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Gagal menghapus soal. Error: ' . $stmt_delete_question->error];
            }
            $stmt_delete_question->close();
        } else {
             // Log error
            error_log("Gagal prepare statement penghapusan soal ID " . $question_id . ": " . $conn->error);
            $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan server saat persiapan penghapusan soal.'];
        }
    } else {
        $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Soal dengan ID ' . $question_id . ' tidak ditemukan.'];
    }
    $stmt_check->close();
} else {
    $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'ID Soal tidak valid.'];
}

$conn->close();
header("Location: manajemen_soal.php");
exit();

// Tidak ada output HTML dari file ini, hanya proses dan redirect.
// Jadi admin_footer.php tidak di-include di sini.
?>
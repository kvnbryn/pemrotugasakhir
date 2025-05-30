<?php
// edit_question.php
// Pastikan file ini ada di dalam folder testIQ/admin/
$page_title = "Edit Soal - TesIQOnline";
require_once 'admin_header.php'; // Include header admin, $levels_admin sudah ada

$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$form_errors = [];

// Jika form disubmit untuk update (dari proses_edit_question.php jika ada error)
if (isset($_SESSION['form_data_edit'])) {
    $form_data = $_SESSION['form_data_edit'];
    $question_text = $form_data['question_text'] ?? '';
    $option_a = $form_data['option_a'] ?? '';
    $option_b = $form_data['option_b'] ?? '';
    $option_c = $form_data['option_c'] ?? '';
    $option_d = $form_data['option_d'] ?? '';
    $correct_option = $form_data['correct_option'] ?? '';
    $points = $form_data['points'] ?? 10;
    $level_id_selected = $form_data['level_id'] ?? '';
    $question_id = $form_data['question_id'] ?? $question_id; // Ambil question_id dari form jika ada
    unset($_SESSION['form_data_edit']);
} elseif ($question_id > 0) {
    // Ambil data soal dari database untuk diedit
    $stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $question_data = $result->fetch_assoc();
            $question_text = $question_data['question_text'];
            $option_a = $question_data['option_a'];
            $option_b = $question_data['option_b'];
            $option_c = $question_data['option_c'];
            $option_d = $question_data['option_d'];
            $correct_option = $question_data['correct_option'];
            $points = $question_data['points'];
            $level_id_selected = $question_data['level_id'];
        } else {
            $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Soal tidak ditemukan.'];
            header("Location: manage_questions.php");
            exit();
        }
        $stmt->close();
    } else {
        // Log error
        error_log("Gagal prepare statement untuk mengambil data soal edit: " . $conn->error);
        $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan server saat mengambil data soal.'];
        header("Location: manage_questions.php");
        exit();
    }
} else {
    $_SESSION['admin_message'] = ['type' => 'error', 'text' => 'ID Soal tidak valid untuk diedit.'];
    header("Location: manage_questions.php");
    exit();
}

if (isset($_SESSION['form_errors_edit'])) {
    $form_errors = $_SESSION['form_errors_edit'];
    unset($_SESSION['form_errors_edit']);
}

?>

<h2 class="page-title"><?php echo $page_title; ?> (ID: <?php echo $question_id; ?>)</h2>

<a href="manage_questions.php" class="action-button" style="margin-bottom: 20px; background-color:#6c757d;">&laquo; Kembali ke Daftar Soal</a>

<?php if (!empty($form_errors)) : ?>
    <div class="message error" style="margin-bottom:15px;">
        <strong>Gagal memperbarui soal. Periksa error berikut:</strong><br>
        <?php foreach ($form_errors as $error) : ?>
            - <?php echo htmlspecialchars($error); ?><br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="proses_edit_question.php" method="POST" class="form-container" style="max-width: 700px; margin-left:auto; margin-right:auto;">
    <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">

    <div class="form-group">
        <label for="level_id">Level Tes</label>
        <select name="level_id" id="level_id" required>
            <option value="">-- Pilih Level --</option>
            <?php foreach ($levels_admin as $level) : ?>
                <option value="<?php echo $level['id']; ?>" <?php echo ($level_id_selected == $level['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($level['level_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="question_text">Teks Pertanyaan</label>
        <textarea name="question_text" id="question_text" rows="4" required><?php echo htmlspecialchars($question_text); ?></textarea>
    </div>

    <div class="form-group">
        <label for="option_a">Pilihan A</label>
        <input type="text" name="option_a" id="option_a" value="<?php echo htmlspecialchars($option_a); ?>" required>
    </div>
    <div class="form-group">
        <label for="option_b">Pilihan B</label>
        <input type="text" name="option_b" id="option_b" value="<?php echo htmlspecialchars($option_b); ?>" required>
    </div>
    <div class="form-group">
        <label for="option_c">Pilihan C</label>
        <input type="text" name="option_c" id="option_c" value="<?php echo htmlspecialchars($option_c); ?>" required>
    </div>
    <div class="form-group">
        <label for="option_d">Pilihan D</label>
        <input type="text" name="option_d" id="option_d" value="<?php echo htmlspecialchars($option_d); ?>" required>
    </div>

    <div class="form-group">
        <label for="correct_option">Kunci Jawaban Benar</label>
        <select name="correct_option" id="correct_option" required>
            <option value="">-- Pilih Kunci --</option>
            <option value="A" <?php echo ($correct_option == 'A') ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo ($correct_option == 'B') ? 'selected' : ''; ?>>B</option>
            <option value="C" <?php echo ($correct_option == 'C') ? 'selected' : ''; ?>>C</option>
            <option value="D" <?php echo ($correct_option == 'D') ? 'selected' : ''; ?>>D</option>
        </select>
    </div>

    <div class="form-group">
        <label for="points">Poin untuk Jawaban Benar</label>
        <input type="number" name="points" id="points" value="<?php echo htmlspecialchars($points); ?>" min="1" required>
    </div>

    <button type="submit" class="btn btn-primary btn-form">Update Soal</button>
</form>

<?php
require_once 'admin_footer.php'; // Include footer admin
?>
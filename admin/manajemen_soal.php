<?php
// manage_questions.php
// Pastikan file ini ada di dalam folder testIQ/admin/
$page_title = "Kelola Soal Tes IQ - TesIQOnline";
require_once 'admin_header.php'; // Include header admin

// Logika untuk menghandle pesan (misalnya setelah add, edit, delete)
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message']['type']) . '" style="margin-bottom:15px;">' . htmlspecialchars($_SESSION['admin_message']['text']) . '</div>';
    unset($_SESSION['admin_message']);
}

// Pagination
$limit = 10; // Jumlah soal per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter berdasarkan level
$filter_level_id = isset($_GET['level_filter']) && is_numeric($_GET['level_filter']) ? (int)$_GET['level_filter'] : null;

// Bangun query dasar
$sql_questions = "SELECT q.id, q.question_text, q.correct_option, q.points, l.level_name 
                  FROM questions q 
                  JOIN levels l ON q.level_id = l.id";
$sql_count = "SELECT COUNT(*) as total FROM questions q";

$where_clauses = [];
$params = [];
$types = "";

if ($filter_level_id) {
    $where_clauses[] = "q.level_id = ?";
    $params[] = $filter_level_id;
    $types .= "i";
}

if (!empty($where_clauses)) {
    $sql_questions .= " WHERE " . implode(" AND ", $where_clauses);
    $sql_count .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql_questions .= " ORDER BY l.id ASC, q.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Get total questions for pagination
$stmt_count = $conn->prepare($sql_count);
if ($filter_level_id) {
    // Hanya bind level_id jika ada filter
    $stmt_count->bind_param("i", $filter_level_id);
}
$stmt_count->execute();
$total_questions = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_questions / $limit);
$stmt_count->close();


// Get questions for current page
$stmt_questions = $conn->prepare($sql_questions);
if (!empty($params)) {
    $stmt_questions->bind_param($types, ...$params);
}
$stmt_questions->execute();
$result_questions = $stmt_questions->get_result();
$questions_list = [];
if ($result_questions->num_rows > 0) {
    while ($row = $result_questions->fetch_assoc()) {
        $questions_list[] = $row;
    }
}
$stmt_questions->close();

?>

<h2 class="page-title"><?php echo $page_title; ?></h2>

<a href="add_question.php" class="action-button" style="margin-bottom: 20px;">+ Tambah Soal Baru</a>

<form method="GET" action="manage_questions.php" style="margin-bottom: 20px; background-color:#f8f9fa; padding:15px; border-radius:5px;">
    <label for="level_filter">Filter berdasarkan Level:</label>
    <select name="level_filter" id="level_filter" onchange="this.form.submit()">
        <option value="">Semua Level</option>
        <?php foreach ($levels_admin as $level) : ?>
            <option value="<?php echo $level['id']; ?>" <?php echo ($filter_level_id == $level['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($level['level_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit" class="btn btn-secondary" style="padding:5px 10px; font-size:0.9em;">Filter</button></noscript>
</form>


<?php if (!empty($questions_list)) : ?>
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>ID Soal</th>
                    <th>Teks Pertanyaan (Potongan)</th>
                    <th>Level</th>
                    <th>Kunci Jawaban</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions_list as $question) : ?>
                    <tr>
                        <td><?php echo $question['id']; ?></td>
                        <td><?php echo htmlspecialchars(substr($question['question_text'], 0, 70)) . (strlen($question['question_text']) > 70 ? '...' : ''); ?></td>
                        <td><?php echo htmlspecialchars($question['level_name']); ?></td>
                        <td><?php echo htmlspecialchars($question['correct_option']); ?></td>
                        <td><?php echo htmlspecialchars($question['points']); ?></td>
                        <td class="action-links">
                            <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="action-button edit" style="padding:5px 8px; font-size:0.8em; margin-right:5px;">Edit</a>
                            <a href="delete_question.php?id=<?php echo $question['id']; ?>" class="action-button delete" style="padding:5px 8px; font-size:0.8em;" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination" style="margin-top: 20px; text-align: center;">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo $page - 1; ?>&level_filter=<?php echo $filter_level_id; ?>" class="btn btn-secondary">&laquo; Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>&level_filter=<?php echo $filter_level_id; ?>" class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages) : ?>
            <a href="?page=<?php echo $page + 1; ?>&level_filter=<?php echo $filter_level_id; ?>" class="btn btn-secondary">Berikutnya &raquo;</a>
        <?php endif; ?>
    </div>

<?php else : ?>
    <div class="message info">Belum ada soal yang tersedia <?php echo $filter_level_id ? 'untuk level ini' : ''; ?>. Silakan tambahkan soal baru.</div>
<?php endif; ?>

<?php
$conn->close();
require_once 'admin_footer.php'; // Include footer admin
?>
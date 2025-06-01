<?php
require_once 'config/db.php'; // Sudah termasuk session_start()

// Ambil data ranking: Username dan skor tertinggi yang pernah dicapai user
// Kita hanya akan mengambil skor tertinggi per user
$sql_ranking = "SELECT u.username, MAX(ut.score) AS highest_score, MAX(ut.test_date) AS last_test_date
                FROM users u
                JOIN user_tests ut ON u.id = ut.user_id
                WHERE u.role = 'user'  -- Hanya rank user biasa, bukan admin
                GROUP BY u.id, u.username
                ORDER BY highest_score DESC, last_test_date ASC -- Jika skor sama, yg lebih dulu capai skor itu lebih tinggi
                LIMIT 100"; // Batasi misal top 100

$result_ranking = $conn->query($sql_ranking);
$rankings = [];
if ($result_ranking && $result_ranking->num_rows > 0) {
    while ($row = $result_ranking->fetch_assoc()) {
        $rankings[] = $row;
    }
}

$username = $_SESSION['username'] ?? ''; // Untuk navigasi

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Peringkat Tes IQ - TesIQOnline</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/ranking.css">
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
                        <li><a href="ranking.php" class="active">Peringkat</a></li>
                        <li><a href="logout.php">Logout (<?php echo htmlspecialchars($username); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="ranking.php" class="active">Peringkat</a></li>
                    <?php endif; ?>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <?php if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] != 'admin')): ?>
                         <li><a href="admin/login_admin.php" class="admin-link">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="ranking-container">
            <h2>🏆 Papan Peringkat Pengguna 🏆</h2>

            <?php if (!empty($rankings)): ?>
                <div class="table-container">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Pengguna</th>
                                <th>Skor Tertinggi</th>
                                <th>Tes Terakhir (Skor Tertinggi)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankings as $index => $rank_data): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $rank_display = $index + 1;
                                        if ($index == 0) {
                                            echo '<span class="trophy-gold">🥇 ' . $rank_display . '</span>';
                                        } elseif ($index == 1) {
                                            echo '<span class="trophy-silver">🥈 ' . $rank_display . '</span>';
                                        } elseif ($index == 2) {
                                            echo '<span class="trophy-bronze">🥉 ' . $rank_display . '</span>';
                                        } else {
                                            echo $rank_display;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($rank_data['username']); ?></td>
                                    <td><?php echo htmlspecialchars($rank_data['highest_score']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($rank_data['last_test_date']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card" style="text-align:center;">
                    <p>Belum ada data peringkat yang tersedia. Ayo mulai kerjakan tes!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer style="margin-top: 50px;">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/transisi.js"></script>
</body>
</html>
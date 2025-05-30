<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['login_error'] = "Anda harus login sebagai user untuk mengakses halaman ini.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil daftar level tes dari database
$levels = [];
$sql_levels = "SELECT id, level_name, description FROM levels WHERE level_name != 'Premium' ORDER BY id ASC"; // Non-premium levels
$result_levels = $conn->query($sql_levels);
if ($result_levels && $result_levels->num_rows > 0) {
    while ($row = $result_levels->fetch_assoc()) {
        $levels[] = $row;
    }
}

// Cek apakah user premium untuk menampilkan level premium
$is_premium_user = $_SESSION['is_premium'] ?? false;
if ($is_premium_user) {
    $sql_premium_level = "SELECT id, level_name, description FROM levels WHERE level_name = 'Premium'";
    $result_premium_level = $conn->query($sql_premium_level);
    if ($result_premium_level && $result_premium_level->num_rows > 0) {
        $premium_level_data = $result_premium_level->fetch_assoc();
        if ($premium_level_data) {
            $levels[] = $premium_level_data; // Add premium level if user is premium
        }
    }
}

// Ambil riwayat tes pengguna
$test_history = [];
$sql_history = "SELECT lt.level_name, ut.score, ut.test_date, ut.time_taken_seconds 
                FROM user_tests ut
                JOIN levels lt ON ut.level_id = lt.id
                WHERE ut.user_id = ? 
                ORDER BY ut.test_date DESC LIMIT 5"; // Ambil 5 tes terakhir
$stmt_history = $conn->prepare($sql_history);
if($stmt_history){
    $stmt_history->bind_param("i", $user_id);
    $stmt_history->execute();
    $result_history = $stmt_history->get_result();
    if ($result_history && $result_history->num_rows > 0) {
        while ($row = $result_history->fetch_assoc()) {
            $test_history[] = $row;
        }
    }
    $stmt_history->close();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - TesIQOnline</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                    <li><a href="dashboard_user.php" class="active">Dashboard</a></li>
                    <li><a href="ranking.php">Peringkat</a></li>  
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($username); ?>)</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container container">
            <div class="dashboard-header">
                <h1>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Pilih level tes IQ yang ingin kamu coba atau lihat riwayat tesmu.</p>
                 <?php if (!$is_premium_user): ?>
                    <div class="premium-banner" style="background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; text-align:center;">
                        <p style="margin-bottom: 10px;">Ingin akses soal yang lebih akurat dan menantang? Upgrade ke akun Premium!</p>
                        <a href="upgrade_premium.php" class="btn btn-secondary" style="background-color: #ffc107; color: #333;">Upgrade Sekarang (Simulasi)</a>
                    </div>
                <?php else: ?>
                     <div class="premium-banner" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; text-align:center;">
                        <p style="margin-bottom: 0px;">Anda adalah pengguna Premium! Nikmati akses ke semua level tes.</p>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="section-title" style="margin-top: 40px; margin-bottom: 30px; text-align:left;">Pilih Level Tes</h2>
            <?php if (!empty($levels)): ?>
                <div class="card-grid">
                    <?php foreach ($levels as $level): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($level['level_name']); ?></h3>
                            <p><?php echo htmlspecialchars($level['description']); ?></p>
                            <?php if ($level['level_name'] == 'Premium' && !$is_premium_user): ?>
                                <a href="upgrade_premium.php" class="btn btn-primary disabled" style="background-color: #6c757d; cursor: not-allowed;" title="Upgrade ke Premium untuk akses level ini">Mulai Tes (Premium)</a>
                            <?php else: ?>
                                <a href="test.php?level_id=<?php echo $level['id']; ?>" class="btn btn-primary">Mulai Tes</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Belum ada level tes yang tersedia saat ini.</p>
            <?php endif; ?>

            <h2 class="section-title" style="margin-top: 50px; margin-bottom: 30px; text-align:left;">Riwayat Tes Terakhir</h2>
            <?php if (!empty($test_history)): ?>
                <div class="table-container">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Level Tes</th>
                                <th>Skor</th>
                                <th>Tanggal Tes</th>
                                <th>Waktu Pengerjaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($test_history as $history): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($history['level_name']); ?></td>
                                    <td><?php echo htmlspecialchars($history['score']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($history['test_date']))); ?></td>
                                    <td><?php echo $history['time_taken_seconds'] ? htmlspecialchars(floor($history['time_taken_seconds'] / 60)) . ' menit ' . htmlspecialchars($history['time_taken_seconds'] % 60) . ' detik' : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                 <p style="text-align: right; margin-top: 15px;"><a href="riwayat_lengkap.php" class="form-link" style="display: inline;">Lihat Semua Riwayat</a></p>
            <?php else: ?>
                <div class="card" style="text-align:center;">
                    <p>Kamu belum pernah mengerjakan tes apapun.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek [Nama Tim Kamu]</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

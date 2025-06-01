<?php
require_once '../config/db.php'; // session_start() sudah ada di db.php

// Cek apakah user adalah admin dan sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error_admin'] = "Anda harus login sebagai Admin untuk mengakses halaman ini.";
    header("Location: login_admin.php");
    exit();
}

$admin_username = $_SESSION['username']; // Ambil username admin dari sesi

// Ambil daftar level dari database untuk dropdown di form tambah/edit soal
$levels_admin = [];
$sql_levels_admin = "SELECT id, level_name FROM levels ORDER BY id ASC";
$result_levels_admin = $conn->query($sql_levels_admin);
if ($result_levels_admin && $result_levels_admin->num_rows > 0) {
    while ($row_level_admin = $result_levels_admin->fetch_assoc()) {
        $levels_admin[] = $row_level_admin;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                 <h1><a href="dashboard_admin.php" style="text-decoration: none; color: inherit;">TesIQ<span class="logo-online">Online</span> - Admin Panel</a></h1>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard_admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="manajemen_soal.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manajemen_soal.php' || basename($_SERVER['PHP_SELF']) == 'tambah_soal.php' || basename($_SERVER['PHP_SELF']) == 'edit_soal.php' ? 'active' : ''; ?>">Kelola Soal</a></li>
                    <li><a href="logout_admin.php">Logout (<?php echo htmlspecialchars($admin_username); ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="admin-container">
            ```
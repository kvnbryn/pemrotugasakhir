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
    <style>
        /* Tambahan style khusus untuk admin jika diperlukan */
        .admin-nav ul {
            display: flex;
            justify-content: center; /* Tengahkan navigasi */
            padding-left: 0; /* Hapus padding default dari ul */
        }
        .admin-nav ul li {
            margin: 0 15px; /* Jarak antar item navigasi */
        }
        .admin-nav ul li a {
            padding: 8px 15px;
        }
        .admin-container {
            width: 95%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .page-title {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .action-button:hover {
            background-color: #218838;
        }
         .action-button.edit {
            background-color: #ffc107;
            color: #333;
        }
        .action-button.edit:hover {
            background-color: #e0a800;
        }
        .action-button.delete {
            background-color: #dc3545;
        }
        .action-button.delete:hover {
            background-color: #c82333;
        }
        .content-table th, .content-table td {
             border: 1px solid #dddddd; /* Garis batas lebih jelas untuk tabel admin */
        }
    </style>
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
                    <li><a href="manage_questions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_questions.php' || basename($_SERVER['PHP_SELF']) == 'add_question.php' || basename($_SERVER['PHP_SELF']) == 'edit_question.php' ? 'active' : ''; ?>">Kelola Soal</a></li>
                    <li><a href="logout_admin.php">Logout (<?php echo htmlspecialchars($admin_username); ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="admin-container">
            ```
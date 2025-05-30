<?php
require_once '../config/db.php';
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: dashboard_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TesIQOnline</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                 <h1><a href="../index.php" style="text-decoration: none; color: inherit;">TesIQ<span class="logo-online">Online</span> - Admin</a></h1>
            </div>
            </div>
    </header>

    <main>
        <div class="form-container" style="margin-top: 50px;">
            <h2>Login Administrator</h2>

            <?php
            if (isset($_SESSION['login_error_admin'])) {
                echo '<div class="message error">' . htmlspecialchars($_SESSION['login_error_admin']) . '</div>';
                unset($_SESSION['login_error_admin']);
            }
            $login_identifier_admin = $_SESSION['form_input_admin']['login_identifier_admin'] ?? '';
            unset($_SESSION['form_input_admin']);
            ?>

            <form action="proses_login_admin.php" method="POST" id="loginAdminForm">
                <div class="form-group">
                    <label for="login_identifier_admin">Username atau Email Admin</label>
                    <input type="text" id="login_identifier_admin" name="login_identifier_admin" value="<?php echo htmlspecialchars($login_identifier_admin); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password_admin">Password Admin</label>
                    <input type="password" id="password_admin" name="password_admin" required>
                </div>
                <button type="submit" class="btn btn-primary btn-form">Login Admin</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                <a href="../index.php" class="form-link" style="display: inline;">Kembali ke Halaman Utama</a>
            </p>
        </div>
    </main>

    <footer style="position: fixed; bottom: 0; width:100%;">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Admin Panel</p>
        </div>
    </footer>

    </body>
</html>
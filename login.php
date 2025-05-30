<?php
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard_user.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TesIQOnline</title>
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
                    <li><a href="login.php" class="active">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="ranking.php">Peringkat</a></li>  
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="admin/login_admin.php" class="admin-link">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2>Login ke Akun Anda</h2>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="message error">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div class="message success">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                unset($_SESSION['register_success']);
            }
            $login_identifier = $_SESSION['form_input']['login_identifier'] ?? '';
            unset($_SESSION['form_input']);
            ?>

            <form action="proses_login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="login_identifier">Username atau Email</label>
                    <input type="text" id="login_identifier" name="login_identifier" value="<?php echo htmlspecialchars($login_identifier); ?>" required>
                    <small class="form-error-message" id="loginIdentifierError"></small>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small class="form-error-message" id="passwordError"></small>
                </div>
                <button type="submit" class="btn btn-primary btn-form">Login</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                Belum punya akun? <a href="register.php" class="form-link" style="display: inline;">Register di sini</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TesIQOnline - Tim Proyek [Nama Tim Kamu]</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    
                    const loginIdentifierInput = document.getElementById('login_identifier');
                    const loginIdentifierError = document.getElementById('loginIdentifierError');
                    if (loginIdentifierInput.value.trim() === '') {
                        isValid = false;
                        loginIdentifierError.textContent = 'Username atau Email tidak boleh kosong.';
                        loginIdentifierInput.style.borderColor = 'red';
                    } else {
                        loginIdentifierError.textContent = '';
                        loginIdentifierInput.style.borderColor = '#ddd';
                    }

                    const passwordInput = document.getElementById('password');
                    const passwordError = document.getElementById('passwordError');
                    if (passwordInput.value === '') {
                        isValid = false;
                        passwordError.textContent = 'Password tidak boleh kosong.';
                        passwordInput.style.borderColor = 'red';
                    } else {
                        passwordError.textContent = '';
                        passwordInput.style.borderColor = '#ddd';
                    }

                    if (!isValid) {
                        event.preventDefault(); 
                    }
                });
            }
        });
    </script>
</body>
</html>

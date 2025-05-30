<?php
require_once 'config/db.php';

$errors = [];
$username = '';
$email = '';

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
    <title>Register Akun - TesIQOnline</title>
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
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="active">Register</a></li>
                    <li><a href="ranking.php">Peringkat</a></li>  
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="admin/login_admin.php" class="admin-link">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2>Buat Akun Baru</h2>

            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<div class="message error">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                unset($_SESSION['register_error']); 
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div class="message success">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                unset($_SESSION['register_success']);
            }
            // Untuk menampilkan kembali input jika ada error (diambil dari session jika proses_register.php mengaturnya)
            $username = $_SESSION['form_input']['username'] ?? '';
            $email = $_SESSION['form_input']['email'] ?? '';
            unset($_SESSION['form_input']);
            ?>

            <form action="proses_register.php" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <small class="form-error-message" id="usernameError"></small>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <small class="form-error-message" id="emailError"></small>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small class="form-error-message" id="passwordError"></small>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <small class="form-error-message" id="confirmPasswordError"></small>
                </div>
                <button type="submit" class="btn btn-primary btn-form">Register</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                Sudah punya akun? <a href="login.php" class="form-link" style="display: inline;">Login di sini</a>
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
            const registerForm = document.getElementById('registerForm');
            if (registerForm) {
                registerForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    
                    const usernameInput = document.getElementById('username');
                    const usernameError = document.getElementById('usernameError');
                    if (usernameInput.value.trim().length < 3) {
                        isValid = false;
                        usernameError.textContent = 'Username minimal 3 karakter.';
                        usernameInput.style.borderColor = 'red';
                    } else {
                        usernameError.textContent = '';
                        usernameInput.style.borderColor = '#ddd';
                    }

                    const emailInput = document.getElementById('email');
                    const emailError = document.getElementById('emailError');
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(emailInput.value.trim())) {
                        isValid = false;
                        emailError.textContent = 'Format email tidak valid.';
                        emailInput.style.borderColor = 'red';
                    } else {
                        emailError.textContent = '';
                        emailInput.style.borderColor = '#ddd';
                    }

                    const passwordInput = document.getElementById('password');
                    const passwordError = document.getElementById('passwordError');
                    if (passwordInput.value.length < 6) {
                        isValid = false;
                        passwordError.textContent = 'Password minimal 6 karakter.';
                        passwordInput.style.borderColor = 'red';
                    } else {
                        passwordError.textContent = '';
                        passwordInput.style.borderColor = '#ddd';
                    }

                    const confirmPasswordInput = document.getElementById('confirm_password');
                    const confirmPasswordError = document.getElementById('confirmPasswordError');
                    if (confirmPasswordInput.value !== passwordInput.value) {
                        isValid = false;
                        confirmPasswordError.textContent = 'Konfirmasi password tidak cocok.';
                        confirmPasswordInput.style.borderColor = 'red';
                    } else {
                        confirmPasswordError.textContent = '';
                        confirmPasswordInput.style.borderColor = '#ddd';
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

<?php
require_once 'config/db.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username hanya boleh berisi huruf, angka, dan underscore.";
    }

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if ($stmt_check) {
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $stmt_check->bind_result($user_id_exists); //dummy variable
                $stmt_check->fetch();
                
                // Check specifically which one exists
                $stmt_check_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_check_user->bind_param("s", $username);
                $stmt_check_user->execute();
                $stmt_check_user->store_result();
                if($stmt_check_user->num_rows > 0) {
                     $errors[] = "Username sudah terdaftar. Silakan gunakan username lain.";
                }
                $stmt_check_user->close();

                $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_check_email->bind_param("s", $email);
                $stmt_check_email->execute();
                $stmt_check_email->store_result();
                if($stmt_check_email->num_rows > 0) {
                     $errors[] = "Email sudah terdaftar. Silakan gunakan email lain.";
                }
                $stmt_check_email->close();
            }
            $stmt_check->close();
        } else {
            $errors[] = "Terjadi kesalahan dalam persiapan statement pengecekan: " . $conn->error;
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // Default role for new users

        $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt_insert) {
            $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);
            if ($stmt_insert->execute()) {
                $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Registrasi gagal. Terjadi kesalahan saat menyimpan data: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
             $errors[] = "Terjadi kesalahan dalam persiapan statement insert: " . $conn->error;
        }
    }

    if (!empty($errors)) {
        $_SESSION['register_error'] = implode("<br>", $errors);
        // Simpan input form ke session agar bisa ditampilkan kembali di halaman register
        $_SESSION['form_input'] = ['username' => $username, 'email' => $email];
        header("Location: register.php");
        exit();
    }

    $conn->close();

} else {
    // Jika diakses langsung tanpa metode POST, redirect ke halaman registrasi
    header("Location: register.php");
    exit();
}
?>

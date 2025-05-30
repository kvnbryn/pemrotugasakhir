<?php
// proses_login_admin.php
// Pastikan file ini ada di dalam folder testIQ/admin/
require_once '../config/db.php'; // session_start() sudah ada di db.php

$errors = [];
$login_identifier_admin = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_identifier_admin = trim($_POST['login_identifier_admin']);
    $password_admin = $_POST['password_admin'];

    if (empty($login_identifier_admin)) {
        $errors[] = "Username atau Email Admin tidak boleh kosong.";
    }
    if (empty($password_admin)) {
        $errors[] = "Password Admin tidak boleh kosong.";
    }

    if (empty($errors)) {
        $field_type = filter_var($login_identifier_admin, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $sql = "SELECT id, username, password, role FROM users WHERE $field_type = ? AND role = 'admin'";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $login_identifier_admin);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $admin = $result->fetch_assoc();
                if (password_verify($password_admin, $admin['password'])) {
                    // Password is correct, start session for admin
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['role'] = $admin['role'];
                    
                    // Hapus session error login user biasa jika ada
                    unset($_SESSION['login_error']);
                    unset($_SESSION['form_input']);

                    header("Location: dashboard_admin.php");
                    exit();
                } else {
                    $errors[] = "Password Admin salah.";
                }
            } else {
                $errors[] = "Akun Admin tidak ditemukan atau bukan Admin.";
            }
            $stmt->close();
        } else {
            // Log error
            error_log("Admin login statement preparation failed: " . $conn->error);
            $errors[] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['login_error_admin'] = implode("<br>", $errors);
        $_SESSION['form_input_admin'] = ['login_identifier_admin' => $login_identifier_admin];
        header("Location: login_admin.php");
        exit();
    }

    $conn->close();

} else {
    // Jika diakses langsung tanpa metode POST
    header("Location: login_admin.php");
    exit();
}
?>
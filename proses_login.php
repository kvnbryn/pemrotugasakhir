<?php
require_once 'config/db.php';

$errors = [];
$login_identifier = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_identifier = trim($_POST['login_identifier']);
    $password = $_POST['password'];

    if (empty($login_identifier)) {
        $errors[] = "Username atau Email tidak boleh kosong.";
    }
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    }

    if (empty($errors)) {
        // Check if login_identifier is email or username
        $field_type = filter_var($login_identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $sql = "SELECT id, username, password, role, is_premium FROM users WHERE $field_type = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $login_identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    // Password is correct, start session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_premium'] = $user['is_premium'];

                    // Redirect based on role
                    if ($user['role'] == 'admin') {
                        header("Location: admin/dashboard_admin.php");
                        exit();
                    } else {
                        header("Location: dashboard_user.php");
                        exit();
                    }
                } else {
                    $errors[] = "Password salah.";
                }
            } else {
                $errors[] = "Username atau Email tidak ditemukan.";
            }
            $stmt->close();
        } else {
            $errors[] = "Terjadi kesalahan dalam persiapan statement: " . $conn->error;
        }
    }

    if (!empty($errors)) {
        $_SESSION['login_error'] = implode("<br>", $errors);
        $_SESSION['form_input'] = ['login_identifier' => $login_identifier];
        header("Location: login.php");
        exit();
    }

    $conn->close();

} else {
    header("Location: login.php");
    exit();
}
?>

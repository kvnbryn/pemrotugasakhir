<?php
// logout_admin.php
// Pastikan file ini ada di dalam folder testIQ/admin/
require_once '../config/db.php'; // Ini akan memanggil session_start()

// Hapus semua variabel sesi yang spesifik untuk admin
// atau hancurkan sesi sepenuhnya jika admin dan user biasa berbagi sesi yang sama
// (dalam kasus ini, mereka berbagi, jadi kita hancurkan sepenuhnya)

$_SESSION = array(); // Kosongkan array sesi

// Jika ingin menghancurkan sesi sepenuhnya, hapus juga cookie sesi.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Hancurkan sesi

// Redirect ke halaman login admin
header("Location: login_admin.php");
exit();
?>
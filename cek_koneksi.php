<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Mencoba memuat config/db.php...<br>";
require_once 'config/db.php'; //

if (isset($conn) && $conn->connect_error) {
    echo "Koneksi database GAGAL: " . $conn->connect_error . "<br>"; //
} elseif (isset($conn)) {
    echo "Koneksi database BERHASIL!<br>"; //
    echo "Sesi juga sudah dimulai (session_start() dipanggil).<br>"; //
} else {
    echo "Variabel koneksi \$conn tidak ditemukan setelah require_once 'config/db.php'.<br>";
}

echo "<hr>Mencoba query sederhana ke tabel 'levels'...<br>";
if (isset($conn) && !$conn->connect_error) {
    $sql = "SELECT COUNT(*) as total_levels FROM levels";
    $result = $conn->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        echo "Jumlah level di database: " . $row['total_levels'] . "<br>";
        if ($row['total_levels'] > 0) {
            echo "Tabel 'levels' berhasil diakses dan berisi data.<br>";
        } else {
            echo "Tabel 'levels' berhasil diakses TAPI KOSONG. Pastikan sudah import db_iqtest.sql.<br>";
        }
    } else {
        echo "Query ke tabel 'levels' GAGAL. Error: " . $conn->error . "<br>";
        echo "Pastikan tabel 'levels' ada dan database 'db_iqtest' sudah dipilih.<br>";
    }
} else {
    echo "Tidak bisa menjalankan query karena koneksi database bermasalah.<br>";
}
?>
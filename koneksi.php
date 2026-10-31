<?php
// Default: Laragon lokal
$host     = "localhost";
$username = "root";
$password = "";
$database = "sim_mahasiswa";
$port     = 3306;

// Kalau ada config.php (di hosting), nilainya menimpa default di atas
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = mysqli_connect($host, $username, $password, $database, $port);

if (!$koneksi) {
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi server.');
}

mysqli_set_charset($koneksi, 'utf8mb4');
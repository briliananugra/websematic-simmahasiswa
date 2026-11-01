<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sudah_login() {
    return !empty($_SESSION['id_pengguna']);
}

// Panggil di halaman yang wajib login
function wajib_login() {
    if (!sudah_login()) {
        header('Location: login.php');
        exit;
    }
}

// Panggil di halaman yang dibatasi role, contoh: wajib_role(['ADMIN','OPERATOR_PRODI'])
function wajib_role(array $role_boleh) {
    wajib_login();
    if (!in_array($_SESSION['kode_role'], $role_boleh, true)) {
        http_response_code(403);
        exit('Akses ditolak: role Anda tidak berwenang membuka halaman ini.');
    }
}
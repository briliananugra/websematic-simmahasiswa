<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cetak teks dengan aman (cegah XSS)
function e($t) {
    return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8');
}

// Hitung jumlah baris, query harus berbentuk SELECT COUNT(*)
function hitung($koneksi, $sql) {
    $r = mysqli_query($koneksi, $sql);
    return $r ? (int) mysqli_fetch_row($r)[0] : 0;
}

// Pesan sekali tampil (dipakai setelah tambah/ubah/hapus)
function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function tampil_flash() {
    if (empty($_SESSION['flash'])) return '';
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alert alert-' . e($f['tipe']) . ' alert-dismissible fade show">'
         . e($f['pesan'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
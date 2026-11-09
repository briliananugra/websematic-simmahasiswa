<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$kembali = BASE_URL . '/pages/role.php';

// Hanya menerima POST (tidak bisa dibuka lewat alamat)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $kembali);
    exit;
}
if (!csrf_valid($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Sesi form tidak valid. Coba lagi.');
    header('Location: ' . $kembali);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

// Aturan 2: role bawaan (id 1-5) tidak boleh dihapus
if ($id <= 5) {
    set_flash('danger', 'Role bawaan tidak bisa dihapus.');
    header('Location: ' . $kembali);
    exit;
}

// Aturan 3: tidak boleh dihapus kalau masih dipakai pengguna
$st = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM pengguna WHERE id_role = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $dipakai);
mysqli_stmt_fetch($st);
mysqli_stmt_close($st);

if ($dipakai > 0) {
    set_flash('danger', "Role tidak bisa dihapus: masih dipakai $dipakai pengguna.");
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM roles WHERE id_role = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$terhapus = mysqli_stmt_affected_rows($st);
mysqli_stmt_close($st);

if ($terhapus > 0) {
    set_flash('success', 'Role berhasil dihapus.');
} else {
    set_flash('danger', 'Role tidak ditemukan.');
}
header('Location: ' . $kembali);
exit;
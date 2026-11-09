<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$kembali = BASE_URL . '/pages/fakultas.php';

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

// Blokir jika masih punya program studi atau pengguna
$st = mysqli_prepare($koneksi,
    "SELECT (SELECT COUNT(*) FROM program_studi WHERE id_fakultas = ?),
            (SELECT COUNT(*) FROM pengguna WHERE id_fakultas = ?)");
mysqli_stmt_bind_param($st, 'ii', $id, $id);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $jmlProdi, $jmlPeng);
mysqli_stmt_fetch($st);
mysqli_stmt_close($st);

if ($jmlProdi > 0 || $jmlPeng > 0) {
    set_flash('danger', "Fakultas tidak bisa dihapus: masih punya $jmlProdi program studi dan $jmlPeng pengguna.");
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM fakultas WHERE id_fakultas = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$terhapus = mysqli_stmt_affected_rows($st);
mysqli_stmt_close($st);

set_flash($terhapus > 0 ? 'success' : 'danger',
          $terhapus > 0 ? 'Fakultas berhasil dihapus.' : 'Fakultas tidak ditemukan.');
header('Location: ' . $kembali);
exit;
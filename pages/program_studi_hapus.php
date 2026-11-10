<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$kembali = BASE_URL . '/pages/program_studi.php';

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

// Blokir jika masih punya mahasiswa atau pengguna
$st = mysqli_prepare($koneksi,
    "SELECT (SELECT COUNT(*) FROM mahasiswa WHERE id_program_studi = ?),
            (SELECT COUNT(*) FROM pengguna WHERE id_program_studi = ?)");
mysqli_stmt_bind_param($st, 'ii', $id, $id);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $jmlMhs, $jmlPeng);
mysqli_stmt_fetch($st);
mysqli_stmt_close($st);

if ($jmlMhs > 0 || $jmlPeng > 0) {
    set_flash('danger', "Program studi tidak bisa dihapus: masih punya $jmlMhs mahasiswa dan $jmlPeng pengguna.");
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM program_studi WHERE id_program_studi = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$terhapus = mysqli_stmt_affected_rows($st);
mysqli_stmt_close($st);

set_flash($terhapus > 0 ? 'success' : 'danger',
          $terhapus > 0 ? 'Program studi berhasil dihapus.' : 'Program studi tidak ditemukan.');
header('Location: ' . $kembali);
exit;
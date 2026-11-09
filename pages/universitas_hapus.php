<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$kembali = BASE_URL . '/pages/universitas.php';

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

// Blokir jika masih punya fakultas atau pengguna
$st = mysqli_prepare($koneksi,
    "SELECT (SELECT COUNT(*) FROM fakultas WHERE id_universitas = ?),
            (SELECT COUNT(*) FROM pengguna WHERE id_universitas = ?)");
mysqli_stmt_bind_param($st, 'ii', $id, $id);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $jmlFak, $jmlPeng);
mysqli_stmt_fetch($st);
mysqli_stmt_close($st);

if ($jmlFak > 0 || $jmlPeng > 0) {
    set_flash('danger', "Universitas tidak bisa dihapus: masih punya $jmlFak fakultas dan $jmlPeng pengguna.");
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM universitas WHERE id_universitas = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$terhapus = mysqli_stmt_affected_rows($st);
mysqli_stmt_close($st);

set_flash($terhapus > 0 ? 'success' : 'danger',
          $terhapus > 0 ? 'Universitas berhasil dihapus.' : 'Universitas tidak ditemukan.');
header('Location: ' . $kembali);
exit;
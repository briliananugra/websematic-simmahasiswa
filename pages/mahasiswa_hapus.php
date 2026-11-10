<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mahasiswa_helper.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI']);

$kembali = BASE_URL . '/pages/mahasiswa.php';

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

$st = mysqli_prepare($koneksi, "SELECT id_program_studi FROM mahasiswa WHERE id_mahasiswa = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Mahasiswa tidak ditemukan.');
    header('Location: ' . $kembali);
    exit;
}
if (!boleh_kelola_prodi((int)$row['id_program_studi'])) {
    set_flash('danger', 'Anda tidak berhak menghapus mahasiswa ini.');
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
mysqli_stmt_close($st);

set_flash('success', 'Mahasiswa berhasil dihapus.');
header('Location: ' . $kembali);
exit;
<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/pengguna_helper.php';
wajib_role(['ADMIN']);

$kembali = BASE_URL . '/pages/pengguna.php';

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

// Aturan 2: tidak boleh menghapus akun sendiri
if ($id === (int)$_SESSION['id_pengguna']) {
    set_flash('danger', 'Akun sendiri tidak bisa dihapus.');
    header('Location: ' . $kembali);
    exit;
}

// Ambil target
$st = mysqli_prepare($koneksi,
    "SELECT p.status_aktif, r.kode_role FROM pengguna p JOIN roles r ON r.id_role = p.id_role
     WHERE p.id_pengguna = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$target = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$target) {
    set_flash('danger', 'Pengguna tidak ditemukan.');
    header('Location: ' . $kembali);
    exit;
}

// Aturan 3: admin aktif terakhir tidak boleh dihapus
if ($target['kode_role'] === 'ADMIN' && $target['status_aktif'] === 'Aktif'
    && jumlah_admin_aktif_lain($koneksi, $id) === 0) {
    set_flash('danger', 'Tidak bisa dihapus: ini satu-satunya admin aktif.');
    header('Location: ' . $kembali);
    exit;
}

$st = mysqli_prepare($koneksi, "DELETE FROM pengguna WHERE id_pengguna = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$terhapus = mysqli_stmt_affected_rows($st);
mysqli_stmt_close($st);

set_flash($terhapus > 0 ? 'success' : 'danger',
          $terhapus > 0 ? 'Pengguna berhasil dihapus.' : 'Pengguna tidak ditemukan.');
header('Location: ' . $kembali);
exit;
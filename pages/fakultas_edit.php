<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/fakultas_helper.php';
wajib_role(['ADMIN']);

$id = (int)($_GET['id'] ?? 0);

$st = mysqli_prepare($koneksi,
    "SELECT id_universitas, kode_fakultas, nama_fakultas FROM fakultas WHERE id_fakultas = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Fakultas tidak ditemukan.');
    header('Location: ' . BASE_URL . '/pages/fakultas.php');
    exit;
}

$listUniv = mysqli_fetch_all(
    mysqli_query($koneksi, "SELECT id_universitas, nama_universitas FROM universitas ORDER BY nama_universitas"),
    MYSQLI_ASSOC);

$d = $row;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_fakultas();
        $errors = validasi_fakultas($koneksi, $d);

        if (!$errors && kode_fakultas_dipakai($koneksi, $d['id_universitas'], $d['kode_fakultas'], $id)) {
            $errors[] = 'Kode fakultas sudah dipakai fakultas lain di universitas ini.';
        }

        if (!$errors) {
            $st = mysqli_prepare($koneksi,
                "UPDATE fakultas SET id_universitas=?, kode_fakultas=?, nama_fakultas=? WHERE id_fakultas=?");
            mysqli_stmt_bind_param($st, 'issi', $d['id_universitas'], $d['kode_fakultas'], $d['nama_fakultas'], $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Fakultas berhasil diubah.');
            header('Location: ' . BASE_URL . '/pages/fakultas.php');
            exit;
        }
    }
}

$judul      = 'Ubah Fakultas';
$menu_aktif = 'fakultas';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Ubah Fakultas</h4>
<p class="text-secondary"><a href="fakultas.php" class="text-decoration-none">Fakultas</a> &rsaquo; Ubah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/fakultas_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
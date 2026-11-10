<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/prodi_helper.php';
wajib_role(['ADMIN']);

$id = (int)($_GET['id'] ?? 0);

$st = mysqli_prepare($koneksi,
    "SELECT id_fakultas, kode_program_studi, nama_program_studi, jenjang, status_aktif
     FROM program_studi WHERE id_program_studi = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Program studi tidak ditemukan.');
    header('Location: ' . BASE_URL . '/pages/program_studi.php');
    exit;
}

$listFak = mysqli_fetch_all(
    mysqli_query($koneksi, "SELECT id_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas"),
    MYSQLI_ASSOC);

$d = $row;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_prodi();
        $errors = validasi_prodi($koneksi, $d);

        if (!$errors && kode_prodi_dipakai($koneksi, $d['id_fakultas'], $d['kode_program_studi'], $id)) {
            $errors[] = 'Kode program studi sudah dipakai prodi lain di fakultas ini.';
        }

        if (!$errors) {
            $st = mysqli_prepare($koneksi,
                "UPDATE program_studi SET id_fakultas=?, kode_program_studi=?, nama_program_studi=?,
                        jenjang=?, status_aktif=?
                 WHERE id_program_studi=?");
            mysqli_stmt_bind_param($st, 'issssi', $d['id_fakultas'], $d['kode_program_studi'],
                $d['nama_program_studi'], $d['jenjang'], $d['status_aktif'], $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Program studi berhasil diubah.');
            header('Location: ' . BASE_URL . '/pages/program_studi.php');
            exit;
        }
    }
}

$judul      = 'Ubah Program Studi';
$menu_aktif = 'program_studi';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Ubah Program Studi</h4>
<p class="text-secondary"><a href="program_studi.php" class="text-decoration-none">Program Studi</a> &rsaquo; Ubah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/prodi_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
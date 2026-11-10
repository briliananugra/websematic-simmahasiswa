<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/prodi_helper.php';
wajib_role(['ADMIN']);

$listFak = mysqli_fetch_all(
    mysqli_query($koneksi, "SELECT id_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas"),
    MYSQLI_ASSOC);

$d = ['id_fakultas' => 0, 'kode_program_studi' => '', 'nama_program_studi' => '',
      'jenjang' => 'S1', 'status_aktif' => 'Aktif'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_prodi();
        $errors = validasi_prodi($koneksi, $d);

        if (!$errors && kode_prodi_dipakai($koneksi, $d['id_fakultas'], $d['kode_program_studi'])) {
            $errors[] = 'Kode program studi sudah dipakai di fakultas ini.';
        }

        if (!$errors) {
            $st = mysqli_prepare($koneksi,
                "INSERT INTO program_studi (id_fakultas, kode_program_studi, nama_program_studi, jenjang, status_aktif)
                 VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($st, 'issss', $d['id_fakultas'], $d['kode_program_studi'],
                $d['nama_program_studi'], $d['jenjang'], $d['status_aktif']);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Program studi berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/pages/program_studi.php');
            exit;
        }
    }
}

$judul      = 'Tambah Program Studi';
$menu_aktif = 'program_studi';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Tambah Program Studi</h4>
<p class="text-secondary"><a href="program_studi.php" class="text-decoration-none">Program Studi</a> &rsaquo; Tambah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/prodi_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mahasiswa_helper.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI']);

$listProdi = daftar_prodi_mhs($koneksi);
$listAkun  = daftar_akun_mhs_bebas($koneksi, 0);

$d = ['id_pengguna' => 0, 'id_program_studi' => 0, 'npm' => '', 'nama_mahasiswa' => '',
      'jenis_kelamin' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '',
      'tanggal_masuk' => date('Y-m-d'), 'alamat' => '', 'status_mahasiswa' => 'Aktif'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_mhs();

        // Operator: prodi dipaksa prodinya sendiri (kiriman form diabaikan)
        if ($_SESSION['kode_role'] === 'OPERATOR_PRODI') {
            $d['id_program_studi'] = (int)($_SESSION['id_program_studi'] ?? 0);
        }

        $errors = validasi_mhs($koneksi, $d, 0);

        if (!$errors) {
            $akun   = id_atau_null($d['id_pengguna']);
            $tempat = $d['tempat_lahir'] === '' ? null : $d['tempat_lahir'];
            $lahir  = $d['tanggal_lahir'] === '' ? null : $d['tanggal_lahir'];
            $alamat = $d['alamat'] === '' ? null : $d['alamat'];

            $st = mysqli_prepare($koneksi,
                "INSERT INTO mahasiswa (id_pengguna, id_program_studi, npm, nama_mahasiswa, jenis_kelamin,
                                        tempat_lahir, tanggal_lahir, tanggal_masuk, alamat, status_mahasiswa)
                 VALUES (?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($st, 'iissssssss', $akun, $d['id_program_studi'], $d['npm'],
                $d['nama_mahasiswa'], $d['jenis_kelamin'], $tempat, $lahir, $d['tanggal_masuk'],
                $alamat, $d['status_mahasiswa']);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Mahasiswa berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/pages/mahasiswa.php');
            exit;
        }
    }
}

$judul      = 'Tambah Mahasiswa';
$menu_aktif = 'mahasiswa';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Tambah Mahasiswa</h4>
<p class="text-secondary"><a href="mahasiswa.php" class="text-decoration-none">Mahasiswa</a> &rsaquo; Tambah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/mahasiswa_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
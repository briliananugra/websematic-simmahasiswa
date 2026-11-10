<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mahasiswa_helper.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI']);

$kembali = BASE_URL . '/pages/mahasiswa.php';
$id = (int)($_GET['id'] ?? 0);

$st = mysqli_prepare($koneksi,
    "SELECT id_pengguna, id_program_studi, npm, nama_mahasiswa, jenis_kelamin, tempat_lahir,
            tanggal_lahir, tanggal_masuk, alamat, status_mahasiswa
     FROM mahasiswa WHERE id_mahasiswa = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Mahasiswa tidak ditemukan.');
    header('Location: ' . $kembali);
    exit;
}
// Operator tidak boleh membuka mahasiswa prodi lain
if (!boleh_kelola_prodi((int)$row['id_program_studi'])) {
    set_flash('danger', 'Anda tidak berhak mengelola mahasiswa ini.');
    header('Location: ' . $kembali);
    exit;
}

$listProdi = daftar_prodi_mhs($koneksi);
$listAkun  = daftar_akun_mhs_bebas($koneksi, $id);

$d = [
    'id_pengguna'      => (int)($row['id_pengguna'] ?? 0),
    'id_program_studi' => (int)$row['id_program_studi'],
    'npm'              => $row['npm'],
    'nama_mahasiswa'   => $row['nama_mahasiswa'],
    'jenis_kelamin'    => $row['jenis_kelamin'],
    'tempat_lahir'     => (string)($row['tempat_lahir'] ?? ''),
    'tanggal_lahir'    => (string)($row['tanggal_lahir'] ?? ''),
    'tanggal_masuk'    => $row['tanggal_masuk'],
    'alamat'           => (string)($row['alamat'] ?? ''),
    'status_mahasiswa' => $row['status_mahasiswa'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_mhs();

        // Operator: prodi tetap prodinya sendiri, tidak bisa dipindah ke prodi lain
        if ($_SESSION['kode_role'] === 'OPERATOR_PRODI') {
            $d['id_program_studi'] = (int)($_SESSION['id_program_studi'] ?? 0);
        }

        $errors = validasi_mhs($koneksi, $d, $id);

        if (!$errors) {
            $akun   = id_atau_null($d['id_pengguna']);
            $tempat = $d['tempat_lahir'] === '' ? null : $d['tempat_lahir'];
            $lahir  = $d['tanggal_lahir'] === '' ? null : $d['tanggal_lahir'];
            $alamat = $d['alamat'] === '' ? null : $d['alamat'];

            $st = mysqli_prepare($koneksi,
                "UPDATE mahasiswa SET id_pengguna=?, id_program_studi=?, npm=?, nama_mahasiswa=?,
                        jenis_kelamin=?, tempat_lahir=?, tanggal_lahir=?, tanggal_masuk=?, alamat=?,
                        status_mahasiswa=?
                 WHERE id_mahasiswa=?");
            mysqli_stmt_bind_param($st, 'iissssssssi', $akun, $d['id_program_studi'], $d['npm'],
                $d['nama_mahasiswa'], $d['jenis_kelamin'], $tempat, $lahir, $d['tanggal_masuk'],
                $alamat, $d['status_mahasiswa'], $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Mahasiswa berhasil diubah.');
            header('Location: ' . $kembali);
            exit;
        }
    }
}

$judul      = 'Ubah Mahasiswa';
$menu_aktif = 'mahasiswa';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Ubah Mahasiswa</h4>
<p class="text-secondary"><a href="mahasiswa.php" class="text-decoration-none">Mahasiswa</a> &rsaquo; Ubah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/mahasiswa_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
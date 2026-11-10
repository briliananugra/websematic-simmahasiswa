<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/pengguna_helper.php';
wajib_role(['ADMIN']);

$listRole  = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_role, nama_role FROM roles ORDER BY id_role"), MYSQLI_ASSOC);
$listUniv  = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_universitas, nama_universitas FROM universitas ORDER BY nama_universitas"), MYSQLI_ASSOC);
$listFak   = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas"), MYSQLI_ASSOC);
$listProdi = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_program_studi, nama_program_studi FROM program_studi ORDER BY nama_program_studi"), MYSQLI_ASSOC);

$edit = false;
$kunciAkunSendiri = false;
$d = ['id_role' => 0, 'id_universitas' => 0, 'id_fakultas' => 0, 'id_program_studi' => 0,
      'username' => '', 'nama_lengkap' => '', 'email' => '', 'no_hp' => '',
      'status_aktif' => 'Aktif', 'password' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_pengguna();
        $kodeRole = null;
        $errors = validasi_pengguna($koneksi, $d, true, $kodeRole);

        if (!$errors && username_dipakai($koneksi, $d['username'])) {
            $errors[] = 'Username sudah dipakai.';
        }

        if (!$errors) {
            $hash  = password_hash($d['password'], PASSWORD_DEFAULT);
            $uni   = id_atau_null($d['id_universitas']);
            $fak   = id_atau_null($d['id_fakultas']);
            $prodi = id_atau_null($d['id_program_studi']);
            $email = $d['email'] === '' ? null : $d['email'];
            $hp    = $d['no_hp'] === '' ? null : $d['no_hp'];

            $st = mysqli_prepare($koneksi,
                "INSERT INTO pengguna (id_role, id_universitas, id_fakultas, id_program_studi, username,
                                       password_hash, nama_lengkap, email, no_hp, status_aktif)
                 VALUES (?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($st, 'iiiissssss', $d['id_role'], $uni, $fak, $prodi, $d['username'],
                $hash, $d['nama_lengkap'], $email, $hp, $d['status_aktif']);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Pengguna berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/pages/pengguna.php');
            exit;
        }
    }
}

$judul      = 'Tambah Pengguna';
$menu_aktif = 'pengguna';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Tambah Pengguna</h4>
<p class="text-secondary"><a href="pengguna.php" class="text-decoration-none">Pengguna</a> &rsaquo; Tambah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/pengguna_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/pengguna_helper.php';
wajib_role(['ADMIN']);

$id     = (int)($_GET['id'] ?? 0);
$idSaya = (int)$_SESSION['id_pengguna'];

$st = mysqli_prepare($koneksi,
    "SELECT p.id_role, p.id_universitas, p.id_fakultas, p.id_program_studi, p.username,
            p.nama_lengkap, p.email, p.no_hp, p.status_aktif, r.kode_role AS kode_role_lama
     FROM pengguna p JOIN roles r ON r.id_role = p.id_role
     WHERE p.id_pengguna = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Pengguna tidak ditemukan.');
    header('Location: ' . BASE_URL . '/pages/pengguna.php');
    exit;
}

$sendiri          = $id === $idSaya;
$kunciAkunSendiri = $sendiri;
$roleLama         = (int)$row['id_role'];
$statusLama       = $row['status_aktif'];
$adalahAdminAktif = $row['kode_role_lama'] === 'ADMIN' && $statusLama === 'Aktif';

$listRole  = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_role, nama_role FROM roles ORDER BY id_role"), MYSQLI_ASSOC);
$listUniv  = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_universitas, nama_universitas FROM universitas ORDER BY nama_universitas"), MYSQLI_ASSOC);
$listFak   = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas"), MYSQLI_ASSOC);
$listProdi = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id_program_studi, nama_program_studi FROM program_studi ORDER BY nama_program_studi"), MYSQLI_ASSOC);

$edit = true;
$d = [
    'id_role'          => (int)$row['id_role'],
    'id_universitas'   => (int)($row['id_universitas'] ?? 0),
    'id_fakultas'      => (int)($row['id_fakultas'] ?? 0),
    'id_program_studi' => (int)($row['id_program_studi'] ?? 0),
    'username'         => $row['username'],
    'nama_lengkap'     => $row['nama_lengkap'],
    'email'            => (string)($row['email'] ?? ''),
    'no_hp'            => (string)($row['no_hp'] ?? ''),
    'status_aktif'     => $row['status_aktif'],
    'password'         => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_pengguna();

        // Aturan 2: akun sendiri, role dan status dipaksa tetap (kiriman form diabaikan)
        if ($sendiri) {
            $d['id_role']      = $roleLama;
            $d['status_aktif'] = $statusLama;
        }

        $kodeRole = null;
        $errors = validasi_pengguna($koneksi, $d, false, $kodeRole);

        if (!$errors && username_dipakai($koneksi, $d['username'], $id)) {
            $errors[] = 'Username sudah dipakai pengguna lain.';
        }

        // Aturan 3: admin aktif terakhir tidak boleh turun role atau dinonaktifkan
        if (!$errors && $adalahAdminAktif) {
            $tetapAdminAktif = ($kodeRole === 'ADMIN' && $d['status_aktif'] === 'Aktif');
            if (!$tetapAdminAktif && jumlah_admin_aktif_lain($koneksi, $id) === 0) {
                $errors[] = 'Tidak bisa: ini satu-satunya admin aktif. Aplikasi harus punya minimal satu admin aktif.';
            }
        }

        if (!$errors) {
            $uni   = id_atau_null($d['id_universitas']);
            $fak   = id_atau_null($d['id_fakultas']);
            $prodi = id_atau_null($d['id_program_studi']);
            $email = $d['email'] === '' ? null : $d['email'];
            $hp    = $d['no_hp'] === '' ? null : $d['no_hp'];

            if ($d['password'] !== '') {
                $hash = password_hash($d['password'], PASSWORD_DEFAULT);
                $st = mysqli_prepare($koneksi,
                    "UPDATE pengguna SET id_role=?, id_universitas=?, id_fakultas=?, id_program_studi=?,
                            username=?, password_hash=?, nama_lengkap=?, email=?, no_hp=?, status_aktif=?
                     WHERE id_pengguna=?");
                mysqli_stmt_bind_param($st, 'iiiissssssi', $d['id_role'], $uni, $fak, $prodi, $d['username'],
                    $hash, $d['nama_lengkap'], $email, $hp, $d['status_aktif'], $id);
            } else {
                $st = mysqli_prepare($koneksi,
                    "UPDATE pengguna SET id_role=?, id_universitas=?, id_fakultas=?, id_program_studi=?,
                            username=?, nama_lengkap=?, email=?, no_hp=?, status_aktif=?
                     WHERE id_pengguna=?");
                mysqli_stmt_bind_param($st, 'iiiisssssi', $d['id_role'], $uni, $fak, $prodi, $d['username'],
                    $d['nama_lengkap'], $email, $hp, $d['status_aktif'], $id);
            }
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            // Jika admin mengubah nama sendiri, perbarui sesi agar topbar ikut berubah
            if ($sendiri) {
                $_SESSION['nama_lengkap'] = $d['nama_lengkap'];
                $_SESSION['username']     = $d['username'];
            }

            set_flash('success', 'Pengguna berhasil diubah.');
            header('Location: ' . BASE_URL . '/pages/pengguna.php');
            exit;
        }
    }
}

$judul      = 'Ubah Pengguna';
$menu_aktif = 'pengguna';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Ubah Pengguna</h4>
<p class="text-secondary"><a href="pengguna.php" class="text-decoration-none">Pengguna</a> &rsaquo; Ubah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/pengguna_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
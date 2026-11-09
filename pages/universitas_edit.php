<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/universitas_helper.php';
wajib_role(['ADMIN']);

$id = (int)($_GET['id'] ?? 0);

$st = mysqli_prepare($koneksi,
    "SELECT kode_universitas, nama_universitas, slogan, alamat, kota, provinsi, kode_pos, website, email, telepon
     FROM universitas WHERE id_universitas = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
mysqli_stmt_close($st);

if (!$row) {
    set_flash('danger', 'Universitas tidak ditemukan.');
    header('Location: ' . BASE_URL . '/pages/universitas.php');
    exit;
}

// Nilai NULL dari database diubah jadi string kosong untuk form
$d = array_map(fn($v) => (string)($v ?? ''), $row);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $d = ambil_input_universitas();
        $errors = validasi_universitas($d);

        if (!$errors) {
            $st = mysqli_prepare($koneksi,
                "SELECT COUNT(*) FROM universitas WHERE kode_universitas = ? AND id_universitas <> ?");
            mysqli_stmt_bind_param($st, 'si', $d['kode_universitas'], $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $jml);
            mysqli_stmt_fetch($st);
            mysqli_stmt_close($st);
            if ($jml > 0) $errors[] = 'Kode universitas sudah dipakai universitas lain.';
        }

        if (!$errors) {
            $slogan = atau_null($d['slogan']);   $alamat  = atau_null($d['alamat']);
            $kota   = atau_null($d['kota']);     $prov    = atau_null($d['provinsi']);
            $pos    = atau_null($d['kode_pos']); $web     = atau_null($d['website']);
            $email  = atau_null($d['email']);    $telp    = atau_null($d['telepon']);

            $st = mysqli_prepare($koneksi,
                "UPDATE universitas SET kode_universitas=?, nama_universitas=?, slogan=?, alamat=?, kota=?,
                        provinsi=?, kode_pos=?, website=?, email=?, telepon=?
                 WHERE id_universitas=?");
            mysqli_stmt_bind_param($st, 'ssssssssssi', $d['kode_universitas'], $d['nama_universitas'],
                $slogan, $alamat, $kota, $prov, $pos, $web, $email, $telp, $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Universitas berhasil diubah.');
            header('Location: ' . BASE_URL . '/pages/universitas.php');
            exit;
        }
    }
}

$judul      = 'Ubah Universitas';
$menu_aktif = 'universitas';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Ubah Universitas</h4>
<p class="text-secondary"><a href="universitas.php" class="text-decoration-none">Universitas</a> &rsaquo; Ubah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <?php include __DIR__ . '/../includes/universitas_form.php'; ?>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
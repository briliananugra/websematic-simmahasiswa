<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$kode = '';
$nama = '';
$ket  = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $kode = strtoupper(trim($_POST['kode_role'] ?? ''));
        $nama = trim($_POST['nama_role'] ?? '');
        $ket  = trim($_POST['keterangan'] ?? '');

        if (!preg_match('/^[A-Z0-9_]{2,30}$/', $kode)) {
            $errors[] = 'Kode role 2-30 karakter: huruf besar, angka, dan underscore saja.';
        }
        if ($nama === '' || mb_strlen($nama) > 100) {
            $errors[] = 'Nama role wajib diisi, maksimal 100 karakter.';
        }
        if (mb_strlen($ket) > 255) {
            $errors[] = 'Keterangan maksimal 255 karakter.';
        }

        // Cek kode sudah dipakai?
        if (!$errors) {
            $st = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM roles WHERE kode_role = ?");
            mysqli_stmt_bind_param($st, 's', $kode);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $jml);
            mysqli_stmt_fetch($st);
            mysqli_stmt_close($st);
            if ($jml > 0) $errors[] = 'Kode role sudah dipakai.';
        }

        if (!$errors) {
            $ketDb = $ket === '' ? null : $ket;
            $st = mysqli_prepare($koneksi,
                "INSERT INTO roles (kode_role, nama_role, keterangan) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($st, 'sss', $kode, $nama, $ketDb);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);

            set_flash('success', 'Role berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/pages/role.php');
            exit;
        }
    }
}

$judul      = 'Tambah Role';
$menu_aktif = 'role';
include __DIR__ . '/../includes/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Tambah Role</h4>
<p class="text-secondary"><a href="role.php" class="text-decoration-none">Role</a> &rsaquo; Tambah</p>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0">
    <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
  </ul></div>
<?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label fw-semibold">Kode Role</label>
      <input type="text" name="kode_role" class="form-control" maxlength="30" required
             value="<?= e($kode) ?>" placeholder="contoh: OPERATOR_FAKULTAS">
      <div class="form-text">Huruf besar, angka, underscore. Tidak bisa diubah setelah dibuat untuk role bawaan.</div>
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Nama Role</label>
      <input type="text" name="nama_role" class="form-control" maxlength="100" required value="<?= e($nama) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Keterangan (opsional)</label>
      <textarea name="keterangan" class="form-control" rows="3" maxlength="255"><?= e($ket) ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
      <a href="role.php" class="btn btn-outline-secondary">Batal</a>
    </div>
  </form>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
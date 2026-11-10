<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mahasiswa_helper.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI', 'DEKANAT', 'REKTORAT']);

$bisaKelola = bisa_kelola_mhs();

[$wScope, $tScope, $pScope] = scope_mhs();
$q = trim($_GET['q'] ?? '');

$where = [$wScope];
$tipe  = $tScope;
$param = $pScope;
if ($q !== '') {
    $where[] = "(m.npm LIKE ? OR m.nama_mahasiswa LIKE ?)";
    $like  = '%' . $q . '%';
    $tipe .= 'ss';
    array_push($param, $like, $like);
}

$sql = "SELECT m.id_mahasiswa, m.npm, m.nama_mahasiswa, m.jenis_kelamin, m.status_mahasiswa,
               ps.nama_program_studi, u.username
        FROM mahasiswa m
        JOIN program_studi ps ON ps.id_program_studi = m.id_program_studi
        LEFT JOIN pengguna u ON u.id_pengguna = m.id_pengguna
        WHERE " . implode(' AND ', $where) . "
        ORDER BY ps.nama_program_studi, m.npm";
$st = mysqli_prepare($koneksi, $sql);
if ($param) mysqli_stmt_bind_param($st, $tipe, ...$param);
mysqli_stmt_execute($st);
$data = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
mysqli_stmt_close($st);

$judul      = 'Mahasiswa';
$menu_aktif = 'mahasiswa';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Mahasiswa</h4>
    <p class="text-secondary mb-0">Data yang tampil sesuai cakupan role Anda</p>
  </div>
  <?php if ($bisaKelola): ?>
    <a href="mahasiswa_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Mahasiswa</a>
  <?php endif; ?>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <form method="get" class="row g-2 mb-3">
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="q" class="form-control" placeholder="Cari NPM atau nama..." value="<?= e($q) ?>">
      </div>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-primary">Cari</button>
      <a href="mahasiswa.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>No</th><th>NPM</th><th>Nama</th><th>JK</th><th>Program Studi</th><th>Status</th><th>Akun</th>
            <?php if ($bisaKelola): ?><th>Aksi</th><?php endif; ?></tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="8" class="text-center text-secondary py-4">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $m): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= e($m['npm']) ?></code></td>
            <td><?= e($m['nama_mahasiswa']) ?></td>
            <td><?= e($m['jenis_kelamin']) ?></td>
            <td><?= e($m['nama_program_studi']) ?></td>
            <td><span class="badge text-bg-<?= $m['status_mahasiswa'] === 'Aktif' ? 'success' : 'secondary' ?>">
                <?= e($m['status_mahasiswa']) ?></span></td>
            <td><?= e($m['username'] ?? '-') ?></td>
            <?php if ($bisaKelola): ?>
            <td class="text-nowrap">
              <a href="mahasiswa_edit.php?id=<?= (int)$m['id_mahasiswa'] ?>" class="btn btn-success btn-sm" title="Ubah">
                <i class="bi bi-pencil"></i></a>
              <form method="post" action="mahasiswa_hapus.php" class="d-inline"
                    onsubmit="return confirm('Yakin hapus data mahasiswa ini? Akun loginnya tidak ikut dihapus.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$m['id_mahasiswa'] ?>">
                <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
              </form>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
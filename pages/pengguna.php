<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$idSaya = (int)$_SESSION['id_pengguna'];

$data = [];
$q = mysqli_query($koneksi,
    "SELECT p.id_pengguna, p.username, p.nama_lengkap, p.status_aktif, p.last_login,
            r.nama_role, r.kode_role,
            f.nama_fakultas, ps.nama_program_studi
     FROM pengguna p
     JOIN roles r ON r.id_role = p.id_role
     LEFT JOIN fakultas f ON f.id_fakultas = p.id_fakultas
     LEFT JOIN program_studi ps ON ps.id_program_studi = p.id_program_studi
     ORDER BY r.id_role, p.username");
while ($row = mysqli_fetch_assoc($q)) { $data[] = $row; }

$judul      = 'Pengguna';
$menu_aktif = 'pengguna';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Pengguna</h4>
    <p class="text-secondary mb-0">Kelola akun, role, dan cakupan data</p>
  </div>
  <a href="pengguna_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pengguna</a>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>No</th><th>Username</th><th>Nama</th><th>Role</th><th>Cakupan</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="8" class="text-center text-secondary py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $p): ?>
          <?php
            $sendiri = (int)$p['id_pengguna'] === $idSaya;
            $cakupan = $p['nama_program_studi'] ?? $p['nama_fakultas'] ?? '-';
          ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td>
              <code><?= e($p['username']) ?></code>
              <?php if ($sendiri): ?><span class="badge text-bg-info">Anda</span><?php endif; ?>
            </td>
            <td><?= e($p['nama_lengkap']) ?></td>
            <td><?= e($p['nama_role']) ?></td>
            <td><?= e($cakupan) ?></td>
            <td>
              <span class="badge <?= $p['status_aktif'] === 'Aktif' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                <?= e($p['status_aktif']) ?>
              </span>
            </td>
            <td class="text-nowrap"><?= e($p['last_login'] ?? '-') ?></td>
            <td class="text-nowrap">
              <a href="pengguna_edit.php?id=<?= (int)$p['id_pengguna'] ?>" class="btn btn-success btn-sm" title="Ubah">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($sendiri): ?>
                <button class="btn btn-danger btn-sm" disabled title="Akun sendiri tidak bisa dihapus">
                  <i class="bi bi-trash"></i></button>
              <?php else: ?>
                <form method="post" action="pengguna_hapus.php" class="d-inline"
                      onsubmit="return confirm('Yakin hapus pengguna ini? Jika terhubung ke data mahasiswa, data mahasiswanya tetap ada tetapi tidak lagi punya akun login.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$p['id_pengguna'] ?>">
                  <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
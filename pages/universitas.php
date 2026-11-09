<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

$data = [];
$q = mysqli_query($koneksi,
    "SELECT u.id_universitas, u.kode_universitas, u.nama_universitas, u.kota,
            (SELECT COUNT(*) FROM fakultas f WHERE f.id_universitas = u.id_universitas) AS jml_fakultas,
            (SELECT COUNT(*) FROM pengguna p WHERE p.id_universitas = u.id_universitas) AS jml_pengguna
     FROM universitas u
     ORDER BY u.id_universitas");
while ($row = mysqli_fetch_assoc($q)) { $data[] = $row; }

$judul      = 'Universitas';
$menu_aktif = 'universitas';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Universitas</h4>
    <p class="text-secondary mb-0">Landing page memakai universitas pertama (id terkecil)</p>
  </div>
  <a href="universitas_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Universitas</a>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>No</th><th>Kode</th><th>Nama Universitas</th><th>Kota</th><th>Fakultas</th><th>Pengguna</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $u): ?>
          <?php $dipakai = ((int)$u['jml_fakultas'] + (int)$u['jml_pengguna']) > 0; ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= e($u['kode_universitas']) ?></code></td>
            <td><?= e($u['nama_universitas']) ?></td>
            <td><?= e($u['kota'] ?? '-') ?></td>
            <td><?= (int)$u['jml_fakultas'] ?></td>
            <td><?= (int)$u['jml_pengguna'] ?></td>
            <td class="text-nowrap">
              <a href="universitas_edit.php?id=<?= (int)$u['id_universitas'] ?>" class="btn btn-success btn-sm" title="Ubah">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($dipakai): ?>
                <button class="btn btn-danger btn-sm" disabled
                        title="Tidak bisa dihapus: masih punya fakultas/pengguna"><i class="bi bi-trash"></i></button>
              <?php else: ?>
                <form method="post" action="universitas_hapus.php" class="d-inline"
                      onsubmit="return confirm('Yakin hapus universitas ini?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$u['id_universitas'] ?>">
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
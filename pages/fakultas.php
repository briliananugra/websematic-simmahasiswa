<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI', 'DEKANAT', 'REKTORAT']);

$bisaKelola = $_SESSION['kode_role'] === 'ADMIN';

$data = [];
$q = mysqli_query($koneksi,
    "SELECT f.id_fakultas, f.kode_fakultas, f.nama_fakultas, u.nama_universitas,
            (SELECT COUNT(*) FROM program_studi ps WHERE ps.id_fakultas = f.id_fakultas) AS jml_prodi,
            (SELECT COUNT(*) FROM pengguna p WHERE p.id_fakultas = f.id_fakultas) AS jml_pengguna
     FROM fakultas f
     JOIN universitas u ON u.id_universitas = f.id_universitas
     ORDER BY u.nama_universitas, f.nama_fakultas");
while ($row = mysqli_fetch_assoc($q)) { $data[] = $row; }

$judul      = 'Fakultas';
$menu_aktif = 'fakultas';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Fakultas</h4>
    <p class="text-secondary mb-0">Fakultas pada universitas</p>
  </div>
  <?php if ($bisaKelola): ?>
    <a href="fakultas_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Fakultas</a>
  <?php endif; ?>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>No</th><th>Kode</th><th>Nama Fakultas</th><th>Universitas</th><th>Prodi</th><th>Pengguna</th>
            <?php if ($bisaKelola): ?><th>Aksi</th><?php endif; ?></tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $f): ?>
          <?php $dipakai = ((int)$f['jml_prodi'] + (int)$f['jml_pengguna']) > 0; ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= e($f['kode_fakultas']) ?></code></td>
            <td><?= e($f['nama_fakultas']) ?></td>
            <td><?= e($f['nama_universitas']) ?></td>
            <td><?= (int)$f['jml_prodi'] ?></td>
            <td><?= (int)$f['jml_pengguna'] ?></td>
            <?php if ($bisaKelola): ?>
            <td class="text-nowrap">
              <a href="fakultas_edit.php?id=<?= (int)$f['id_fakultas'] ?>" class="btn btn-success btn-sm" title="Ubah">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($dipakai): ?>
                <button class="btn btn-danger btn-sm" disabled
                        title="Tidak bisa dihapus: masih punya prodi/pengguna"><i class="bi bi-trash"></i></button>
              <?php else: ?>
                <form method="post" action="fakultas_hapus.php" class="d-inline"
                      onsubmit="return confirm('Yakin hapus fakultas ini?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$f['id_fakultas'] ?>">
                  <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              <?php endif; ?>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
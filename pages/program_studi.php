<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI', 'DEKANAT', 'REKTORAT']);

$bisaKelola = $_SESSION['kode_role'] === 'ADMIN';

$data = [];
$q = mysqli_query($koneksi,
    "SELECT ps.id_program_studi, ps.kode_program_studi, ps.nama_program_studi, ps.jenjang, ps.status_aktif,
            f.nama_fakultas,
            (SELECT COUNT(*) FROM mahasiswa m WHERE m.id_program_studi = ps.id_program_studi) AS jml_mhs,
            (SELECT COUNT(*) FROM pengguna p WHERE p.id_program_studi = ps.id_program_studi) AS jml_pengguna
     FROM program_studi ps
     JOIN fakultas f ON f.id_fakultas = ps.id_fakultas
     ORDER BY f.nama_fakultas, ps.nama_program_studi");
while ($row = mysqli_fetch_assoc($q)) { $data[] = $row; }

$judul      = 'Program Studi';
$menu_aktif = 'program_studi';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Program Studi</h4>
    <p class="text-secondary mb-0">Program studi pada fakultas</p>
  </div>
  <?php if ($bisaKelola): ?>
    <a href="program_studi_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Program Studi</a>
  <?php endif; ?>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm"><div class="card-body">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>No</th><th>Kode</th><th>Nama Program Studi</th><th>Fakultas</th><th>Jenjang</th><th>Status</th>
            <th>Mahasiswa</th><th>Pengguna</th>
            <?php if ($bisaKelola): ?><th>Aksi</th><?php endif; ?></tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="9" class="text-center text-secondary py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $p): ?>
          <?php $dipakai = ((int)$p['jml_mhs'] + (int)$p['jml_pengguna']) > 0; ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= e($p['kode_program_studi']) ?></code></td>
            <td><?= e($p['nama_program_studi']) ?></td>
            <td><?= e($p['nama_fakultas']) ?></td>
            <td><?= e($p['jenjang']) ?></td>
            <td>
              <span class="badge <?= $p['status_aktif'] === 'Aktif' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                <?= e($p['status_aktif']) ?>
              </span>
            </td>
            <td><?= (int)$p['jml_mhs'] ?></td>
            <td><?= (int)$p['jml_pengguna'] ?></td>
            <?php if ($bisaKelola): ?>
            <td class="text-nowrap">
              <a href="program_studi_edit.php?id=<?= (int)$p['id_program_studi'] ?>" class="btn btn-success btn-sm" title="Ubah">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($dipakai): ?>
                <button class="btn btn-danger btn-sm" disabled
                        title="Tidak bisa dihapus: masih punya mahasiswa/pengguna"><i class="bi bi-trash"></i></button>
              <?php else: ?>
                <form method="post" action="program_studi_hapus.php" class="d-inline"
                      onsubmit="return confirm('Yakin hapus program studi ini?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$p['id_program_studi'] ?>">
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
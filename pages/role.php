<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN']);

// Hitung jumlah pengguna per role (untuk info dan penanda "dipakai")
$data = [];
$q = mysqli_query($koneksi,
    "SELECT r.id_role, r.kode_role, r.nama_role, r.keterangan,
            COUNT(p.id_pengguna) AS jml_pengguna
     FROM roles r
     LEFT JOIN pengguna p ON p.id_role = r.id_role
     GROUP BY r.id_role, r.kode_role, r.nama_role, r.keterangan
     ORDER BY r.id_role");
while ($row = mysqli_fetch_assoc($q)) { $data[] = $row; }

$judul      = 'Role';
$menu_aktif = 'role';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Data Role</h4>
    <p class="text-secondary mb-0">Kelola peran pengguna aplikasi</p>
  </div>
  <a href="role_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Role</a>
</div>

<?= tampil_flash() ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr><th>No</th><th>Kode Role</th><th>Nama Role</th><th>Keterangan</th><th>Pengguna</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (!$data): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr>
          <?php endif; ?>
          <?php foreach ($data as $i => $r): ?>
            <?php $bawaan = (int)$r['id_role'] <= 5; ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td>
                <code><?= e($r['kode_role']) ?></code>
                <?php if ($bawaan): ?><span class="badge text-bg-secondary">bawaan</span><?php endif; ?>
              </td>
              <td><?= e($r['nama_role']) ?></td>
              <td><?= e($r['keterangan'] ?? '-') ?></td>
              <td><?= (int)$r['jml_pengguna'] ?></td>
              <td class="text-nowrap">
                <a href="role_edit.php?id=<?= (int)$r['id_role'] ?>" class="btn btn-success btn-sm" title="Ubah">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php if (!$bawaan): ?>
                  <form method="post" action="role_hapus.php" class="d-inline"
                        onsubmit="return confirm('Yakin hapus role ini?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$r['id_role'] ?>">
                    <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
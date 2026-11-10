<?php
/** @var array $d isi form */
/** @var array $listRole */
/** @var array $listUniv */
/** @var array $listFak */
/** @var array $listProdi */
/** @var bool $edit */
/** @var bool $kunciAkunSendiri true jika admin mengedit akunnya sendiri */
?>
<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label fw-semibold">Username</label>
      <input type="text" name="username" class="form-control" maxlength="100" required
             value="<?= e($d['username']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" class="form-control" maxlength="200" required
             value="<?= e($d['nama_lengkap']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Password<?= $edit ? ' (kosongkan jika tidak diubah)' : '' ?></label>
      <input type="password" name="password" class="form-control" autocomplete="new-password"
             <?= $edit ? '' : 'required' ?> placeholder="minimal 8 karakter">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">Role</label>
      <?php if ($kunciAkunSendiri): ?>
        <input type="hidden" name="id_role" value="<?= (int)$d['id_role'] ?>">
      <?php endif; ?>
      <select name="<?= $kunciAkunSendiri ? '' : 'id_role' ?>" class="form-select" required
              <?= $kunciAkunSendiri ? 'disabled' : '' ?>>
        <option value="">-- Pilih role --</option>
        <?php foreach ($listRole as $r): ?>
          <option value="<?= (int)$r['id_role'] ?>"
            <?= (int)$d['id_role'] === (int)$r['id_role'] ? 'selected' : '' ?>><?= e($r['nama_role']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($kunciAkunSendiri): ?>
        <div class="form-text">Role akun sendiri tidak bisa diubah.</div>
      <?php endif; ?>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">Status</label>
      <?php if ($kunciAkunSendiri): ?>
        <input type="hidden" name="status_aktif" value="Aktif">
      <?php endif; ?>
      <select name="<?= $kunciAkunSendiri ? '' : 'status_aktif' ?>" class="form-select" required
              <?= $kunciAkunSendiri ? 'disabled' : '' ?>>
        <?php foreach (['Aktif', 'Tidak Aktif'] as $s): ?>
          <option value="<?= e($s) ?>" <?= $d['status_aktif'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Email (opsional)</label>
      <input type="text" name="email" class="form-control" maxlength="150" value="<?= e($d['email']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">No HP (opsional)</label>
      <input type="text" name="no_hp" class="form-control" maxlength="30" value="<?= e($d['no_hp']) ?>">
    </div>
  </div>

  <hr class="my-4">
  <h6 class="fw-bold">Cakupan data</h6>
  <p class="text-secondary small">
    Isi sesuai role: <b>Rektorat</b> pilih universitas, <b>Dekanat</b> pilih fakultas,
    <b>Operator Prodi</b> dan <b>Mahasiswa</b> pilih program studi. Pilihan yang tidak relevan
    dikosongkan otomatis, dan cakupan di atasnya diisi otomatis.
  </p>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">Universitas</label>
      <select name="id_universitas" class="form-select">
        <option value="0">-- Tidak ada --</option>
        <?php foreach ($listUniv as $u): ?>
          <option value="<?= (int)$u['id_universitas'] ?>"
            <?= (int)$d['id_universitas'] === (int)$u['id_universitas'] ? 'selected' : '' ?>><?= e($u['nama_universitas']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Fakultas</label>
      <select name="id_fakultas" class="form-select">
        <option value="0">-- Tidak ada --</option>
        <?php foreach ($listFak as $f): ?>
          <option value="<?= (int)$f['id_fakultas'] ?>"
            <?= (int)$d['id_fakultas'] === (int)$f['id_fakultas'] ? 'selected' : '' ?>><?= e($f['nama_fakultas']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Program Studi</label>
      <select name="id_program_studi" class="form-select">
        <option value="0">-- Tidak ada --</option>
        <?php foreach ($listProdi as $p): ?>
          <option value="<?= (int)$p['id_program_studi'] ?>"
            <?= (int)$d['id_program_studi'] === (int)$p['id_program_studi'] ? 'selected' : '' ?>><?= e($p['nama_program_studi']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
    <a href="pengguna.php" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
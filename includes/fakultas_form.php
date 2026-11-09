<?php
/** @var array $d isi form */
/** @var array $listUniv daftar universitas untuk dropdown */
?>
<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label fw-semibold">Universitas</label>
    <select name="id_universitas" class="form-select" required>
      <option value="">-- Pilih universitas --</option>
      <?php foreach ($listUniv as $u): ?>
        <option value="<?= (int)$u['id_universitas'] ?>"
          <?= (int)$d['id_universitas'] === (int)$u['id_universitas'] ? 'selected' : '' ?>>
          <?= e($u['nama_universitas']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">Kode Fakultas</label>
      <input type="text" name="kode_fakultas" class="form-control" maxlength="20" required
             value="<?= e($d['kode_fakultas']) ?>" placeholder="contoh: FT">
    </div>
    <div class="col-md-8">
      <label class="form-label fw-semibold">Nama Fakultas</label>
      <input type="text" name="nama_fakultas" class="form-control" maxlength="200" required
             value="<?= e($d['nama_fakultas']) ?>">
    </div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
    <a href="fakultas.php" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
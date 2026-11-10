<?php
/** @var array $d isi form */
/** @var array $listFak daftar fakultas untuk dropdown */
?>
<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label fw-semibold">Fakultas</label>
    <select name="id_fakultas" class="form-select" required>
      <option value="">-- Pilih fakultas --</option>
      <?php foreach ($listFak as $f): ?>
        <option value="<?= (int)$f['id_fakultas'] ?>"
          <?= (int)$d['id_fakultas'] === (int)$f['id_fakultas'] ? 'selected' : '' ?>>
          <?= e($f['nama_fakultas']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">Kode Program Studi</label>
      <input type="text" name="kode_program_studi" class="form-control" maxlength="20" required
             value="<?= e($d['kode_program_studi']) ?>" placeholder="contoh: TI">
    </div>
    <div class="col-md-8">
      <label class="form-label fw-semibold">Nama Program Studi</label>
      <input type="text" name="nama_program_studi" class="form-control" maxlength="200" required
             value="<?= e($d['nama_program_studi']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Jenjang</label>
      <select name="jenjang" class="form-select" required>
        <?php foreach (daftar_jenjang() as $j): ?>
          <option value="<?= e($j) ?>" <?= $d['jenjang'] === $j ? 'selected' : '' ?>><?= e($j) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Status</label>
      <select name="status_aktif" class="form-select" required>
        <?php foreach (['Aktif', 'Tidak Aktif'] as $s): ?>
          <option value="<?= e($s) ?>" <?= $d['status_aktif'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
    <a href="program_studi.php" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
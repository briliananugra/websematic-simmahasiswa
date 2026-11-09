<?php
/** @var array $d isi form, disiapkan oleh universitas_tambah.php / universitas_edit.php */
?>

<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">Kode Universitas</label>
      <input type="text" name="kode_universitas" class="form-control" maxlength="20" required
             value="<?= e($d['kode_universitas']) ?>" placeholder="contoh: UMB">
    </div>
    <div class="col-md-8">
      <label class="form-label fw-semibold">Nama Universitas</label>
      <input type="text" name="nama_universitas" class="form-control" maxlength="200" required
             value="<?= e($d['nama_universitas']) ?>">
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold">Slogan</label>
      <input type="text" name="slogan" class="form-control" maxlength="255" value="<?= e($d['slogan']) ?>">
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold">Alamat</label>
      <textarea name="alamat" class="form-control" rows="2"><?= e($d['alamat']) ?></textarea>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Kota</label>
      <input type="text" name="kota" class="form-control" maxlength="100" value="<?= e($d['kota']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Provinsi</label>
      <input type="text" name="provinsi" class="form-control" maxlength="100" value="<?= e($d['provinsi']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Kode Pos</label>
      <input type="text" name="kode_pos" class="form-control" maxlength="10" value="<?= e($d['kode_pos']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Website</label>
      <input type="text" name="website" class="form-control" maxlength="255"
             value="<?= e($d['website']) ?>" placeholder="https://...">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Email</label>
      <input type="text" name="email" class="form-control" maxlength="150" value="<?= e($d['email']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Telepon</label>
      <input type="text" name="telepon" class="form-control" maxlength="50" value="<?= e($d['telepon']) ?>">
    </div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
    <a href="universitas.php" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
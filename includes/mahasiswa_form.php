<?php
/** @var array $d isi form */
/** @var array $listProdi */
/** @var array $listAkun */
?>
<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">NPM</label>
      <input type="text" name="npm" class="form-control" maxlength="30" required value="<?= e($d['npm']) ?>">
    </div>
    <div class="col-md-8">
      <label class="form-label fw-semibold">Nama Mahasiswa</label>
      <input type="text" name="nama_mahasiswa" class="form-control" maxlength="200" required
             value="<?= e($d['nama_mahasiswa']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Program Studi</label>
      <select name="id_program_studi" class="form-select" required>
        <option value="">-- Pilih program studi --</option>
        <?php foreach ($listProdi as $p): ?>
          <option value="<?= (int)$p['id_program_studi'] ?>"
            <?= (int)$d['id_program_studi'] === (int)$p['id_program_studi'] ? 'selected' : '' ?>>
            <?= e($p['nama_program_studi']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">Jenis Kelamin</label>
      <select name="jenis_kelamin" class="form-select" required>
        <option value="">-- Pilih --</option>
        <option value="L" <?= $d['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
        <option value="P" <?= $d['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">Status</label>
      <select name="status_mahasiswa" class="form-select" required>
        <?php foreach (daftar_status_mhs() as $s): ?>
          <option value="<?= e($s) ?>" <?= $d['status_mahasiswa'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Tempat Lahir</label>
      <input type="text" name="tempat_lahir" class="form-control" maxlength="100" value="<?= e($d['tempat_lahir']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" class="form-control" value="<?= e($d['tanggal_lahir']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">Tanggal Masuk</label>
      <input type="date" name="tanggal_masuk" class="form-control" required value="<?= e($d['tanggal_masuk']) ?>">
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold">Alamat</label>
      <textarea name="alamat" class="form-control" rows="2"><?= e($d['alamat']) ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">Akun Login (opsional)</label>
      <select name="id_pengguna" class="form-select">
        <option value="0">-- Tanpa akun --</option>
        <?php foreach ($listAkun as $a): ?>
          <option value="<?= (int)$a['id_pengguna'] ?>"
            <?= (int)$d['id_pengguna'] === (int)$a['id_pengguna'] ? 'selected' : '' ?>>
            <?= e($a['username'] . ' - ' . $a['nama_lengkap']) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="form-text">Hanya akun ber-role Mahasiswa yang belum terhubung.</div>
    </div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
    <a href="mahasiswa.php" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
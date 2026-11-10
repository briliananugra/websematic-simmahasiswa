<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
wajib_role(['ADMIN', 'OPERATOR_PRODI', 'DEKANAT', 'REKTORAT']);

$role = $_SESSION['kode_role'];

// 1. Daftar prodi yang boleh dipilih sesuai cakupan role
$sqlProdi = "SELECT ps.id_program_studi, ps.nama_program_studi, f.nama_fakultas, u.nama_universitas
             FROM program_studi ps
             JOIN fakultas f   ON f.id_fakultas = ps.id_fakultas
             JOIN universitas u ON u.id_universitas = f.id_universitas";
$tipe = ''; $param = [];
if ($role === 'OPERATOR_PRODI') {
    $sqlProdi .= " WHERE ps.id_program_studi = ?";
    $tipe = 'i'; $param[] = (int)($_SESSION['id_program_studi'] ?? 0);
} elseif ($role === 'DEKANAT') {
    $sqlProdi .= " WHERE ps.id_fakultas = ?";
    $tipe = 'i'; $param[] = (int)($_SESSION['id_fakultas'] ?? 0);
}
$sqlProdi .= " ORDER BY f.nama_fakultas, ps.nama_program_studi";

$st = mysqli_prepare($koneksi, $sqlProdi);
if ($param) mysqli_stmt_bind_param($st, $tipe, ...$param);
mysqli_stmt_execute($st);
$listProdi = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
mysqli_stmt_close($st);

// 2. Prodi terpilih: harus ada di daftar di atas (kalau tidak, pakai yang pertama)
$idPilih = (int)($_GET['prodi'] ?? 0);
$prodi = null;
foreach ($listProdi as $p) {
    if ((int)$p['id_program_studi'] === $idPilih) { $prodi = $p; break; }
}
if (!$prodi && $listProdi) { $prodi = $listProdi[0]; }

// 3. Data mahasiswa prodi terpilih
$data = [];
if ($prodi) {
    $idProdi = (int)$prodi['id_program_studi'];
    $st = mysqli_prepare($koneksi,
        "SELECT m.npm, m.nama_mahasiswa, m.jenis_kelamin, m.tempat_lahir, m.tanggal_lahir,
                m.tanggal_masuk, m.alamat, m.id_pengguna
         FROM mahasiswa m
         WHERE m.id_program_studi = ?
         ORDER BY m.npm");
    mysqli_stmt_bind_param($st, 'i', $idProdi);
    mysqli_stmt_execute($st);
    $data = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
    mysqli_stmt_close($st);
}

$fmtTgl = fn($t) => $t ? date('d-m-Y', strtotime($t)) : '-';

$judul      = 'Laporan';
$menu_aktif = 'laporan';
include __DIR__ . '/../includes/layout_atas.php';
?>

<div class="no-print d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="fw-bold mb-1">Laporan Mahasiswa</h4>
    <p class="text-secondary mb-0">Pilih program studi untuk menampilkan laporan</p>
  </div>
  <button type="button" class="btn btn-outline-primary" onclick="window.print()">
    <i class="bi bi-printer"></i> Cetak
  </button>
</div>

<form method="get" class="no-print row g-2 mb-3">
  <div class="col-md-6">
    <select name="prodi" class="form-select">
      <?php foreach ($listProdi as $p): ?>
        <option value="<?= (int)$p['id_program_studi'] ?>"
          <?= $prodi && (int)$p['id_program_studi'] === (int)$prodi['id_program_studi'] ? 'selected' : '' ?>>
          <?= e($p['nama_program_studi'] . ' - ' . $p['nama_fakultas']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3"><button class="btn btn-primary">Tampilkan</button></div>
</form>

<?php if (!$prodi): ?>
  <div class="alert alert-info">Belum ada program studi dalam cakupan Anda.</div>
<?php else: ?>
<div class="card border-0 shadow-sm"><div class="card-body">
  <h5 class="fw-bold mb-0">Daftar Mahasiswa Per Program Studi</h5>
  <div class="mb-3"><?= e($prodi['nama_universitas']) ?></div>
  <div>Program Studi : <?= e($prodi['nama_program_studi']) ?></div>
  <div class="mb-3">Fakultas : <?= e($prodi['nama_fakultas']) ?></div>

  <div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th>No.</th><th>NPM</th><th>Nama Mahasiswa</th><th>Jenis Kelamin</th><th>Tempat Lahir</th>
          <th>Tanggal Lahir</th><th>Tanggal Masuk</th><th>Alamat</th><th>Password</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr><td colspan="9" class="text-center text-secondary py-4">Belum ada mahasiswa di program studi ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $m): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e($m['npm']) ?></td>
            <td><?= e($m['nama_mahasiswa']) ?></td>
            <td><?= $m['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            <td><?= e($m['tempat_lahir'] ?? '-') ?></td>
            <td><?= e($fmtTgl($m['tanggal_lahir'])) ?></td>
            <td><?= e($fmtTgl($m['tanggal_masuk'])) ?></td>
            <td><?= e($m['alamat'] ?? '-') ?></td>
            <td><?= $m['id_pengguna'] ? '********' : '-' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="text-secondary small mt-2">Total: <?= count($data) ?> mahasiswa</div>
</div></div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/layout_bawah.php'; ?>
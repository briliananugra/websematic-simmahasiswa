<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/helper.php';

// Data universitas
$r = mysqli_query($koneksi, "SELECT * FROM universitas ORDER BY id_universitas LIMIT 1");
$univ = $r ? mysqli_fetch_assoc($r) : [];
$nama_univ = $univ['nama_universitas'] ?? 'Universitas';
$slogan    = !empty($univ['slogan']) ? $univ['slogan'] : 'Unggul · Islam · Berkemajuan';
$alamat    = !empty($univ['alamat']) ? $univ['alamat'] : '-';
$kota      = $univ['kota'] ?? '';
$email     = !empty($univ['email']) ? $univ['email'] : '-';
$telepon   = !empty($univ['telepon']) ? $univ['telepon'] : '-';

// Statistik
$total_mhs      = hitung($koneksi, "SELECT COUNT(*) FROM mahasiswa");
$total_prodi    = hitung($koneksi, "SELECT COUNT(*) FROM program_studi");
$total_fakultas = hitung($koneksi, "SELECT COUNT(*) FROM fakultas");

// Daftar prodi + jumlah mahasiswa
$prodi = [];
$q = mysqli_query($koneksi,
    "SELECT p.id_program_studi, p.nama_program_studi, COUNT(m.id_mahasiswa) AS jumlah
     FROM program_studi p
     LEFT JOIN mahasiswa m ON m.id_program_studi = p.id_program_studi
     GROUP BY p.id_program_studi, p.nama_program_studi
     ORDER BY p.nama_program_studi
     LIMIT 8");
if ($q) { while ($row = mysqli_fetch_assoc($q)) { $prodi[] = $row; } }

$ikon = ['bi-pc-display','bi-bar-chart-fill','bi-book-fill','bi-heart-pulse','bi-people-fill','bi-flower1','bi-gear-fill','bi-three-dots'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIM Mahasiswa - <?= e($nama_univ) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <i class="bi bi-mortarboard-fill brand-icon"></i>
      <span>
        <strong class="d-block lh-1"><?= e($nama_univ) ?></strong>
        <small class="text-muted"><?= e($slogan) ?></small>
      </span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="#beranda">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#prodi">Data Mahasiswa</a></li>
        <li class="nav-item"><a class="nav-link" href="#prodi">Program Studi</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Web Semantik</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
      </ul>
      <a href="login.php" class="btn btn-primary px-4"><i class="bi bi-person-fill me-1"></i> Login</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<header id="beranda" class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1 class="hero-title">Data Mahasiswa<br><span>Per Program Studi</span></h1>
        <p class="lead text-secondary mt-3">
          Akses, eksplorasi, dan manfaatkan data mahasiswa secara terbuka,
          terstruktur, dan terhubung untuk mendukung tata kelola universitas yang lebih baik.
        </p>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="#prodi" class="btn btn-primary btn-lg"><i class="bi bi-search me-2"></i>Lihat Data Mahasiswa</a>
          <a href="#tentang" class="btn btn-outline-primary btn-lg"><i class="bi bi-book me-2"></i>Pelajari Web Semantik</a>
        </div>
        <p class="fst-italic text-secondary mt-4">"Data yang terhubung, pengetahuan yang lebih luas"</p>
      </div>
      <div class="col-lg-5 d-none d-lg-block">
        <div class="hero-card">
          <div class="hero-card-title">Semantic Web<br>for a Smarter University</div>
          <div class="hero-tags">
            <span>RDF</span><span>OWL</span><span>SPARQL</span><span>LINKED DATA</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- 4 FITUR -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-md-6 col-lg-3">
        <div class="fitur-ikon"><i class="bi bi-database-fill"></i></div>
        <h6 class="fw-bold">Data Terintegrasi</h6>
        <p class="small text-secondary">Data mahasiswa terhubung dengan program studi, fakultas, dan informasi akademik lainnya.</p>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="fitur-ikon"><i class="bi bi-share-fill"></i></div>
        <h6 class="fw-bold">Standar Terbuka</h6>
        <p class="small text-secondary">Menggunakan teknologi Web Semantik (RDF, OWL, SPARQL) untuk interoperabilitas data.</p>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="fitur-ikon"><i class="bi bi-bar-chart-fill"></i></div>
        <h6 class="fw-bold">Mudah Diakses</h6>
        <p class="small text-secondary">Pencarian dan visualisasi data yang cepat dan interaktif.</p>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="fitur-ikon"><i class="bi bi-shield-fill-check"></i></div>
        <h6 class="fw-bold">Mendukung Transparansi</h6>
        <p class="small text-secondary">Data yang terbuka untuk mendukung akreditasi, riset, dan pengambilan keputusan.</p>
      </div>
    </div>
  </div>
</section>

<!-- DAFTAR PRODI -->
<section id="prodi" class="py-5 bg-soft">
  <div class="container">
    <h2 class="fw-bold mb-1">Daftar Program Studi</h2>
    <p class="text-secondary mb-4">Pilih program studi untuk melihat data mahasiswa secara detail</p>
    <div class="row g-3">
      <?php if (empty($prodi)): ?>
        <div class="col-12"><div class="alert alert-info">Belum ada data program studi.</div></div>
      <?php endif; ?>
      <?php foreach ($prodi as $i => $p): ?>
      <div class="col-sm-6 col-lg-3">
        <a href="login.php" class="prodi-card">
          <span class="prodi-ikon ic-<?= $i % 8 ?>"><i class="bi <?= $ikon[$i % 8] ?>"></i></span>
          <span class="flex-grow-1">
            <strong class="d-block"><?= e($p['nama_program_studi']) ?></strong>
            <small class="text-secondary"><?= (int)$p['jumlah'] ?> Mahasiswa</small>
          </span>
          <i class="bi bi-arrow-right text-primary"></i>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- STATISTIK -->
<section class="stat-band">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-6 col-md-3">
        <i class="bi bi-mortarboard-fill"></i>
        <div class="stat-angka"><?= number_format($total_mhs, 0, ',', '.') ?></div>
        <div>Total Mahasiswa</div>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-people-fill"></i>
        <div class="stat-angka"><?= number_format($total_prodi, 0, ',', '.') ?></div>
        <div>Program Studi</div>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-bank2"></i>
        <div class="stat-angka"><?= number_format($total_fakultas, 0, ',', '.') ?></div>
        <div>Fakultas</div>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-globe2"></i>
        <div class="fw-semibold mt-2">Data Terhubung<br>dengan Web Semantik</div>
      </div>
    </div>
  </div>
</section>

<!-- TENTANG -->
<section id="tentang" class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h3 class="fw-bold text-primary-dark">Membangun Ekosistem Data Terbuka</h3>
        <p class="text-secondary">
          Dengan pendekatan <strong>Web Semantik</strong>, data mahasiswa tidak hanya disimpan,
          tetapi juga dapat dipahami, dihubungkan, dan dimanfaatkan oleh berbagai aplikasi
          untuk mendukung pendidikan, penelitian, dan inovasi.
        </p>
        <a href="#" class="btn btn-primary"><i class="bi bi-book me-2"></i>Tentang Web Semantik</a>
      </div>
      <div class="col-lg-5">
        <blockquote class="kutipan">
          "Web Semantik memungkinkan data di universitas tidak hanya dilihat oleh manusia,
          tetapi juga dipahami oleh mesin."
          <footer>- Menuju Universitas yang Lebih Cerdas</footer>
        </blockquote>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer id="kontak" class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="bi bi-mortarboard-fill brand-icon text-white"></i>
          <strong class="fs-5"><?= e($nama_univ) ?></strong>
        </div>
        <small><?= e($slogan) ?></small>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Tautan Cepat</h6>
        <ul class="list-unstyled small">
          <li><a href="#beranda">Beranda</a></li>
          <li><a href="#prodi">Program Studi</a></li>
          <li><a href="#tentang">Tentang</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Sumber Daya</h6>
        <ul class="list-unstyled small">
          <li>RDF</li><li>OWL</li><li>SPARQL</li>
        </ul>
      </div>
      <div class="col-lg-4">
        <h6>Kontak</h6>
        <ul class="list-unstyled small">
          <li><i class="bi bi-geo-alt-fill me-2"></i><?= e($alamat) ?><?= $kota ? ', ' . e($kota) : '' ?></li>
          <li><i class="bi bi-envelope-fill me-2"></i><?= e($email) ?></li>
          <li><i class="bi bi-telephone-fill me-2"></i><?= e($telepon) ?></li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary">
    <small>&copy; <?= date('Y') ?> <?= e($nama_univ) ?>. All rights reserved.</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
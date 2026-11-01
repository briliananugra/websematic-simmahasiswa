<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
wajib_login();

function e($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function hitung($koneksi, $sql) {
    $r = mysqli_query($koneksi, $sql);
    return $r ? (int) mysqli_fetch_row($r)[0] : 0;
}

$total_mhs      = hitung($koneksi, "SELECT COUNT(*) FROM mahasiswa");
$total_prodi    = hitung($koneksi, "SELECT COUNT(*) FROM program_studi");
$total_fakultas = hitung($koneksi, "SELECT COUNT(*) FROM fakultas");
$total_pengguna = hitung($koneksi, "SELECT COUNT(*) FROM pengguna");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - SIM Mahasiswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body class="bg-soft">

<nav class="navbar bg-white shadow-sm px-3">
  <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
    <i class="bi bi-mortarboard-fill brand-icon"></i><strong>SIM Mahasiswa</strong>
  </a>
  <div class="d-flex align-items-center gap-3">
    <span class="small text-end">
      <strong class="d-block"><?= e($_SESSION['nama_lengkap']) ?></strong>
      <span class="text-secondary"><?= e($_SESSION['nama_role']) ?></span>
    </span>
    <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>

<div class="app-wrap">
  <aside class="app-side">
    <a class="side-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="side-link disabled" href="#"><i class="bi bi-bank"></i> Universitas</a>
    <a class="side-link disabled" href="#"><i class="bi bi-building"></i> Fakultas</a>
    <a class="side-link disabled" href="#"><i class="bi bi-diagram-3"></i> Program Studi</a>
    <a class="side-link disabled" href="#"><i class="bi bi-people"></i> Mahasiswa</a>
    <a class="side-link disabled" href="#"><i class="bi bi-person-gear"></i> Pengguna</a>
    <a class="side-link disabled" href="#"><i class="bi bi-shield-lock"></i> Role</a>
  </aside>

  <main class="app-main">
    <h4 class="fw-bold mb-1">Dashboard</h4>
    <p class="text-secondary">Selamat datang, <?= e($_SESSION['nama_lengkap']) ?>.</p>

    <div class="row g-3">
      <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-mortarboard-fill"></i><div><div class="angka"><?= $total_mhs ?></div>Mahasiswa</div></div></div>
      <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-diagram-3-fill"></i><div><div class="angka"><?= $total_prodi ?></div>Program Studi</div></div></div>
      <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-building-fill"></i><div><div class="angka"><?= $total_fakultas ?></div>Fakultas</div></div></div>
      <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-person-fill-gear"></i><div><div class="angka"><?= $total_pengguna ?></div>Pengguna</div></div></div>
    </div>
  </main>
</div>

</body>
</html>
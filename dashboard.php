<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
wajib_login();

$total_mhs      = hitung($koneksi, "SELECT COUNT(*) FROM mahasiswa");
$total_prodi    = hitung($koneksi, "SELECT COUNT(*) FROM program_studi");
$total_fakultas = hitung($koneksi, "SELECT COUNT(*) FROM fakultas");
$total_pengguna = hitung($koneksi, "SELECT COUNT(*) FROM pengguna");

$judul      = 'Dashboard';
$menu_aktif = 'dashboard';
include __DIR__ . '/layout_atas.php';
?>

<h4 class="fw-bold mb-1">Dashboard</h4>
<p class="text-secondary">Selamat datang, <?= e($_SESSION['nama_lengkap']) ?>.</p>

<?= tampil_flash() ?>

<div class="row g-3">
  <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-mortarboard-fill"></i><div><div class="angka"><?= $total_mhs ?></div>Mahasiswa</div></div></div>
  <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-diagram-3-fill"></i><div><div class="angka"><?= $total_prodi ?></div>Program Studi</div></div></div>
  <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-building-fill"></i><div><div class="angka"><?= $total_fakultas ?></div>Fakultas</div></div></div>
  <div class="col-6 col-lg-3"><div class="kartu-stat"><i class="bi bi-person-fill-gear"></i><div><div class="angka"><?= $total_pengguna ?></div>Pengguna</div></div></div>
</div>

<?php include __DIR__ . '/layout_bawah.php'; ?>
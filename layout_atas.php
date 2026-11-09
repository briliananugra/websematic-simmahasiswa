<?php
// Halaman yang memakai file ini harus mengisi dulu: $judul dan $menu_aktif
$judul      = $judul      ?? 'SIM Mahasiswa';
$menu_aktif = $menu_aktif ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?> - SIM Mahasiswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body class="bg-soft">

<?php include __DIR__ . '/topbar.php'; ?>

<div class="app-wrap">
  <?php include __DIR__ . '/sideleftbar.php'; ?>
  <main class="app-main">
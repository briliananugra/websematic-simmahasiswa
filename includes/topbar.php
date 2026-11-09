<nav class="navbar bg-white shadow-sm px-3">
  <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/pages/dashboard.php">
    <i class="bi bi-mortarboard-fill brand-icon"></i><strong>SIM Mahasiswa</strong>
  </a>
  <div class="d-flex align-items-center gap-3">
    <span class="small text-end">
      <strong class="d-block"><?= e($_SESSION['nama_lengkap']) ?></strong>
      <span class="text-secondary"><?= e($_SESSION['nama_role']) ?></span>
    </span>
    <a href="<?= BASE_URL ?>/auth/logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>
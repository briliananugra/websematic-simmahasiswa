<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

if (sudah_login()) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf_token'] ?? '')) {
        $error = 'Sesi form tidak valid. Muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = mysqli_prepare($koneksi,
            "SELECT p.id_pengguna, p.username, p.password_hash, p.nama_lengkap,
                    p.id_fakultas, p.id_program_studi, p.status_aktif,
                    r.kode_role, r.nama_role
             FROM pengguna p
             JOIN roles r ON r.id_role = p.id_role
             WHERE p.username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($user && $user['status_aktif'] === 'Aktif'
            && password_verify($password, $user['password_hash'])) {

            session_regenerate_id(true);
            $_SESSION['id_pengguna']      = (int)$user['id_pengguna'];
            $_SESSION['username']         = $user['username'];
            $_SESSION['nama_lengkap']     = $user['nama_lengkap'];
            $_SESSION['kode_role']        = $user['kode_role'];
            $_SESSION['nama_role']        = $user['nama_role'];
            $_SESSION['id_fakultas']      = $user['id_fakultas'];
            $_SESSION['id_program_studi'] = $user['id_program_studi'];

            $upd = mysqli_prepare($koneksi,
                "UPDATE pengguna SET last_login = NOW() WHERE id_pengguna = ?");
            mysqli_stmt_bind_param($upd, 'i', $_SESSION['id_pengguna']);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);

            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        }
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - SIM Mahasiswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
  <div class="login-box">
    <div class="text-center mb-4">
      <i class="bi bi-mortarboard-fill brand-icon"></i>
      <h4 class="fw-bold mt-2 mb-0">SIM Mahasiswa</h4>
      <small class="text-secondary">Silakan login untuk melanjutkan</small>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Username / NPM</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3"><a href="<?= BASE_URL ?>/index.php" class="small">&larr; Kembali ke beranda</a></div>
  </div>
</body>
</html>
<?php
$menu_aktif = $menu_aktif ?? '';

$role   = $_SESSION['kode_role'] ?? '';
$staf   = ['ADMIN', 'OPERATOR_PRODI', 'DEKANAT', 'REKTORAT'];
$semua  = array_merge($staf, ['MAHASISWA']);
$admin  = ['ADMIN'];

// [kunci, label, ikon, file tujuan, role yang boleh melihat]
$menu = [
    ['dashboard',     'Dashboard',     'bi-speedometer2',      'dashboard.php',         $semua],
    ['universitas',   'Universitas',   'bi-bank',              'universitas.php',       $admin],
    ['fakultas',      'Fakultas',      'bi-building',          'fakultas.php',          $staf],
    ['program_studi', 'Program Studi', 'bi-diagram-3',         'program_studi.php',     $staf],
    ['mahasiswa',     'Mahasiswa',     'bi-people',            'mahasiswa.php',         $staf],
    ['laporan',       'Laporan',       'bi-file-earmark-text', 'laporan_mahasiswa.php', $staf],
    ['pengguna',      'Pengguna',      'bi-person-gear',       'pengguna.php',          $admin],
    ['role',          'Role',          'bi-shield-lock',       'role.php',              $admin],
];
?>
<aside class="app-side">
<?php foreach ($menu as [$kunci, $label, $ikon, $file, $boleh]): ?>
    <?php
    if (!in_array($role, $boleh, true)) continue;      // role ini tidak boleh lihat menu
    $ada   = is_file(__DIR__ . '/../pages/' . $file);          // halaman sudah dibuat?
    $kelas = 'side-link'
           . ($menu_aktif === $kunci ? ' active' : '')
           . (!$ada ? ' disabled' : '');
    ?>
    <a class="<?= $kelas ?>" href="<?= $ada ? BASE_URL . '/pages/' . e($file) : '#' ?>">
        <i class="bi <?= $ikon ?>"></i> <?= e($label) ?>
    </a>
<?php endforeach; ?>
</aside>
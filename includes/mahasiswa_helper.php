<?php
require_once __DIR__ . '/pengguna_helper.php';   // memakai hitung_stmt() dan id_atau_null()

function daftar_status_mhs(): array {
    return ['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'Drop Out', 'Tidak Aktif'];
}

function bisa_kelola_mhs(): bool {
    return in_array($_SESSION['kode_role'] ?? '', ['ADMIN', 'OPERATOR_PRODI'], true);
}

// Kondisi WHERE untuk cakupan daftar: [sql, tipe bind, nilai bind]. Alias tabel mahasiswa = m
function scope_mhs(): array {
    $role = $_SESSION['kode_role'] ?? '';
    if ($role === 'OPERATOR_PRODI') {
        return ['m.id_program_studi = ?', 'i', [(int)($_SESSION['id_program_studi'] ?? 0)]];
    }
    if ($role === 'DEKANAT') {
        return ['m.id_program_studi IN (SELECT id_program_studi FROM program_studi WHERE id_fakultas = ?)',
                'i', [(int)($_SESSION['id_fakultas'] ?? 0)]];
    }
    return ['1=1', '', []];
}

// Boleh mengelola mahasiswa di prodi ini?
function boleh_kelola_prodi(int $idProdi): bool {
    $role = $_SESSION['kode_role'] ?? '';
    if ($role === 'ADMIN') return true;
    if ($role === 'OPERATOR_PRODI') {
        $saya = (int)($_SESSION['id_program_studi'] ?? 0);
        return $saya > 0 && $saya === $idProdi;
    }
    return false;
}

// Prodi yang boleh dipilih di form
function daftar_prodi_mhs(mysqli $koneksi): array {
    if (($_SESSION['kode_role'] ?? '') === 'OPERATOR_PRODI') {
        $id = (int)($_SESSION['id_program_studi'] ?? 0);
        $st = mysqli_prepare($koneksi,
            "SELECT id_program_studi, nama_program_studi FROM program_studi WHERE id_program_studi = ?");
        mysqli_stmt_bind_param($st, 'i', $id);
        mysqli_stmt_execute($st);
        $r = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
        mysqli_stmt_close($st);
        return $r;
    }
    return mysqli_fetch_all(
        mysqli_query($koneksi, "SELECT id_program_studi, nama_program_studi FROM program_studi ORDER BY nama_program_studi"),
        MYSQLI_ASSOC);
}

// Akun ber-role MAHASISWA yang belum terhubung (kecuali yang terhubung ke mahasiswa $idSaatIni)
function daftar_akun_mhs_bebas(mysqli $koneksi, int $idSaatIni = 0): array {
    $st = mysqli_prepare($koneksi,
        "SELECT p.id_pengguna, p.username, p.nama_lengkap
         FROM pengguna p JOIN roles r ON r.id_role = p.id_role
         WHERE r.kode_role = 'MAHASISWA'
           AND NOT EXISTS (SELECT 1 FROM mahasiswa m
                           WHERE m.id_pengguna = p.id_pengguna AND m.id_mahasiswa <> ?)
         ORDER BY p.username");
    mysqli_stmt_bind_param($st, 'i', $idSaatIni);
    mysqli_stmt_execute($st);
    $r = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
    mysqli_stmt_close($st);
    return $r;
}

function ambil_input_mhs(): array {
    return [
        'id_pengguna'      => (int)($_POST['id_pengguna'] ?? 0),
        'id_program_studi' => (int)($_POST['id_program_studi'] ?? 0),
        'npm'              => trim($_POST['npm'] ?? ''),
        'nama_mahasiswa'   => trim($_POST['nama_mahasiswa'] ?? ''),
        'jenis_kelamin'    => trim($_POST['jenis_kelamin'] ?? ''),
        'tempat_lahir'     => trim($_POST['tempat_lahir'] ?? ''),
        'tanggal_lahir'    => trim($_POST['tanggal_lahir'] ?? ''),
        'tanggal_masuk'    => trim($_POST['tanggal_masuk'] ?? ''),
        'alamat'           => trim($_POST['alamat'] ?? ''),
        'status_mahasiswa' => trim($_POST['status_mahasiswa'] ?? ''),
    ];
}

function tgl_valid(string $t): bool {
    $d = DateTime::createFromFormat('Y-m-d', $t);
    return $d && $d->format('Y-m-d') === $t;
}

// $idSaatIni = id mahasiswa yang sedang diubah (0 untuk tambah)
function validasi_mhs(mysqli $koneksi, array $d, int $idSaatIni = 0): array {
    $er = [];

    if (hitung_stmt($koneksi, "SELECT COUNT(*) FROM program_studi WHERE id_program_studi = ?",
                    'i', $d['id_program_studi']) === 0) {
        $er[] = 'Program studi wajib dipilih.';
    }
    if (!preg_match('/^[A-Za-z0-9-]{3,30}$/', $d['npm'])) {
        $er[] = 'NPM 3-30 karakter: huruf, angka, minus.';
    } elseif (hitung_stmt($koneksi, "SELECT COUNT(*) FROM mahasiswa WHERE npm = ? AND id_mahasiswa <> ?",
                          'si', $d['npm'], $idSaatIni) > 0) {
        $er[] = 'NPM sudah dipakai.';
    }
    if ($d['nama_mahasiswa'] === '' || mb_strlen($d['nama_mahasiswa']) > 200) {
        $er[] = 'Nama mahasiswa wajib diisi, maksimal 200 karakter.';
    }
    if (!in_array($d['jenis_kelamin'], ['L', 'P'], true)) {
        $er[] = 'Jenis kelamin tidak valid.';
    }
    if (mb_strlen($d['tempat_lahir']) > 100) {
        $er[] = 'Tempat lahir maksimal 100 karakter.';
    }
    if ($d['tanggal_lahir'] !== '') {
        if (!tgl_valid($d['tanggal_lahir'])) {
            $er[] = 'Tanggal lahir tidak valid.';
        } elseif ($d['tanggal_lahir'] > date('Y-m-d')) {
            $er[] = 'Tanggal lahir tidak boleh di masa depan.';
        }
    }
    if (!tgl_valid($d['tanggal_masuk'])) {
        $er[] = 'Tanggal masuk wajib diisi dengan benar.';
    }
    if (mb_strlen($d['alamat']) > 2000) {
        $er[] = 'Alamat maksimal 2000 karakter.';
    }
    if (!in_array($d['status_mahasiswa'], daftar_status_mhs(), true)) {
        $er[] = 'Status mahasiswa tidak valid.';
    }
    if ($d['id_pengguna'] > 0) {
        $ok = hitung_stmt($koneksi,
            "SELECT COUNT(*) FROM pengguna p JOIN roles r ON r.id_role = p.id_role
             WHERE p.id_pengguna = ? AND r.kode_role = 'MAHASISWA'
               AND NOT EXISTS (SELECT 1 FROM mahasiswa m
                               WHERE m.id_pengguna = p.id_pengguna AND m.id_mahasiswa <> ?)",
            'ii', $d['id_pengguna'], $idSaatIni);
        if ($ok === 0) $er[] = 'Akun login tidak valid atau sudah terhubung ke mahasiswa lain.';
    }
    return $er;
}
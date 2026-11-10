<?php
function ambil_input_pengguna(): array {
    return [
        'id_role'          => (int)($_POST['id_role'] ?? 0),
        'id_universitas'   => (int)($_POST['id_universitas'] ?? 0),
        'id_fakultas'      => (int)($_POST['id_fakultas'] ?? 0),
        'id_program_studi' => (int)($_POST['id_program_studi'] ?? 0),
        'username'         => trim($_POST['username'] ?? ''),
        'nama_lengkap'     => trim($_POST['nama_lengkap'] ?? ''),
        'email'            => trim($_POST['email'] ?? ''),
        'no_hp'            => trim($_POST['no_hp'] ?? ''),
        'status_aktif'     => trim($_POST['status_aktif'] ?? ''),
        'password'         => $_POST['password'] ?? '',
    ];
}

// Hitung satu angka dengan prepared statement
function hitung_stmt(mysqli $koneksi, string $sql, string $tipe, ...$param): int {
    $st = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($st, $tipe, ...$param);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $n);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    return (int)$n;
}

// Validasi + turunkan cakupan. $d diubah lewat referensi (cakupan dibersihkan/diisi otomatis).
// $kode_role dikembalikan lewat $kodeRole.
function validasi_pengguna(mysqli $koneksi, array &$d, bool $tambah, ?string &$kodeRole): array {
    $er = [];

    // Role harus ada
    $st = mysqli_prepare($koneksi, "SELECT kode_role FROM roles WHERE id_role = ?");
    mysqli_stmt_bind_param($st, 'i', $d['id_role']);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $kodeRole);
    $adaRole = mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    if (!$adaRole) { $er[] = 'Role wajib dipilih.'; $kodeRole = null; }

    if (!preg_match('/^[A-Za-z0-9._-]{3,100}$/', $d['username'])) {
        $er[] = 'Username 3-100 karakter: huruf, angka, titik, minus, underscore.';
    }
    if ($d['nama_lengkap'] === '' || mb_strlen($d['nama_lengkap']) > 200) {
        $er[] = 'Nama lengkap wajib diisi, maksimal 200 karakter.';
    }
    if ($d['email'] !== '' && (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 150)) {
        $er[] = 'Format email tidak valid.';
    }
    if (!preg_match('/^[0-9+\-\s()]{0,30}$/', $d['no_hp'])) {
        $er[] = 'No HP hanya angka, spasi, +, -, (, ), maksimal 30 karakter.';
    }
    if (!in_array($d['status_aktif'], ['Aktif', 'Tidak Aktif'], true)) {
        $er[] = 'Status tidak valid.';
    }

    // Password
    if ($tambah && $d['password'] === '') {
        $er[] = 'Password wajib diisi.';
    } elseif ($d['password'] !== '' && mb_strlen($d['password']) < 8) {
        $er[] = 'Password minimal 8 karakter.';
    }

    // Cakupan: turunkan dari yang paling spesifik
    if ($kodeRole !== null) {
        if (in_array($kodeRole, ['OPERATOR_PRODI', 'MAHASISWA'], true)) {
            $d['id_fakultas'] = 0; $d['id_universitas'] = 0;
            $st = mysqli_prepare($koneksi,
                "SELECT f.id_fakultas, f.id_universitas FROM program_studi ps
                 JOIN fakultas f ON f.id_fakultas = ps.id_fakultas WHERE ps.id_program_studi = ?");
            mysqli_stmt_bind_param($st, 'i', $d['id_program_studi']);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $fak, $uni);
            if (mysqli_stmt_fetch($st)) { $d['id_fakultas'] = (int)$fak; $d['id_universitas'] = (int)$uni; }
            else { $er[] = 'Program studi wajib dipilih untuk role ini.'; }
            mysqli_stmt_close($st);
        } elseif ($kodeRole === 'DEKANAT') {
            $d['id_program_studi'] = 0; $d['id_universitas'] = 0;
            $st = mysqli_prepare($koneksi, "SELECT id_universitas FROM fakultas WHERE id_fakultas = ?");
            mysqli_stmt_bind_param($st, 'i', $d['id_fakultas']);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $uni);
            if (mysqli_stmt_fetch($st)) { $d['id_universitas'] = (int)$uni; }
            else { $er[] = 'Fakultas wajib dipilih untuk role Dekanat.'; }
            mysqli_stmt_close($st);
        } elseif ($kodeRole === 'REKTORAT') {
            $d['id_fakultas'] = 0; $d['id_program_studi'] = 0;
            if (hitung_stmt($koneksi, "SELECT COUNT(*) FROM universitas WHERE id_universitas = ?", 'i', $d['id_universitas']) === 0) {
                $er[] = 'Universitas wajib dipilih untuk role Rektorat.';
            }
        } else {
            // ADMIN dan role lain: cakupan opsional, tapi yang diisi harus valid dan konsisten
            if ($d['id_program_studi'] > 0) {
                $st = mysqli_prepare($koneksi,
                    "SELECT f.id_fakultas, f.id_universitas FROM program_studi ps
                     JOIN fakultas f ON f.id_fakultas = ps.id_fakultas WHERE ps.id_program_studi = ?");
                mysqli_stmt_bind_param($st, 'i', $d['id_program_studi']);
                mysqli_stmt_execute($st);
                mysqli_stmt_bind_result($st, $fak, $uni);
                if (mysqli_stmt_fetch($st)) { $d['id_fakultas'] = (int)$fak; $d['id_universitas'] = (int)$uni; }
                else { $er[] = 'Program studi tidak valid.'; }
                mysqli_stmt_close($st);
            } elseif ($d['id_fakultas'] > 0) {
                $st = mysqli_prepare($koneksi, "SELECT id_universitas FROM fakultas WHERE id_fakultas = ?");
                mysqli_stmt_bind_param($st, 'i', $d['id_fakultas']);
                mysqli_stmt_execute($st);
                mysqli_stmt_bind_result($st, $uni);
                if (mysqli_stmt_fetch($st)) { $d['id_universitas'] = (int)$uni; }
                else { $er[] = 'Fakultas tidak valid.'; }
                mysqli_stmt_close($st);
            } elseif ($d['id_universitas'] > 0) {
                if (hitung_stmt($koneksi, "SELECT COUNT(*) FROM universitas WHERE id_universitas = ?", 'i', $d['id_universitas']) === 0) {
                    $er[] = 'Universitas tidak valid.';
                }
            }
        }
    }
    return $er;
}

function username_dipakai(mysqli $koneksi, string $username, int $kecuali = 0): bool {
    return hitung_stmt($koneksi,
        "SELECT COUNT(*) FROM pengguna WHERE username = ? AND id_pengguna <> ?",
        'si', $username, $kecuali) > 0;
}

// Jumlah admin aktif, tidak menghitung id tertentu
function jumlah_admin_aktif_lain(mysqli $koneksi, int $kecuali): int {
    return hitung_stmt($koneksi,
        "SELECT COUNT(*) FROM pengguna p JOIN roles r ON r.id_role = p.id_role
         WHERE r.kode_role = 'ADMIN' AND p.status_aktif = 'Aktif' AND p.id_pengguna <> ?",
        'i', $kecuali);
}

// 0 -> NULL untuk kolom relasi
function id_atau_null(int $v): ?int {
    return $v > 0 ? $v : null;
}
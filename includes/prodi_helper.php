<?php
function daftar_jenjang(): array {
    return ['D3', 'D4', 'S1', 'S2', 'S3'];
}

function ambil_input_prodi(): array {
    return [
        'id_fakultas'        => (int)($_POST['id_fakultas'] ?? 0),
        'kode_program_studi' => strtoupper(trim($_POST['kode_program_studi'] ?? '')),
        'nama_program_studi' => trim($_POST['nama_program_studi'] ?? ''),
        'jenjang'            => trim($_POST['jenjang'] ?? ''),
        'status_aktif'       => trim($_POST['status_aktif'] ?? ''),
    ];
}

function validasi_prodi(mysqli $koneksi, array $d): array {
    $er = [];

    $st = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM fakultas WHERE id_fakultas = ?");
    mysqli_stmt_bind_param($st, 'i', $d['id_fakultas']);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $ada);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    if (!$ada) $er[] = 'Fakultas wajib dipilih.';

    if (!preg_match('/^[A-Z0-9_-]{1,20}$/', $d['kode_program_studi'])) {
        $er[] = 'Kode program studi 1-20 karakter: huruf besar, angka, minus, underscore.';
    }
    if ($d['nama_program_studi'] === '' || mb_strlen($d['nama_program_studi']) > 200) {
        $er[] = 'Nama program studi wajib diisi, maksimal 200 karakter.';
    }
    if (!in_array($d['jenjang'], daftar_jenjang(), true)) {
        $er[] = 'Jenjang tidak valid.';
    }
    if (!in_array($d['status_aktif'], ['Aktif', 'Tidak Aktif'], true)) {
        $er[] = 'Status tidak valid.';
    }
    return $er;
}

// Kode sudah dipakai di fakultas yang sama? ($kecuali = id prodi yang sedang diubah)
function kode_prodi_dipakai(mysqli $koneksi, int $idFak, string $kode, int $kecuali = 0): bool {
    $st = mysqli_prepare($koneksi,
        "SELECT COUNT(*) FROM program_studi
         WHERE id_fakultas = ? AND kode_program_studi = ? AND id_program_studi <> ?");
    mysqli_stmt_bind_param($st, 'isi', $idFak, $kode, $kecuali);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $jml);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    return $jml > 0;
}
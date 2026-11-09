<?php
function ambil_input_fakultas(): array {
    return [
        'id_universitas' => (int)($_POST['id_universitas'] ?? 0),
        'kode_fakultas'  => strtoupper(trim($_POST['kode_fakultas'] ?? '')),
        'nama_fakultas'  => trim($_POST['nama_fakultas'] ?? ''),
    ];
}

// $koneksi dipakai untuk memastikan universitas yang dipilih benar-benar ada
function validasi_fakultas(mysqli $koneksi, array $d): array {
    $er = [];

    $st = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM universitas WHERE id_universitas = ?");
    mysqli_stmt_bind_param($st, 'i', $d['id_universitas']);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $ada);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    if (!$ada) $er[] = 'Universitas wajib dipilih.';

    if (!preg_match('/^[A-Z0-9_-]{1,20}$/', $d['kode_fakultas'])) {
        $er[] = 'Kode fakultas 1-20 karakter: huruf besar, angka, minus, underscore.';
    }
    if ($d['nama_fakultas'] === '' || mb_strlen($d['nama_fakultas']) > 200) {
        $er[] = 'Nama fakultas wajib diisi, maksimal 200 karakter.';
    }
    return $er;
}

// Kode sudah dipakai di universitas yang sama? ($kecuali = id fakultas yang sedang diubah)
function kode_fakultas_dipakai(mysqli $koneksi, int $idUniv, string $kode, int $kecuali = 0): bool {
    $st = mysqli_prepare($koneksi,
        "SELECT COUNT(*) FROM fakultas
         WHERE id_universitas = ? AND kode_fakultas = ? AND id_fakultas <> ?");
    mysqli_stmt_bind_param($st, 'isi', $idUniv, $kode, $kecuali);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $jml);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    return $jml > 0;
}
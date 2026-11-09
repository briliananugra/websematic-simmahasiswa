<?php
// Ambil dan bersihkan input form universitas
function ambil_input_universitas(): array {
    $f = ['kode_universitas', 'nama_universitas', 'slogan', 'alamat', 'kota',
          'provinsi', 'kode_pos', 'website', 'email', 'telepon'];
    $d = [];
    foreach ($f as $k) { $d[$k] = trim($_POST[$k] ?? ''); }
    $d['kode_universitas'] = strtoupper($d['kode_universitas']);
    return $d;
}

// Kembalikan daftar pesan error (kosong = valid)
function validasi_universitas(array $d): array {
    $er = [];
    if (!preg_match('/^[A-Z0-9_-]{2,20}$/', $d['kode_universitas'])) {
        $er[] = 'Kode universitas 2-20 karakter: huruf besar, angka, minus, underscore.';
    }
    if ($d['nama_universitas'] === '' || mb_strlen($d['nama_universitas']) > 200) {
        $er[] = 'Nama universitas wajib diisi, maksimal 200 karakter.';
    }
    if (mb_strlen($d['slogan']) > 255)   $er[] = 'Slogan maksimal 255 karakter.';
    if (mb_strlen($d['alamat']) > 2000)  $er[] = 'Alamat maksimal 2000 karakter.';
    if (mb_strlen($d['kota']) > 100)     $er[] = 'Kota maksimal 100 karakter.';
    if (mb_strlen($d['provinsi']) > 100) $er[] = 'Provinsi maksimal 100 karakter.';
    if (!preg_match('/^[0-9]{0,10}$/', $d['kode_pos'])) {
        $er[] = 'Kode pos hanya angka, maksimal 10 digit.';
    }
    if ($d['website'] !== '') {
        if (!preg_match('~^https?://~i', $d['website']) || !filter_var($d['website'], FILTER_VALIDATE_URL)
            || mb_strlen($d['website']) > 255) {
            $er[] = 'Website harus diawali http:// atau https:// dan berupa URL valid.';
        }
    }
    if ($d['email'] !== '' && (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 150)) {
        $er[] = 'Format email tidak valid.';
    }
    if (!preg_match('/^[0-9+\-\s()]{0,50}$/', $d['telepon'])) {
        $er[] = 'Telepon hanya angka, spasi, +, -, (, ), maksimal 50 karakter.';
    }
    return $er;
}

// String kosong disimpan sebagai NULL
function atau_null(string $v): ?string {
    return $v === '' ? null : $v;
}
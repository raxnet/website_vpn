<?php
// Mendapatkan domain yang digunakan oleh aplikasi
$domain = $_SERVER['SERVER_NAME']; // Nama domain aktif

// URL file JSON lisensi di GitHub
$github_url = "https://raw.githubusercontent.com/raxnet/webapp/refs/heads/main/licence.json";

// File cache lokal untuk lisensi
$cache_file = __DIR__ . '/license_cache.json';
$cache_time = 3600; // Cache berlaku selama 1 jam

// Mengambil data lisensi dengan caching
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    $response = file_get_contents($cache_file);
} else {
    // Mengambil data dari GitHub menggunakan cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $github_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifikasi SSL
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        die("Tidak bisa mengakses daftar domain sah.");
    }

    // Simpan data ke cache
    file_put_contents($cache_file, $response);
}

// Decode JSON ke array PHP
$data = json_decode($response, true);

// Pastikan format data JSON valid
if (!isset($data['valid_domains'])) {
    die("Format data lisensi tidak valid.");
}

// Validasi domain dan tanggal kedaluwarsa
$domain_found = false;
foreach ($data['valid_domains'] as $entry) {
    if (isset($entry['domain'], $entry['expiry_date'], $entry['token']) && $entry['domain'] === $domain) {
        $domain_found = true;

        // Memeriksa tanggal kedaluwarsa
        $current_date = date("Y-m-d");
        if ($current_date > $entry['expiry_date']) {
            die("Lisensi untuk domain ini telah kedaluwarsa.");
        }

        // Validasi token lisensi
        $client_token = 'Aqus97shwGsjd'; // Token unik untuk aplikasi ini
        if ($entry['token'] !== $client_token) {
            die("Token lisensi tidak valid.");
        }

        // Lisensi valid, keluar dari loop
        break;
    }
}

// Jika domain tidak ditemukan dalam daftar valid, tolak akses
if (!$domain_found) {
    die("Lisensi tidak valid untuk domain ini.");
}

// Lisensi valid, lanjutkan eksekusi
?>
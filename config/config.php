<?php
// config.php

// Informasi koneksi database
$host = 'localhost'; // Ganti dengan host database Anda
$username = 'root'; // Ganti dengan username database Anda
$password = 'server'; // Ganti dengan password database Anda
$dbname = 'raxnet_db'; // Nama database yang Anda buat

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

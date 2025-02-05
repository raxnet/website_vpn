<?php
// Koneksi ke database
include '../config/config.php';

// Query untuk mengambil data
$query = "SELECT day, activity_count FROM account_activity"; // Ganti dengan query sesuai kebutuhan
$result = mysqli_query($conn, $query);

// Menyimpan data ke dalam array
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row['activity_count']; // Data jumlah aktivitas akun per hari
}

// Mengirimkan data dalam format JSON
echo json_encode($data);
?>
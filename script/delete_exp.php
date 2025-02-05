<?php
// Koneksi database web
include('../config/config.php');

// Ambil alamat IP atau URL server API yang statusnya "Available"
$sql_ip = "SELECT ip_address FROM servers WHERE status = 'Available' ORDER BY id LIMIT 1"; // Ambil server dengan status 'Available'
$result_ip = $conn->query($sql_ip);
$server_ip = ''; // Variabel untuk menyimpan IP/URL server API

if ($result_ip->num_rows > 0) {
    $server = $result_ip->fetch_assoc();
    $server_ip = $server['ip_address']; // Ambil IP address
} else {
    // Jika tidak ditemukan server aktif, berikan pesan error
    die("No available API server found.");
}

// Ambil tanggal kadaluarsa dari database
$sql = "SELECT * FROM accounts WHERE expiration_date < NOW() AND status != 'deleted'";
$result = $conn->query($sql);

while ($account = $result->fetch_assoc()) {
    $uuid = $account['uuid'];

    // Panggil API untuk menghapus akun dari server API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://$server_ip:5000/delete-account"); // URL endpoint API dengan IP yang diambil dari database
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['uuid' => $uuid])); // Kirim UUID akun
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Tangani response
    if ($http_code == 200) {
        // Jika berhasil, tandai akun sebagai "deleted" di database web
        $sql_update = "UPDATE accounts SET status = 'deleted' WHERE uuid = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
    }
}
?>
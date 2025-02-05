<?php
// Koneksi database
include('/www/wwwroot/panel.raxnet.my.id/config/config.php');

// Fungsi untuk memeriksa status server dengan port 80 (HTTP)
function checkServerStatus($ip_address) {
    $timeout = 2; // Waktu timeout dalam detik
    $port = 80; // Port HTTP (ganti sesuai dengan kebutuhan, seperti 443 atau 8080)

    // Cek apakah port bisa diakses
    $connection = @fsockopen($ip_address, $port, $errno, $errstr, $timeout);

    if ($connection) {
        fclose($connection);
        return true; // Server aktif pada port 80
    } else {
        return false; // Server tidak aktif pada port 80
    }
}

// Ambil semua server dari tabel `servers`
$sql = "SELECT id, ip_address FROM servers";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($server = $result->fetch_assoc()) {
        $server_id = $server['id'];
        $ip_address = $server['ip_address'];

        // Periksa status server pada port 80
        $is_active = checkServerStatus($ip_address);

        // Update status di database
        $status = $is_active ? 'Available' : 'Unavailable';
        $sql_update = "UPDATE servers SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $status, $server_id);
        $stmt->execute();
    }

    echo "Server statuses updated successfully.\n";
} else {
    echo "No servers found in the database.\n";
}

// Tutup koneksi database
$conn->close();
?>
<?php
include('../script/autoload.php');
// Ambil daftar server dari database
$limit = 10; // Menampilkan 10 server per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$sql = "SELECT * FROM servers WHERE status = 'Available' LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Available</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        ul {
            list-style-type: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            padding: 15px;
            margin: 30px auto;
            max-width: 1100px;
        }

        .server-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 180px;
        }

        .server-name {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            -webkit-background-clip: text;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
            color: transparent;
            margin: 0;
        }

        .server-details {
            color: #555;
            font-size: 12px;
            margin: 5px 0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .status {
            padding: 4px 8px;
            border-radius: 12px;
            color: #fff;
            font-weight: bold;
        }

        .status-available {
            background-color: #27ae60;
        }

        .status-unavailable {
            background-color: #e74c3c;
        }

        .create-account-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }

        .create-account-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .no-server {
            color: #ccc;
            text-align: center;
            font-size: 14px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while ($server = $result->fetch_assoc()) {
                $server_id = $server['id'];
                $sql_akun = "SELECT COUNT(*) AS jumlah_akun FROM accounts WHERE server_id = $server_id";
                $result_akun = $conn->query($sql_akun);
                if($result_akun) {
                    $akun_data = $result_akun->fetch_assoc();
                    $jumlah_akun_dibuat = $akun_data['jumlah_akun'];
                    $jumlah_akun_maksimal = $server['jumlah_akun_maksimal'];

                    $status = $jumlah_akun_dibuat < $jumlah_akun_maksimal ? 'Available' : 'Unavailable';
                    $status_text = $jumlah_akun_dibuat < $jumlah_akun_maksimal ? "Tersedia (" . ($jumlah_akun_maksimal - $jumlah_akun_dibuat) . " akun lagi)" : "Tidak Tersedia";

                    echo "<li class='server-card'>";
                    echo "<h3 class='server-name'>" . $server['server_name'] . " (" . $server['country'] . ")</h3>";
                    echo "<p class='server-details'>IP Address: " . $server['ip_address'] . "</p>";
                    echo "<p class='server-details'>Domain: " . $server['domain'] . "</p>";
                    echo "<p class='server-details'>Harga: Rp " . number_format($server['price'], 0, ',', '.') . " / bulan</p>";
                    echo "<p class='server-details'>Status: <span class='status " . ($status === 'Available' ? 'status-available' : 'status-unavailable') . "'>" . $status_text . "</span></p>";
                    echo "<a href='../user/proses_vless?server_id=" . $server['id'] . "'><button class='create-account-btn'>Buat Akun</button></a>";
                    echo "</li>";
                }
            }
        } else {
            echo "<p class='no-server'>Tidak ada server tersedia.</p>";
        }
        ?>
    </ul>
</body>
</html>
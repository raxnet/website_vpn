<?php
include('../config/config.php');
// Fungsi untuk mengambil data dari server
function getServerData($ip) {
    $url = "http://$ip:5000/monitor"; // Ganti dengan endpoint yang benar
    $ch = curl_init();

    // Setup CURL untuk POST request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "Error: " . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($response, true); // Mengembalikan response dalam bentuk array
}

// Cek apakah ada IP server yang dikirim melalui query parameter
$ip = isset($_GET['ip']) ? $_GET['ip'] : '';
$data = null;

if ($ip) {
    $data = getServerData($ip); // Ambil data dari server
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Server</title>
    <style>
        body {
            font-family: Arial, sans-serif;
     
            color: #ddd;
           
        }

        .server-list {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            padding: 30px 0;
            overflow-x: auto;
        }
        .server-box {
         top:30px;
            background-color: #2c3e50;
            width: 300px;
            height: 30px;
            border-radius: 9px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            transition: box-shadow 0.3s ease, background 0.3s ease;
        }
        .server-box:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            background: linear-gradient(45deg, #3498db, #2980b9);
        }
        .server-box h3 {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin: 0 auto;
        }
        .server-box .status-light {
            width: 10px;
            height: 10px;
            background-color: #28a745;
            border-radius: 50%;
            box-shadow: 0 0 10px #28a745;
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
        }
        .server-box .action-btn {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            padding: 5px 10px;
            background-color: #3498db;
            color: #fff;
            font-size: 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .server-box .action-btn:hover {
            background-color: #00ff33;
        }

/* Style untuk container output */
/* Container output dengan latar belakang gradasi dan bayangan */
.output-container {
    background: linear-gradient(145deg, #2f4b6b, #1c3b47);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    margin-top: 30px;
    position: relative;
    transition: all 0.3s ease-in-out;
}

.output-container::before {
    content: "";
    position: absolute;
    top: -15px;
    left: -15px;
    right: -15px;
    bottom: -15px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 20px;
    z-index: -1;
    filter: blur(10px);
}

.output-container:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8);
    transform: translateY(-10px);
}

/* Tabel untuk informasi server */
.output-container table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    border-radius: 12px;
}

.output-container th, .output-container td {
    padding: 14px 20px;
    text-align: left;
    border: 1px solid #444;
    color: #ecf0f1;
    font-family: 'Arial', sans-serif;
    font-size: 15px;
}

.output-container th {
    background: linear-gradient(135deg, #2980b9, #3498db);
    color: #fff;
    font-weight: bold;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transition: background 0.3s ease;
}

.output-container th:hover {
    background: linear-gradient(135deg, #1abc9c, #16a085);
}

.output-container tr:nth-child(even) {
    background-color: #34495e;
    transition: background-color 0.3s ease;
}

.output-container tr:nth-child(odd) {
    background-color: #2c3e50;
    transition: background-color 0.3s ease;
}

.output-container tr:hover {
    background-color: #1abc9c;
    transform: scale(1.03);
    transition: all 0.3s ease-in-out;
}

/* Status untuk layanan dengan efek dinamis */
.status {
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    margin-right: 12px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.6);
    transition: all 0.3s ease-in-out;
}

.status.running {
    background: linear-gradient(45deg, #2ecc71, #27ae60);
    box-shadow: 0 0 20px rgba(46, 204, 113, 0.8);
}

.status.stopped {
    background: linear-gradient(45deg, #e74c3c, #c0392b);
    box-shadow: 0 0 20px rgba(231, 76, 60, 0.8);
}

.status.running:hover,
.status.stopped:hover {
    transform: scale(1.5);
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.7);
}

/* Animasi untuk tabel cell yang sedang loading */
@keyframes blink {
    0% {
        background-color: #f39c12;
    }
    50% {
        background-color: #f1c40f;
    }
    100% {
        background-color: #f39c12;
    }
}

.output-container td.loading {
    animation: blink 1.5s infinite;
}

/* Efek transisi hover pada tombol action */
.output-container .status,
.output-container td {
    position: relative;
    z-index: 1;
}

.output-container td .action-btn {
    background-color: #e74c3c;
    color: #fff;
    padding: 8px 15px;
    font-size: 14px;
    border-radius: 10px;
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    cursor: pointer;
    border: none;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.output-container td:hover .action-btn {
    opacity: 1;
    transform: translateY(-50%) scale(1.1);
}

/* Desain untuk tabel kolom 'status' dengan ikon dan animasi */
.output-container td.status-cell {
    position: relative;
    display: flex;
    align-items: center;
}

.output-container td.status-cell .status-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
}

.output-container td.status-cell .status-icon.running {
    background: #2ecc71;
    box-shadow: 0 0 15px rgba(46, 204, 113, 0.8);
}

.output-container td.status-cell .status-icon.stopped {
    background: #e74c3c;
    box-shadow: 0 0 15px rgba(231, 76, 60, 0.8);
}

.output-container td.status-cell:hover .status-icon {
    transform: scale(1.2);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
}

/* Animasi untuk status yang berubah */
@keyframes status-change {
    0% {
        transform: scale(0.9);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.output-container td.status-cell .status-icon {
    animation: status-change 0.8s ease-in-out;
}


    </style>
</head>
<body>


    <div class="server-list" id="server-list">
        <?php
        // Ambil daftar server dari database
        $sql = "SELECT * FROM servers WHERE status = 'Available'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($server = $result->fetch_assoc()) {
                echo "<div class='server-box'>";
                echo "<div class='status-light'></div>";
                echo "<h3>" . $server['server_name'] . "</h3>";
                echo "<a href='?ip=" . $server['ip_address'] . "' class='action-btn'>detail</a>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-servers'>Tidak ada server tersedia.</p>";
        }
        ?>
    </div>

    <?php if ($data): ?>
        <div class="output-container">
            <table>
                <tr>
                    <th>CPU Usage</th>
                    <td><?php echo $data['cpu']['usage']; ?>%</td>
                </tr>
                <tr>
                    <th>Disk Usage</th>
                    <td><?php echo $data['disk']['used'] . " / " . $data['disk']['total'] . " (" . $data['disk']['percent'] . "%)"; ?></td>
                </tr>
                <tr>
                    <th>Memory Usage</th>
                    <td><?php echo $data['memory']['used'] . " / " . $data['memory']['total'] . " (" . $data['memory']['percent'] . "%)"; ?></td>
                </tr>
                <tr>
                    <th>Dropbear Service</th>
                    <td><span class="status <?php echo ($data['services']['dropbear'] == 'running') ? 'running' : 'stopped'; ?>"></span><?php echo ucfirst($data['services']['dropbear']); ?></td>
                </tr>
                <tr>
                    <th>SSH Service</th>
                    <td><span class="status <?php echo ($data['services']['ssh'] == 'running') ? 'running' : 'stopped'; ?>"></span><?php echo ucfirst($data['services']['ssh']); ?></td>
                </tr>
                <tr>
                    <th>XRay Service</th>
                    <td><span class="status <?php echo ($data['services']['xray'] == 'running') ? 'running' : 'stopped'; ?>"></span><?php echo ucfirst($data['services']['xray']); ?></td>
                </tr>
                <tr>
                    <th>Hostname</th>
                    <td><?php echo $data['system']['hostname']; ?></td>
                </tr>
                <tr>
                    <th>OS Name</th>
                    <td><?php echo $data['system']['os_name']; ?></td>
                </tr>
                <tr>
                    <th>OS Version</th>
                    <td><?php echo $data['system']['os_version']; ?></td>
                </tr>
                <tr>
                    <th>Uptime</th>
                    <td><?php echo $data['system']['uptime']; ?></td>
                </tr>
            </table>
        </div>
    <?php else: ?>
        <p>cek untuk melihat detail </p>
    <?php endif; ?>

</body>
</html>
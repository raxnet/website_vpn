<?php
session_start();
include('../script/autoload.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit;
}

// Ambil ID pengguna yang sedang login
$user_id = $_SESSION['user_id'];

// Ambil ID akun dari parameter URL
if (!isset($_GET['account_id']) || !is_numeric($_GET['account_id'])) {
    echo "Akun tidak ditemukan.";
    exit;
}
$account_id = $_GET['account_id'];

// Ambil detail akun berdasarkan account_id
$query = "SELECT a.*, s.server_name, s.ip_address, s.domain 
          FROM accounts a
          LEFT JOIN servers s ON a.server_id = s.id
          WHERE a.user_id = ? AND a.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $account_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Akun tidak ditemukan.";
    exit;
}

// Ambil data akun
$account = $result->fetch_assoc();

$username = htmlspecialchars($account['username']);
$uuid = htmlspecialchars($account['uuid']);
$server = $account['server_name'];
$domain = htmlspecialchars($account['domain']);
$protocol = htmlspecialchars($account['protocol']); // Protokol: vmess, vless, trojan

// Menyiapkan URL sesuai dengan protokol
if ($protocol == 'vmess') {
    // VMess dengan Base64
    $config = [
        'add' => $domain,
        'host' => $domain,
        'aid' => 0,
        'type' => '',
        'path' => '/vmess',
        'net' => 'ws',
        'ps' => "vvip-$username",
        'tls' => 'tls',
        'sni' => $domain,
        'port' => '443',
        'v' => '2',
        'id' => $uuid
    ];

    $json_config = json_encode($config);
    $base64_config_tls = base64_encode($json_config);
    $vmess_url_tls = "vmess://".$base64_config_tls;

    $config['tls'] = '';
    $config['port'] = '80';  // Non-TLS menggunakan port 80
    $json_config_non_tls = json_encode($config);
    $base64_config_non_tls = base64_encode($json_config_non_tls);
    $vmess_url_non_tls = "vmess://".$base64_config_non_tls;

    // gRPC untuk VMess
    $config['net'] = 'grpc';
    $json_config_grpc = json_encode($config);
    $base64_config_grpc = base64_encode($json_config_grpc);
    $vmess_url_grpc = "vmess://".$base64_config_grpc;
}

if ($protocol == 'vless') {
    // Vless tanpa Base64
    $vless_url_tls = "vless://$uuid@$domain:443?type=ws&security=tls&sni=$domain&path=/vless&encryption=none#trial-$username";
    $vless_url_non_tls = "vless://$uuid@$domain:80?type=ws&security=none&path=/vless&encryption=none#trial-$username";

    // gRPC untuk Vless
    $vless_url_grpc = "vless://$uuid@$domain:443?type=grpc&security=tls&sni=$domain&path=/vless&encryption=none#trial-$username";
}

if ($protocol == 'trojan') {
    // Trojan tanpa Base64
    $trojan_url_ws = "trojan://$uuid@$domain:443?type=ws&security=tls&sni=$domain&path=/trojan-ws&encryption=none#trial-$username";
    $trojan_url_grpc = "trojan://$uuid@$domain:433?mode=gun&security=tls&path=/trojan-grpc&encryption=none&type=grpc&serviceName=trojan-grpc&sni=$domain#trial-$username";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Akun</title>
    <style>
        body {
            background-color: #20232A;
            color: #EDEDED;

            
        }

        .container {
            max-width: 300px; /* Memperkecil ukuran container */
            margin: 50px auto;
            background-color: #2C2F3A;
            border-radius: 8px;
            padding: 15px; /* Mengurangi padding untuk membuatnya lebih rapat */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .detail-header {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #61DAFB;
            margin-bottom: 15px;
        }

        .account-details p {
            font-size: 14px;
            margin: 8px 0;
            word-wrap: break-word;
        }

        .url-box {
            background-color: #3C4048;
            padding: 8px;
            border-radius: 6px;
            font-size: 12px;
            overflow-x: auto;
            word-wrap: break-word; /* Agar URL tidak keluar layar */
        }

        .copy-btn {
            margin-top: 10px;
            display: inline-block;
            padding: 6px 12px;
            background-color: #61DAFB;
            color: #20232A;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .copy-btn:hover {
            background-color: #4fc1e9;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #E44C65;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #c33a50;
        }

        .icon {
            margin-right: 6px;
            font-size: 14px;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .url-box {
                font-size: 10px;
            }

            .copy-btn, .back-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="detail-header">Detail Akun</div>

    <div class="account-details">
        <p><strong>Protokol:</strong> <?= strtoupper($protocol) ?></p>
        <p><strong>UUID:</strong> <?= $uuid ?></p>
        <p><strong>Server:</strong> <?= htmlspecialchars($account['server_name']) ?> (<?= htmlspecialchars($account['domain']) ?>)</p>
        <p><strong>Tanggal Expirasi:</strong> <?= htmlspecialchars($account['expiration_date']) ?></p>
        <p><strong>Status:</strong> 
            <?php if (strtotime($account['expiration_date']) < time()): ?>
                <span style="color: red;">Kadaluarsa</span>
            <?php else: ?>
                <span style="color: green;">Aktif</span>
            <?php endif; ?>
        </p>

        <!-- URL dan Tombol Salin -->
        <?php if ($protocol == 'vmess'): ?>
            <p><strong>VMess TLS:</strong></p>
            <div class="url-box"><?= $vmess_url_tls ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vmess_url_tls ?>')">Salin URL</button>

            <p><strong>VMess Non-TLS:</strong></p>
            <div class="url-box"><?= $vmess_url_non_tls ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vmess_url_non_tls ?>')">Salin URL</button>

            <p><strong>VMess gRPC:</strong></p>
            <div class="url-box"><?= $vmess_url_grpc ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vmess_url_grpc ?>')">Salin URL</button>
        <?php endif; ?>

        <?php if ($protocol == 'vless'): ?>
            <p><strong>VLess TLS:</strong></p>
            <div class="url-box"><?= $vless_url_tls ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vless_url_tls ?>')">Salin URL</button>

            <p><strong>VLess Non-TLS:</strong></p>
            <div class="url-box"><?= $vless_url_non_tls ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vless_url_non_tls ?>')">Salin URL</button>

            <p><strong>VLess gRPC:</strong></p>
            <div class="url-box"><?= $vless_url_grpc ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $vless_url_grpc ?>')">Salin URL</button>
        <?php endif; ?>

        <?php if ($protocol == 'trojan'): ?>
            <p><strong>Trojan WS:</strong></p>
            <div class="url-box"><?= $trojan_url_ws ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $trojan_url_ws ?>')">Salin URL</button>

            <p><strong>Trojan gRPC:</strong></p>
            <div class="url-box"><?= $trojan_url_grpc ?></div>
            <button class="copy-btn" onclick="copyToClipboard('<?= $trojan_url_grpc ?>')">Salin URL</button>
        <?php endif; ?>
    </div>

    <div class="account-actions">
        <a href="daftar_akun" class="back-btn">Kembali</a>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert("URL berhasil disalin!");
    }
</script>

</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../script/autoload.php');

if (!isset($_SESSION['user_id'])) {
    echo "Session user_id tidak ada.";
    exit;
}

$user_id = $_SESSION['user_id'];
$server_id = $_GET['server_id'] ?? null;

if (!$server_id) {
    die("Server ID tidak ditemukan.");
}

$sql = "SELECT * FROM servers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $server_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $server = $result->fetch_assoc();
} else {
    die("Server tidak ditemukan.");
}

$sql = "SELECT saldo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $saldo = $user['saldo'];
} else {
    die("Pengguna tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $duration = $_POST['duration'] ?? 'trial'; // Default ke trial

    if (!$username || strlen($username) < 3) {
        die("Username tidak valid.");
    }

    $is_trial = $duration === 'trial';
    $harga_per_bulan = $is_trial ? 0 : $server['price'];
    $total_harga = $is_trial ? 0 : $harga_per_bulan * (int) $duration;

    if ($saldo < $total_harga && !$is_trial) {
        $error_message = "Maaf, saldo Anda tidak cukup untuk membuat akun ini.";
    } else {
        if (!$is_trial) {
            $new_saldo = $saldo - $total_harga;
            $sql = "UPDATE users SET saldo = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_saldo, $user_id);
            $stmt->execute();
        }
        


        function generateUUID() {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        $uuid = generateUUID();
        $expiration_date = $is_trial 
            ? date('Y-m-d H:i:s', strtotime('+1 hour')) 
            : date('Y-m-d H:i:s', strtotime("+$duration month"));
        
        $config = [
            'add' => $server['domain'],
            'host' => $server['domain'],
            'aid' => 0,
            'type' => '',
            'path' => '/vmess',
            'net' => 'ws',
            'ps' => "vvip-$username",
            'tls' => 'tls',
            'sni' => $server['domain'],
            'port' => '443',
            'v' => '2',
            'id' => $uuid
        ];

        $json_config = json_encode($config);
        $base64_config_tls = base64_encode($json_config);
        $vmess_url_tls = "vmess://".$base64_config_tls;

        // Perbaikan: Mengubah port non-TLS menjadi 80
        $config['tls'] = '';
        $config['port'] = '80';  // Non-TLS menggunakan port 80
        $json_config_non_tls = json_encode($config);
        $base64_config_non_tls = base64_encode($json_config_non_tls);
        $vmess_url_non_tls = "vmess://".$base64_config_non_tls;

        $data = [
            'protocol' => 'vmess',
            'uuid' => $uuid,
            'username' => $username,
            'expiration_date' => $expiration_date,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://{$server['ip_address']}:5000/create-account");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
        	

    // Proses PHP seperti pembuatan akun di server
    sleep(5);  // Simulasi delay
        
        
        
            $sql = "INSERT INTO accounts (user_id, server_id, username, protocol, uuid, expiration_date, vmess_url_tls, vmess_url_non_tls) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $protocol = 'vmess';
            $stmt->bind_param("iissssss", $user_id, $server_id, $username, $protocol, $uuid, $expiration_date, $vmess_url_tls, $vmess_url_non_tls);
            
            if ($stmt->execute()) {
                $_SESSION['account_created_vmess'] = true;
                $_SESSION['created_account_data_vmess'] = [
                    'username' => $username,
                    'uuid' => $uuid,
                    'expiration_date' => $expiration_date,
                    'vmess_url_tls' => $vmess_url_tls,
                    'vmess_url_non_tls' => $vmess_url_non_tls
                ];

                header("Location: {$_SERVER['PHP_SELF']}?server_id=$server_id");
                exit;
            } else {
                $error_message = "Gagal menyimpan akun ke database: " . $stmt->error;
            }
        } else {
            $error_message = "Gagal membuat akun di server vmess: $response";
        }
    }
}


ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun VMess</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f4f8; /* Background cerah */
            color: #333; /* Warna teks gelap untuk kontras yang baik */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            box-sizing: border-box;
        }

        /* Container Styling */
        .container {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 1px solid #ddd;
        }

        /* Heading Styling */
        .judul-container {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #007bff; /* Warna biru yang profesional */
            text-align: center;
            margin-bottom: 15px;
        }

        /* Balance Section */
        .balance {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f9f9f9;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        /* Submit Button Styling */
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            border: none;
            background: #007bff;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 15px;
        }

        .btn:hover {
            background: #0056b3;
        }

        /* Result Box Styling */
        .result {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            color: #333;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .result h3 {
            font-size: 16px;
            color: #28a745;
            margin-bottom: 10px;
        }

        .result p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .result textarea {
            width: 100%;
            padding: 10px;
            font-size: 12px;
            background: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 6px;
            color: #333;
            resize: none;
            transition: border-color 0.3s ease;
        }

        .result textarea:focus {
            border-color: #007bff;
        }

        /* Copy Button Styling */
        .btn-copy {
            margin-top: 10px;
            background-color: #28a745;
            padding: 8px 15px;
            border: none;
            color: #fff;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-copy:hover {
            background-color: #218838;
        }

        /* Error Message */
        .error {
            color: #e74c3c;
            background: #fdecea;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        /* Error Message Styling */
        .error-message {
            color: #e74a3b;
            font-size: 12px;
            margin-top: 10px;
            text-align: center;
        }

        /* Spinner Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        /* Loading Spinner */
        .loading {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        function copyToClipboard(id) {
            const textarea = document.getElementById(id);
            navigator.clipboard.writeText(textarea.value).then(() => {
                const button = document.getElementById(id + '_btn');
                button.textContent = "URL Tersalin!";
                setTimeout(() => button.textContent = "Salin URL", 2000);
            }).catch(err => {
                alert("Gagal menyalin URL: " + err);
            });
        }
    </script>
</head>
<body>
    <!-- Spinner Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading"></div>
    </div>
    <div class="container">
        <h2 class="judul-container">Buat Akun VMess</h2>
        <div class="balance">Saldo Anda: <?php echo number_format($saldo, 0, ',', '.'); ?> IDR</div>

        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post" onsubmit="showLoading()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label for="duration">Durasi Akun (Bulan)</label>
                <select name="duration" id="duration" required>
                    <option value="trial">Trial 1 Jam</option>
                    <option value="1">1 Bulan</option>
                    <option value="2">2 Bulan</option>
                    <option value="3">3 Bulan</option>
                    <option value="4">4 Bulan</option>
                </select>
            </div>

            <button type="submit" class="btn">Buat Akun</button>
        </form>

        <?php if (isset($_SESSION['account_created_vmess'])): ?>
            <div class="result">
                <h3>Akun Berhasil Dibuat!</h3>
                <p><strong>Username:</strong> <?php echo $_SESSION['created_account_data_vmess']['username']; ?></p>
                <p><strong>UUID:</strong> <?php echo $_SESSION['created_account_data_vmess']['uuid']; ?></p>
                <p><strong>Tanggal Kadaluarsa:</strong> <?php echo $_SESSION['created_account_data_vmess']['expiration_date']; ?></p>

                <h4>URL VMess TLS:</h4>
                <textarea id="vmess_url_tls" readonly><?php echo $_SESSION['created_account_data_vmess']['vmess_url_tls']; ?></textarea>
                <button id="vmess_url_tls_btn" class="btn-copy" onclick="copyToClipboard('vmess_url_tls')">Salin URL</button>

                <h4>URL VMess Non-TLS:</h4>
                <textarea id="vmess_url_non_tls" readonly><?php echo $_SESSION['created_account_data_vmess']['vmess_url_non_tls']; ?></textarea>
                <button id="vmess_url_non_tls_btn" class="btn-copy" onclick="copyToClipboard('vmess_url_non_tls')">Salin URL</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
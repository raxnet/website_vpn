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
        die("username tidak valid.");
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

        function generatepassword() {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        $password = generatepassword(); // Fungsi untuk menghasilkan password

        $expiration_date = $is_trial 
            ? date('Y-m-d H:i:s', strtotime('+1 hour')) 
            : date('Y-m-d H:i:s', strtotime("+$duration month"));

        $trojan_url_ws = "trojan://$password@{$server['domain']}:443?type=ws&security=tls&sni={$server['domain']}&path=/trojan-ws&encryption=none#trial-$username";
        $trojan_url_grpc = "trojan://$password@{$server['domain']}:433?mode=gun&security=tls&path=/trojan-grpc&encryption=none&type=grpc&serviceName=trojan-grpc&sni={$server['domain']}#trial-$username";

        $data = [
            'protocol' => 'trojan',
            'password' => $password,
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
        
       $sql = "INSERT INTO accounts (user_id, server_id, username, protocol, uuid, expiration_date, trojan_url_ws, trojan_url_grpc) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $protocol = 'trojan';
            $stmt->bind_param("iissssss", $user_id, $server_id, $username, $protocol, $password, $expiration_date, $trojan_url_ws, $trojan_url_grpc);
 
        

            if ($stmt->execute()) {
                $_SESSION['account_created_trojan'] = true;
                $_SESSION['created_account_data_trojan'] = [
                    'username' => $username,
                    'password' => $password,
                    'expiration_date' => $expiration_date,
                    'trojan_url_ws' => $trojan_url_ws,
                    'trojan_url_grpc' => $trojan_url_grpc
                ];

                header("Location: {$_SERVER['PHP_SELF']}?server_id=$server_id");
                exit;
            } else {
                $error_message = "Gagal menyimpan akun ke database: " . $stmt->error;
            }
        } else {
            $error_message = "Gagal membuat akun di server trojan: $response";
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
    <title>Buat Akun trojan</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
    /* General Body Styling */
    body {
        font-family: 'Roboto', sans-serif;
        background: #f9f9f9; /* Background cerah */
        color: #333; /* Teks gelap untuk kontras */
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Membuat body memenuhi layar */
        margin: 0;
    }

    /* Container Styling */
    .container {
        width: 100%;
        max-width: 500px; /* Perkecil lebar maksimum */
        background: #fff; /* Warna latar belakang cerah */
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        padding: 20px; /* Menambah padding */
        border: 1px solid #ddd; /* Border ringan */
    }

    /* Heading Styling */
    .judul-container {
        font-family: 'Poppins', sans-serif;
        font-size: 16px; /* Ukuran font yang lebih besar */
        font-weight: 600;
        color: #4e73df; /* Warna biru untuk judul */
        text-align: center;
        margin-bottom: 16px; /* Menambah margin bawah */
    }

    /* Balance Section */
    .balance {
        text-align: center;
        font-size: 14px;
        color: #555;
        margin-top: 8px;
        margin-bottom: 20px; /* Jarak lebih besar */
    }

    /* Form Group */
    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #555;
        margin-bottom: 6px;
        font-size: 14px; /* Ukuran font label lebih besar */
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f3f3f3;
        color: #333;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 4px rgba(78, 115, 223, 0.5);
        outline: none;
    }

    /* Submit Button Styling */
    .btn {
        display: block;
        width: 100%;
        padding: 10px;
        border: none;
        background: linear-gradient(90deg, #4e73df, #375ac9);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-top: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        background: linear-gradient(90deg, #375ac9, #4e73df);
    }

    /* Result Box Styling */
    .result {
        background: #fff;
        padding: 16px;
        border-radius: 6px;
        margin-top: 16px;
        color: #333;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .result h3 {
        font-size: 14px;
        color: #4e73df;
        margin-bottom: 12px;
    }

    h4 {
        font-size: 14px;
        color: #4e73df;
        margin-bottom: 8px;
    }

    .result p {
        font-size: 12px;
        color: #333;
    }

    .result textarea {
        width: 100%;
        padding: 8px;
        font-size: 12px;
        background: #f3f3f3;
        border: 1px solid #ddd;
        border-radius: 6px;
        color: #333;
        resize: none;
        transition: border-color 0.3s ease;
    }

    .result textarea:focus {
        border-color: #4e73df;
    }

    /* Copy Button Styling */
    .btn-copy {
        margin-top: 10px;
        background-color: #28a745;
        padding: 8px 12px;
        border: none;
        color: #fff;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-copy:hover {
        background-color: #218838;
    }

    /* Error Message */
    .error {
        color: #e74c3c;
        background: #fdecea;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 16px;
    }

    /* Error Message Styling */
    .error-message {
        color: #e74a3b;
        font-size: 14px;
        margin-top: 8px;
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
        border-top: 4px solid #4e73df;
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
</head>
<body>
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading"></div>
    </div>
    <div class="container">
        <div class="judul-container">
            <div class="balance">Saldo Anda: <?php echo number_format($saldo, 0, ',', '.'); ?> IDR</div>
            <h2>Buat Akun trojan</h2>
        </div>

        <?php if (isset($error_message)) : ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" onsubmit="showLoading();">
            <div class="form-group">
                <label for="username">username</label>
                <input type="text" name="username" id="username" required>
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

        <?php if (isset($_SESSION['account_created_trojan']) && $_SESSION['account_created_trojan']) : ?>
            <div class="result">
                <h3>Detail Akun trojan</h3>
                <p><strong>username:</strong> <?php echo $_SESSION['created_account_data_trojan']['username']; ?></p>
                <p><strong>password:</strong> <?php echo $_SESSION['created_account_data_trojan']['password']; ?></p>
                <p><strong>Expiry Date:</strong> <?php echo $_SESSION['created_account_data_trojan']['expiration_date']; ?></p>

                <h4>URL trojan ws:</h4>
                <textarea id="trojan_url_ws" readonly><?php echo $_SESSION['created_account_data_trojan']['trojan_url_ws']; ?></textarea>
                <button id="trojan_url_ws_btn" class="btn-copy" onclick="copyToClipboard('trojan_url_ws')">Salin URL</button>

                <h4>URL trojan gRpc:</h4>
                <textarea id="trojan_url_grpc" readonly><?php echo $_SESSION['created_account_data_trojan']['trojan_url_grpc']; ?></textarea>
                <button id="trojan_url_grpc_btn" class="btn-copy" onclick="copyToClipboard('trojan_url_grpc')">Salin URL</button>
            </div>
        <?php endif; ?>
    </div>

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
            });
        }
    </script>
</body>
</html>
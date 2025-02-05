
<?php
ob_start();
session_start();
include('../script/autoload.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit;
}

// Ambil ID akun dari parameter
if (!isset($_GET['account_id'])) {
    echo "ID akun tidak ditemukan.";
    exit;
}

$account_id = $_GET['account_id'];

// Jika halaman di-refresh setelah pengiriman, arahkan ke dashboard
if (isset($_SESSION['data_sent']) && $_SESSION['data_sent'] === true) {
    unset($_SESSION['data_sent']); // Reset status agar tidak terus-menerus redirect
    header("Location: dashboard");
    exit;
}


// Ambil saldo pengguna dari tabel users berdasarkan user_id yang sedang login
$user_id = $_SESSION['user_id'];
$user_query = "SELECT saldo FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("s", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $saldo = $user['saldo']; // Saldo pengguna dari tabel users
} else {
    echo "Saldo pengguna tidak ditemukan.";
    exit;
}

// Jika form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Periksa apakah data sudah pernah dikirim
    if (isset($_SESSION['data_sent']) && $_SESSION['data_sent'] === true) {
        echo "Data sudah dikirim sebelumnya.";
        exit;
    }

    $months_to_extend = $_POST['months'];

    // Ambil informasi akun berdasarkan ID
    $query = "SELECT a.*, s.ip_address, s.price
              FROM accounts a
              LEFT JOIN servers s ON a.server_id = s.id
              WHERE a.id = ? AND a.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $account_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $account = $result->fetch_assoc();

        $protocol = $account['protocol'];
        $uuid = $account['uuid'];
        $current_expiration_date = $account['expiration_date'];
        $server_ip = $account['ip_address'];
        $price_per_month = $account['price'];
        $username = $account['username'];

        // Hitung biaya perpanjangan
        $cost = $months_to_extend * $price_per_month;

        // Periksa saldo
        if ($saldo < $cost) {
            $error = "Saldo Anda tidak mencukupi untuk memperpanjangkan akun.";
        } else {
            // Mulai transaksi untuk memastikan perubahan konsisten
            $conn->begin_transaction();

            try {
                // Kurangi saldo pengguna
                $new_saldo = $saldo - $cost;
                $update_saldo_query = "UPDATE users SET saldo = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_saldo_query);
                $update_stmt->bind_param("is", $new_saldo, $user_id);
                $update_stmt->execute();

                // Hitung tanggal kadaluarsa baru
                $new_expiration_date = date('Y-m-d H:i:s', strtotime("+$months_to_extend months", strtotime($current_expiration_date)));

                // Update tanggal kadaluarsa di tabel accounts
                $update_expiration_query = "UPDATE accounts SET expiration_date = ? WHERE id = ?";
                $update_expiration_stmt = $conn->prepare($update_expiration_query);
                $update_expiration_stmt->bind_param("ss", $new_expiration_date, $account_id);
                $update_expiration_stmt->execute();

                // Kirim data ke server hanya setelah perubahan di database berhasil
                $data = [
                    "protocol" => $protocol,
                    "uuid" => $uuid,
                    "username" => $username, 
                    "expiration_date" => $new_expiration_date
                ];

                // Kirim ke server dengan timeout 10 detik
                $url = "http://$server_ip:5000/create-account";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 detik

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error_curl = curl_error($ch); // Ambil error dari curl jika ada
                curl_close($ch);

                if ($error_curl) {
                    // Jika terjadi kesalahan curl (seperti timeout), rollback transaksi
                    $conn->rollback();
                    $error = "Gagal menghubungi server: $error_curl";
                } elseif ($http_code === 200) {
                    // Commit transaksi jika server memberikan respons yang valid
                    $conn->commit();
                    $_SESSION['data_sent'] = true;  // Tandai data sudah dikirim
                    sleep(5);  // Simulasi delay
                    $success = "Akun berhasil diperpanjang. Saldo Anda dikurangi Rp $cost.";
                } else {
                    // Rollback transaksi jika server gagal merespons dengan sukses
                    $conn->rollback();
                    $error = "Gagal memperpanjangkan akun. Respons server: $response";
                }
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan dalam proses
                $conn->rollback();
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    } else {
        $error = "Akun tidak ditemukan.";
    }
} else {
    // Ambil informasi akun berdasarkan ID
    $query = "SELECT a.*, s.ip_address 
              FROM accounts a
              LEFT JOIN servers s ON a.server_id = s.id
              WHERE a.id = ? AND a.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $account_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Akun tidak ditemukan.";
        exit;
    }

    $account = $result->fetch_assoc();
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjang Akun</title>
    
            <style>
        /* CSS untuk tampilan perpanjangan akun */
        /* ... [Tetap sama dengan yang sudah ada] */
        
        body {
            background-color: #ffffff;
            color: #555; /* Menjadi lebih cerah */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px; /* Ukuran font diperkecil */
        }

        .container {
            width: 100%;
            max-width: 380px;
            margin: 60px auto; /* Jarak atas container diperkecil */
            padding: 15px; /* Padding diperketat */
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        body.dark-mode .container {
            background-color: #3A444F;
        }

        h1 {
            text-align: center;
            font-size: 18px; /* Ukuran font judul diperkecil */
            margin-bottom: 10px;
            color: #2980b9; /* Lebih cerah */
        }

        body.dark-mode h1 {
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 10px; /* Margin diperketat */
        }

        .form-group label {
            display: block;
            font-size: 10px; /* Ukuran font label diperkecil */
            margin-bottom: 5px;
            color: #3498db; /* Lebih cerah */
        }

        body.dark-mode .form-group label {
            color: #3498db; /* Cerahkan label di dark mode */
        }

        .form-group input,
        .form-group select,
        .perpanjang-button {
            width: 100%;
            padding: 10px; /* Padding diperketat */
            font-size: 12px; /* Ukuran font input dan tombol diperkecil */
            border-radius: 6px; /* Border radius diperkecil */
            border: 1px solid #bdc3c7;
            background-color: #ecf0f1;
            transition: background-color 0.3s ease;
            color: #333;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .perpanjang-button {
            background-color: #4f5b62;
            color: #ffffff;
        }

        .form-group input:disabled,
        .form-group select:disabled {
            background-color: #bdc3c7;
            color: #7f8c8d;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .perpanjang-button {
            background-color: #3498db;
            color: #ffffff;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-weight: bold;
            margin-top: 15px; /* Margin atas tombol diperkecil */
            font-size: 13px; /* Ukuran font tombol diperkecil */
        }

        body.dark-mode .perpanjang-button {
            background-color: #2980b9;
        }

        .perpanjang-button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .perpanjang-button:active {
            transform: scale(1);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .loading {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        .card {
            background-color: #ffffff;
            padding: 15px; /* Padding diperketat */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px; /* Margin atas diperkecil */
        }

        body.dark-mode .card {
            background-color: #2F3B46;
            border-color: #3E4A58;
        }

        .card-header {
            background-color: #3498db;
            padding: 8px; /* Padding diperketat */
            color: #ffffff;
            font-weight: bold;
            border-radius: 8px;
        }

        .card-body {
            padding: 8px; /* Padding diperketat */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 8px; /* Padding diperketat */
            border: 1px solid #bdc3c7;
            text-align: left;
            font-size: 12px; /* Ukuran font tabel diperkecil */
        }

        .table th {
            background-color: #3498db;
            color: #ffffff;
        }

        .table td {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 600px) {
            .container {
                width: 90%;
            }
        }

        /* Menambahkan garis horizontal di bawah tombol perpanjang */
        .separator {
            margin-top: 15px;
            border-top: 2px solid #3498db;
        }
    </style>
    
        
    
</head>
<body>
    <div class="container">
        <div id="loading-overlay" class="loading-overlay">
            <div class="loading"></div>
        </div>

        <h1>Perpanjang Akun</h1>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p style='color: green;'>$success</p>"; } ?>

        <div class="form-group">
            <label>Saldo Anda:</label>
            <input type="text" value="Rp <?= number_format($saldo, 0, ',', '.') ?>" disabled>
        </div>

        <form action="../user/perpanjang.php?account_id=<?= htmlspecialchars($account_id) ?>" method="POST" onsubmit="showLoading()">
            <div class="form-group">
                <label>UUID:</label>
                <input type="text" value="<?= htmlspecialchars($account['uuid']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Protokol:</label>
                <input type="text" value="<?= htmlspecialchars($account['protocol']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal Kadaluarsa Saat Ini:</label>
                <input type="text" value="<?= htmlspecialchars($account['expiration_date']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Jumlah Bulan Perpanjangan:</label>
                <select name="months" required>
                    <option value="1">1 Bulan(Rp 10.000)</option>
                    <option value="2">2 Bulan(Rp 20.000)</option>
                    <option value="3">3 Bulan(Rp 30.000)</option>
                    <option value="4">4 Bulan(Rp 40.000)</option>
                </select>
            </div>
            <button type="submit" class="perpanjang-button">Perpanjang</button>
        </form>

        <div class="separator"></div>

    </div>

    <script>
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }
    </script>
</body>
</html>


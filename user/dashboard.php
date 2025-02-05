<?php
ob_start();
session_start();
include('../script/autoload.php');
include('pemberitahuan.html');

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit();
}

$user_id = $_SESSION['user_id']; // Mengambil ID pengguna yang login

// Mengambil data pengguna
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Mengambil data akun pengguna
$query_accounts = "SELECT * FROM accounts WHERE user_id = ?";
$stmt_accounts = $conn->prepare($query_accounts);
$stmt_accounts->bind_param("i", $user_id);
$stmt_accounts->execute();
$result_accounts = $stmt_accounts->get_result();

// Mengambil daftar server yang digunakan
$query_servers = "SELECT * FROM servers WHERE id IN (SELECT server_id FROM accounts WHERE user_id = ?)";
$stmt_servers = $conn->prepare($query_servers);
$stmt_servers->bind_param("i", $user_id);
$stmt_servers->execute();
$result_servers = $stmt_servers->get_result();

// Mengambil Payment Gateway Aktif
$query_pg = "SELECT * FROM payment_gateways WHERE status = 'active'";
$stmt_pg = $conn->prepare($query_pg);
$stmt_pg->execute();
$result_pg = $stmt_pg->get_result();

$active_gateways = [];
while ($gateway = $result_pg->fetch_assoc()) {
    $active_gateways[] = $gateway;
}

$query = "SELECT uuid, server_id, expiration_date FROM accounts WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$active_accounts = 0;
$expired_accounts = 0;

// Menghitung jumlah akun aktif dan kadaluarsa
while ($account = $result->fetch_assoc()) {
    $expiration_date = new DateTime($account['expiration_date']);
    $current_date = new DateTime();
    if ($expiration_date > $current_date) {
        $active_accounts++;
    } else {
        $expired_accounts++;
    }
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <!-- Menambahkan font modern -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Body Styling */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            font-size: 12px; /* Memperkecil ukuran font */
            margin-top: 40px;
            box-sizing: border-box; /* Agar padding dan border tidak mempengaruhi lebar elemen */
            transition: background-color 0.3s, color 0.3s;
        }

        /* Container */
        .container {
            max-width: 1200px; /* Batasi lebar maksimal kontainer */
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box; /* Agar padding tidak mempengaruhi lebar */
            transition: background-color 0.3s, color 0.3s;
        }

        h1 {
            font-size: 20px; /* Memperkecil ukuran judul */
            color: #2c3e50;
            margin-bottom: 15px;
            transition: color 0.3s;
        }

        hr {
            border: 0;
            border-top: 2px solid #3498db; /* Memperkecil ketebalan garis */
            margin: 15px 0;
        }

        /* Kartu Statistik */
        .stat-card {
            display: inline-block;
            margin-right: 10px; /* Mengurangi margin kanan */
            background-color: #ffffff;
            border-radius: 8px; /* Menurunkan radius sudut */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            font-size: 14px; /* Memperkecil ukuran font dalam kartu */
            max-width: 100%; /* Pastikan kartu tidak melampaui lebar kontainer */
            box-sizing: border-box;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            font-size: 12px; /* Memperkecil ukuran font header */
            font-weight: 600;
            color: #3498db;
            margin-bottom: 5px;
            transition: color 0.3s;
        }

        .stat-value {
            font-size: 16px; /* Memperkecil ukuran angka */
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-card i {
            font-size: 20px; /* Memperkecil ukuran ikon */
            margin-right: 8px;
        }

        /* Daftar Akun dan Server */
        .card {
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 12px; /* Memperkecil font */
            max-width: 100%; /* Pastikan kartu tidak melampaui lebar kontainer */
            box-sizing: border-box;
            transition: background-color 0.3s, color 0.3s;
        }

        .card-header {
            font-size: 14px; /* Memperkecil ukuran judul card */
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            transition: color 0.3s;
        }

        .card-content ul {
            list-style-type: none;
            padding: 0;
        }

        .card-content li {
            margin-bottom: 8px; /* Memperkecil jarak antar list */
        }

        .card-content li span {
            font-weight: 600;
            color: #7f8c8d;
        }

        /* Tombol */
        .btn {
            display: inline-block;
            padding: 8px 15px; /* Memperkecil padding tombol */
            border-radius: 4px; /* Menurunkan radius tombol */
            text-align: center;
            font-size: 12px; /* Memperkecil ukuran font tombol */
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-success {
            background-color: #1abc9c;
            color: white;
        }

        .btn-warning {
            background-color: #f39c12;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Responsivitas */
        @media (max-width: 768px) {
            .stat-card {
                width: 100%; /* Membuat kartu statistik menjadi penuh pada layar kecil */
                margin-bottom: 10px;
            }

            .container {
                padding: 15px; /* Memperkecil padding container */
            }
        }

        /* Mode Gelap */
        body.dark-mode {
            background-color: #2c3e50;
            color: #ecf0f1;
        }

        body.dark-mode .container {
            background-color: #34495e;
        }

        body.dark-mode h1 {
            color: #ecf0f1;
        }

        body.dark-mode .stat-card {
            background-color: #2c3e50;
            color: #ecf0f1;
        }

        body.dark-mode .stat-header {
            color: #3498db;
        }

        body.dark-mode .stat-value {
            color: #ecf0f1;
        }

        body.dark-mode .card {
            background-color: #34495e;
            color: #ecf0f1;
        }

        body.dark-mode .card-header {
            color: #ecf0f1;
        }

        body.dark-mode .card-content li span {
            color: #bdc3c7;
        }

        body.dark-mode .btn {
            color: #ecf0f1;
        }

        body.dark-mode .btn-success {
            background-color: #16a085;
        }

        body.dark-mode .btn-warning {
            background-color: #e67e22;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Pengguna</h1>
        <hr>

        <!-- Statistik Akun Aktif -->
        <div class="stat-card">
            <div class="stat-header">Akun Aktif</div>
            <div class="stat-value">
                <i class="fas fa-users"></i> <?php echo $active_accounts; ?>
            </div>
        </div>

        <!-- Statistik Saldo -->
        <div class="stat-card">
            <div class="stat-header">Saldo</div>
            <div class="stat-value">
                <i class="fas fa-wallet"></i> Rp <?php echo number_format($user['saldo'], 0, ',', '.'); ?>
            </div>
            <div>
                <?php foreach ($active_gateways as $gateway): ?>
                    <a href="../pembayaran/<?php echo strtolower($gateway['name']); ?>/topup" class="btn btn-success">
                        <i class="fas fa-arrow-up"></i> Top Up
                    </a>
                <?php endforeach; ?>
                	





<div id="redeemButton" style="padding: 7px 17px; background-color: #4CAF50; color: white; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; display: inline-flex; align-items: center; justify-content: center;" onclick="showCouponForm()">
    <i class="fa fa-gift" style="margin-right: 5px;"></i> Redeem
</div>

<div id="couponForm" style="display: none; margin-top: 20px; text-align: center;">
    <form action="reedem" method="POST" style="max-width: 400px; margin: 0 auto;">
        <input type="text" name="kode_kupon" placeholder="Masukkan Kode Kupon" required 
            style="padding: 10px; width: 100%; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px; box-sizing: border-box;">
        <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">Verifikasi Kupon</button>
    </form>
</div>

<script>
    function showCouponForm() {
        var form = document.getElementById('couponForm');
        // Menampilkan form kupon jika tombol Redeem ditekan
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none"; // Sebagai alternatif, bisa disembunyikan jika ingin toggle
        }
    }
</script>


                </a>
            </div>
        </div>

        <!-- Daftar Akun -->
        <div class="card">
            <div class="card-header">Akun Anda</div>
            <div class="card-content">
                <ul>
                    <?php while ($account = $result_accounts->fetch_assoc()): ?>
                        <li>
                            <span>Username:</span> <?php echo $account['username']; ?><br>
                            <span>UUID:</span> <?php echo $account['uuid']; ?><br>
                            <span>Server:</span> <?php echo $account['server_id']; ?><br>
                            <span>Kadaluarsa:</span> <?php echo date("d-m-Y H:i:s", strtotime($account['expiration_date'])); ?><br>
                            <span>Status:</span> 
                            <?php 
                                $expiration_date = new DateTime($account['expiration_date']);
                                $current_date = new DateTime();
                                echo ($expiration_date > $current_date) ? 'Aktif' : 'Kadaluarsa';
                            ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Daftar Server -->
        <div class="card">
            <div class="card-header">Server Anda</div>
            <div class="card-content">
                <ul>
                    <?php while ($server = $result_servers->fetch_assoc()): ?>
                        <li>
                            <span>Nama Server:</span> <?php echo $server['server_name']; ?><br>
                            <span>Negara:</span> <?php echo $server['country']; ?><br>
                            <span>IP:</span> <?php echo $server['ip_address']; ?><br>
                            <span>Domain:</span> <?php echo $server['domain']; ?><br>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
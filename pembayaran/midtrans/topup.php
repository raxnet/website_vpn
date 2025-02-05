<?php
session_start();
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';
require_once '../../layout/header.php';
require_once '../../user/navbar.php';
require_once '../../layout/footer.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pastikan koneksi ke database berhasil
if (!$conn) {
    die("Koneksi database gagal.");
}

// Ambil informasi pengguna
$query = "SELECT * FROM users WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan.");
}

// Ambil kunci API Midtrans
$query = "SELECT * FROM midtrans_key WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$midtrans_key = $stmt->get_result()->fetch_assoc();

if (!$midtrans_key) {
    die("API key Midtrans tidak ditemukan.");
}

// Set konfigurasi Midtrans
\Midtrans\Config::$serverKey = $midtrans_key['server_key'];
\Midtrans\Config::$clientKey = $midtrans_key['client_key'];
\Midtrans\Config::$isProduction = false;  // Ubah ke true jika sudah live
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil site key Cloudflare dari database
$query = "SELECT site_key FROM cloudflare_captcha WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$captcha_data = $stmt->get_result()->fetch_assoc();

if (!$captcha_data) {
    die("Site key Cloudflare tidak ditemukan.");
}

$cloudflare_site_key = $captcha_data['site_key'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up Saldo</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script> 
    <style>
        /* CSS untuk TopUp */
        body {
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        /* Container Utama */
        .topup-container {
            background: #ffffff;
            padding: 24px;
            border-radius: 10px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .topup-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        /* Judul */
        .topup-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
        }

        /* Label Input */
        .topup-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
            text-align: left;
        }

        /* Input Field */
        .topup-input {
            width: 100%;
            padding: 14px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            color: #333;
            font-size: 14px;
            outline: none;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }

        .topup-input:focus {
            border-color: #4f46e5;
            background: #ffffff;
        }

        /* Tombol */
        .topup-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .topup-btn:hover {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            transform: scale(1.05);
        }

        .topup-btn:active {
            transform: scale(0.98);
        }

        /* Syarat & Ketentuan */
        .topup-terms {
            font-size: 13px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 8px;
            transition: max-height 0.3s ease-out;
            overflow: hidden;
            max-height: 38px;
            cursor: pointer;
        }

        .topup-terms:hover {
            max-height: 120px;
        }

        .topup-terms-title {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        .topup-terms-text {
            margin-top: 6px;
            display: none;
        }

        .topup-terms:hover .topup-terms-text {
            display: block;
        }

        /* Cloudflare CAPTCHA */
        .cf-turnstile {
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="topup-container">
        <h2 class="topup-title">Top Up Saldo</h2>
        <form id="topup-form" method="post" action="topup_process.php">
            <label for="amount" class="topup-label">Jumlah Top Up:</label>
            <input type="number" name="amount" id="amount" class="topup-input" required min="10000" placeholder="Minimal Rp 10.000">

            <!-- Cloudflare Turnstile CAPTCHA -->
            <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($cloudflare_site_key) ?>"></div>

            <button type="submit" class="topup-btn">Lanjutkan</button>
        </form>

        <!-- Syarat & Ketentuan -->
        <div class="topup-terms">
            <div class="topup-terms-title">⚠ Syarat & Ketentuan</div>
            <div class="topup-terms-text">
                - Minimal top-up Rp 10.000.<br>
                - Saldo tidak bisa diuangkan kembali.<br>
                - Proses top-up maksimal 5 menit.<br>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('topup-form').addEventListener('submit', function(event) {
            var amount = document.getElementById('amount').value;
            if (amount < 10000) {
                alert("Minimal top-up adalah Rp 10.000.");
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
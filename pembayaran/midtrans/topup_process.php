<?php
session_start();
require_once '../../config/config.php';  // Pastikan ini mengarah pada file konfigurasi
require_once '../../vendor/autoload.php';  // Untuk autoload library Midtrans

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil jumlah top-up dari form
$amount = $_POST['amount'];

// Validasi input
if ($amount < 10000) {
    die("Jumlah top-up minimal Rp 10.000");
}

// Ambil informasi pengguna
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan.");
}

// Ambil kunci API Midtrans dari tabel midtrans_key
$query = "SELECT * FROM midtrans_key WHERE id = 1";
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

// Buat transaksi top-up dengan Midtrans
$order_id = 'ORDER-' . time();
$transaction_details = array(
    'order_id' => $order_id,
    'gross_amount' => $amount,
);

$customer_details = array(
    'first_name' => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'],
);

$payment_type = \Midtrans\Snap::createTransaction([
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
]);

// Simpan transaksi ke database
$query = "INSERT INTO transaksi (user_id, amount, status, order_id) VALUES (?, ?, 'pending', ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $user_id, $amount, $order_id);
$stmt->execute();

// Arahkan ke halaman Midtrans untuk menyelesaikan pembayaran
header("Location: " . $payment_type->redirect_url);
exit;
?>
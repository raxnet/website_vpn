<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';  // Untuk autoload library Midtrans

// Ambil data notifikasi dari Midtrans
$payload = file_get_contents("php://input");
$notif = json_decode($payload);

if (!$notif) {
    file_put_contents('midtrans_log.txt', "Notifikasi kosong!\n", FILE_APPEND);
    die("Invalid notification");
}

// Ambil data yang dikirim oleh Midtrans
$order_id = $notif->order_id ?? '';
$status_code = $notif->transaction_status ?? '';
$trans_id = $notif->transaction_id ?? '';
$gross_amount = $notif->gross_amount ?? 0;
$payment_type = $notif->payment_type ?? '';

// Simpan log untuk debugging
file_put_contents('midtrans_log.txt', "Order ID: $order_id | Status: $status_code | Trans ID: $trans_id | Amount: $gross_amount | Payment Type: $payment_type\n", FILE_APPEND);

if (empty($order_id)) {
    die("Order ID kosong, tidak dapat diproses.");
}

// Proses pembaruan status transaksi di database
if ($status_code == 'settlement') {
    // Pembayaran berhasil, update status transaksi di database
    $query = "UPDATE transaksi SET status = 'completed', payment_method = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $payment_type, $order_id);  // Menambahkan payment_type

    if ($stmt->execute()) {
        file_put_contents('midtrans_debug.txt', "Order $order_id status updated to completed with payment method $payment_type.\n", FILE_APPEND);
    } else {
        file_put_contents('midtrans_debug.txt', "Gagal update status: " . $stmt->error . "\n", FILE_APPEND);
    }

    // Update saldo pengguna berdasarkan transaksi
    $query = "UPDATE users SET saldo = saldo + (SELECT amount FROM transaksi WHERE order_id = ?) WHERE id = (SELECT user_id FROM transaksi WHERE order_id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $order_id, $order_id);

    if ($stmt->execute()) {
        file_put_contents('midtrans_debug.txt', "Saldo user untuk order $order_id berhasil diperbarui.\n", FILE_APPEND);
    } else {
        file_put_contents('midtrans_debug.txt', "Gagal update saldo: " . $stmt->error . "\n", FILE_APPEND);
    }
} elseif ($status_code == 'expired') {
    // Pembayaran expired
    $query = "UPDATE transaksi SET status = 'expired', payment_method = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $payment_type, $order_id);  // Menambahkan payment_type
    $stmt->execute();
    file_put_contents('midtrans_debug.txt', "Order $order_id expired with payment method $payment_type.\n", FILE_APPEND);
} elseif ($status_code == 'cancel') {
    // Pembayaran dibatalkan
    $query = "UPDATE transaksi SET status = 'cancelled', payment_method = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $payment_type, $order_id);  // Menambahkan payment_type
    $stmt->execute();
    file_put_contents('midtrans_debug.txt', "Order $order_id cancelled with payment method $payment_type.\n", FILE_APPEND);
}

// Kirim response ke Midtrans
echo 'OK';
?>
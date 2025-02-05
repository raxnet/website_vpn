<?php
// Include koneksi database
include('../admin/app/autoload.php'); 

// Cek apakah ada permintaan untuk edit status
if (isset($_POST['edit_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Validasi untuk menghindari dua gateway aktif secara bersamaan
    $status_query = "SELECT * FROM payment_gateways WHERE status = 'active' AND id != $id";
    $status_result = mysqli_query($conn, $status_query);
    if (mysqli_num_rows($status_result) > 0 && $status == 'active') {
        $warning_message = "Warning: Hanya satu payment gateway yang dapat diaktifkan pada satu waktu.";
    } else {
        // Update status gateway di tabel payment_gateways
        $update_query = "UPDATE payment_gateways SET status = '$status' WHERE id = $id";
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Status berhasil diperbarui!";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// Cek jika ada permintaan edit data terkait dari gateway yang sesuai (misalnya, Duitku atau Midtrans)
if (isset($_POST['edit_gateway'])) {
    $payment_gateway_id = $_POST['payment_gateway_id'];
    $merchant_id = $_POST['merchant_id'];
    $client_key = $_POST['client_key'];
    $server_key = $_POST['server_key'];
    $gateway_type = $_POST['gateway_type']; // 'midtrans' or 'duitku'

    // Update data di tabel yang sesuai berdasarkan tipe gateway
    if ($gateway_type == 'midtrans') {
        $update_query = "UPDATE midtrans_key SET merchant_id = '$merchant_id', client_key = '$client_key', server_key = '$server_key' WHERE id = $payment_gateway_id";
    } else if ($gateway_type == 'duitku') {
        $update_query = "UPDATE duitku_key SET merchant_id = '$merchant_id', client_key = '$client_key', server_key = '$server_key' WHERE id = $payment_gateway_id";
    }
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Data gateway berhasil diperbarui!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Query untuk mengambil data dari tabel payment_gateways
$query = "SELECT * FROM payment_gateways";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateways</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            padding: 25px;
            background-color: transparent;
            border: none;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }
        .success {
            background-color: #4CAF50;
            color: white;
        }
        .error {
            background-color: #f44336;
            color: white;
        }
        .warning {
            background-color: #ff9800;
            color: white;
        }
        .payment-gateway-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            font-size: 14px;
        }
        .payment-gateway-table th, .payment-gateway-table td {
            padding: 12px 20px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .payment-gateway-table th {
            background-color: #f4f4f4;
        }
        .payment-gateway-table td {
            background-color: #fff;
        }
        .payment-gateway-table td button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 13px;
        }
        .payment-gateway-table td button:hover {
            background-color: #45a049;
        }
        form input, form select, form button {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        form button {
            width: auto;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
        .edit-form {
            display: none;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Payment Gateways</h1>

    <?php
    // Menampilkan pesan peringatan jika ada
    if (isset($warning_message)) {
        echo "<div class='alert warning'>$warning_message</div>";
    }

    // Menampilkan pesan sukses jika ada
    if (isset($success_message)) {
        echo "<div class='alert success'>$success_message</div>";
    }

    // Menampilkan pesan error jika ada
    if (isset($error_message)) {
        echo "<div class='alert error'>$error_message</div>";
    }

    // Tampilkan tabel data payment gateways
    if ($result) {
        echo "<table class='payment-gateway-table'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $payment_gateway_id = $row['id'];

            // Cek jenis gateway (misalnya Midtrans atau Duitku)
            if ($row['name'] == 'Midtrans') {
                $midtrans_query = "SELECT * FROM midtrans_key WHERE id = $payment_gateway_id";
                $midtrans_result = mysqli_query($conn, $midtrans_query);
                $midtrans_data = mysqli_fetch_assoc($midtrans_result);
                $gateway_type = 'midtrans';
            } else if ($row['name'] == 'Duitku') {
                $duitku_query = "SELECT * FROM duitku_key WHERE id = $payment_gateway_id";
                $duitku_result = mysqli_query($conn, $duitku_query);
                $duitku_data = mysqli_fetch_assoc($duitku_result);
                $gateway_type = 'duitku';
            }

            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <!-- Form untuk edit status -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
                        <select name="status" required>
                            <option value="active" <?php echo ($row['status'] == 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo ($row['status'] == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                        <button type="submit" name="edit_status">Update</button>
                    </form>
                    <br>

                    <!-- Tombol Edit untuk Data Gateway -->
                    <button onclick="document.getElementById('editGateway<?php echo $payment_gateway_id; ?>').style.display='block'">Edit Gateway Data</button>
                    
                    <!-- Form Edit Gateway Key -->
                    <div id="editGateway<?php echo $payment_gateway_id; ?>" class="edit-form">
                        <form method="POST">
                            <input type="hidden" name="payment_gateway_id" value="<?php echo $payment_gateway_id; ?>">
                            <input type="hidden" name="gateway_type" value="<?php echo $gateway_type; ?>">
                            <label for="merchant_id">Merchant ID</label>
                            <input type="text" name="merchant_id" value="<?php echo ($gateway_type == 'midtrans' ? $midtrans_data['merchant_id'] : $duitku_data['merchant_id']); ?>" required><br>
                            <label for="client_key">Client Key</label>
                            <input type="text" name="client_key" value="<?php echo ($gateway_type == 'midtrans' ? $midtrans_data['client_key'] : $duitku_data['client_key']); ?>" required><br>
                            <label for="server_key">Server Key</label>
                            <input type="text" name="server_key" value="<?php echo ($gateway_type == 'midtrans' ? $midtrans_data['server_key'] : $duitku_data['server_key']); ?>" required><br>
                            <button type="submit" name="edit_gateway">Update Gateway Key</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php
        }
        echo "</table>";
    } else {
        echo "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>";
    }

    // Menutup koneksi
    mysqli_close($conn);
    ?>
</div>

</body>
</html>
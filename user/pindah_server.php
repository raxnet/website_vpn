<?php
session_start();
include('../script/autoload.php');

$account_id = $_GET['account_id'];

// Ambil data akun berdasarkan ID
$account_query = "SELECT * FROM accounts WHERE id = ?";
$account_stmt = $conn->prepare($account_query);
$account_stmt->bind_param("i", $account_id);
$account_stmt->execute();
$result = $account_stmt->get_result();

if ($result->num_rows === 0) {
    // Akun tidak ditemukan
    $error = "Akun dengan ID $account_id tidak ditemukan. Pastikan Anda menggunakan akun yang valid.";
    exitWithError($error);
}

$account = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_server_id = $_POST['server_id'];

    // Cek apakah server yang dipilih sama dengan server lama
    if ($account['server_id'] == $new_server_id) {
        $error = "Server yang dipilih sama dengan server lama. Silakan pilih server yang berbeda.";
        exitWithError($error);
    }

    // Ambil data server baru berdasarkan ID
    $server_query = "SELECT ip_address FROM servers WHERE id = ?";
    $server_stmt = $conn->prepare($server_query);
    $server_stmt->bind_param("i", $new_server_id);
    $server_stmt->execute();
    $server_result = $server_stmt->get_result();

    if ($server_result->num_rows > 0) {
        $new_server = $server_result->fetch_assoc();
        $new_server_ip = $new_server['ip_address'];

        // Cek apakah akun tersebut valid untuk pengguna yang login
        $check_account_query = "SELECT id FROM accounts WHERE id = ? AND user_id = ?";
        $check_account_stmt = $conn->prepare($check_account_query);
        $check_account_stmt->bind_param("ss", $account_id, $_SESSION['user_id']);
        $check_account_stmt->execute();
        $check_account_result = $check_account_stmt->get_result();

        if ($check_account_result->num_rows === 0) {
            $error = "Akun tidak ditemukan atau tidak valid untuk pengguna ini. Pastikan Anda memiliki akses ke akun tersebut.";
            exitWithError($error);
        }

        // Update server untuk akun
        $update_server_query = "UPDATE accounts SET server_id = ? WHERE id = ?";
        $update_server_stmt = $conn->prepare($update_server_query);
        $update_server_stmt->bind_param("ii", $new_server_id, $account_id);

        if ($update_server_stmt->execute()) {
            if ($update_server_stmt->affected_rows > 0) {
                $data = [
                    "protocol" => $account['protocol'],
                    "uuid" => $account['uuid'],
                    "username" => $account['username'],
                    "expiration_date" => $account['expiration_date']
                ];

                // Kirim data ke server baru untuk membuat akun
                $url = "http://$new_server_ip:5000/create-account";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code === 200) {
                    $success = "Akun berhasil dipindahkan ke server baru.";
                    echo $success;
                } else {
                    $error = "Gagal pindah server. Respons server: $response. Harap coba lagi nanti.";
                    exitWithError($error);
                }
            } else {
                $error = "Tidak ada perubahan pada server yang dipilih.";
                exitWithError($error);
            }
        } else {
            $error = "Gagal memperbarui informasi server. Error: " . $update_server_stmt->error;
            exitWithError($error);
        }
    } else {
        $error = "Server baru tidak ditemukan. Pastikan server yang dipilih valid.";
        exitWithError($error);
    }
}

// Fungsi untuk menangani error dan menampilkan pesan
function exitWithError($message) {
    // Log error untuk referensi lebih lanjut
    error_log("Error: " . $message);

    // Tampilkan pesan error kepada pengguna
    echo "<p><strong>Terjadi kesalahan:</strong> $message</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pindah Server</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5; /* Warna netral untuk latar belakang halaman */
            color: #333; /* Warna teks utama yang lebih gelap untuk kontras */
        }

        body.dark-mode {
            background-color: #2d2d2d; /* Warna latar belakang gelap */
            color: #f5f5f5; /* Warna teks terang untuk kontras */
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff; /* Warna putih netral pada container */
            padding: 20px 10px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4); /* Menambahkan sedikit bayangan untuk efek */
        }

        body.dark-mode .container {
            background-color: #3A444F; /* Warna latar belakang container dalam mode gelap */
        }

        h1 {
            text-align: center;
            font-size: 17px;
            color: #000; 
        }

   body.dark-mode h1 {
            color: #00ff33; 
        }
        
        

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555; /* Warna teks label yang lebih terang */
            margin-bottom: 5px;
        }

        body.dark-mode .form-group label {
            color: #ccc; /* Warna teks label dalam mode gelap */
        }

        .form-group select {
            width: 100%;
            padding: 8px;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 14px;
        }

        body.dark-mode .form-group select {
            background-color: #444; /* Warna latar belakang select dalam mode gelap */
            color: #f5f5f5; /* Warna teks select dalam mode gelap */
        }

        .server-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
            table-layout: fixed;
        }

        .server-table th, .server-table td {
            padding: 6px;
            border: 1px solid #ddd; /* Warna garis border lebih lembut */
            text-align: left;
            word-wrap: break-word;
        }

        .server-table th {
            background-color: #f0f0f0; /* Warna netral cerah pada header tabel */
        }

        body.dark-mode .server-table th {
            background-color: #444; /* Warna latar belakang header tabel dalam mode gelap */
        }

        body.dark-mode .server-table td {
            background-color: #333; /* Warna latar belakang tabel dalam mode gelap */
            color: #f5f5f5; /* Warna teks tabel dalam mode gelap */
        }

        .perpanjang-button {
            width: 100%;
            padding: 10px;
            background-color: #f5a623;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin :7px;
            margin-left: 0;
        }

        body.dark-mode .perpanjang-button {
            background-color: #f5a623; /* Warna tombol tetap sama dalam mode gelap */
        }

        .perpanjang-button:hover {
            background-color: #d88a18;
        }

        body.dark-mode .perpanjang-button:hover {
            background-color: #f5a623; /* Warna hover tetap sama dalam mode gelap */
        }

        .feedback {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .feedback.success {
            color: #4caf50;
        }

        body.dark-mode .feedback.success {
            color: #81c784; /* Warna sukses lebih terang dalam mode gelap */
        }

        .feedback.error {
            color: #f44336;
        }

        body.dark-mode .feedback.error {
            color: #e57373; /* Warna error lebih terang dalam mode gelap */
        }

        .scrollable-url {
            display: block;
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 5px;
            background-color: #222;
            border-radius: 5px;
        }

        body.dark-mode .scrollable-url {
            background-color: #444; /* Warna latar belakang URL dalam mode gelap */
        }

        .scrollable-url.visible {
            display: block;
            overflow: auto;
            white-space: normal;
        }

        .scrollable-url:hover {
            background-color: #333;
        }

        body.dark-mode .scrollable-url:hover {
            background-color: #555; /* Warna hover URL lebih terang dalam mode gelap */
        }

        .scrollable-url::-webkit-scrollbar {
            height: 6px;
        }

        .scrollable-url::-webkit-scrollbar-thumb {
            background: #f5a623;
            border-radius: 3px;
        }

        body.dark-mode .scrollable-url::-webkit-scrollbar-thumb {
            background: #ff6347; /* Warna scrollbar lebih terang dalam mode gelap */
        }

        .scrollable-url::-webkit-scrollbar-track {
            background: #222;
        }

        body.dark-mode .scrollable-url::-webkit-scrollbar-track {
            background: #444; /* Warna track scrollbar lebih gelap dalam mode gelap */
        }
        
        .garis {
    border: none;
    height: 4px; /* Menentukan ketebalan garis */
    background-color: #3498db; /* Menentukan warna garis */
    width: 100%; /* Menentukan panjang garis */
    margin: 20px auto; /* Menentukan jarak atas-bawah dan tengah garis */
}

body.dark-mode .garis {
            background: #fff; 
        }


    </style>
</head>
<body>
    <div class="container">
        <h1>Pindah Server</h1>
        <hr class="garis">
        <?php if (isset($error)) { echo "<p class='feedback error'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='feedback success'>$success</p>"; } ?>

        <h3></h3>
        <table class="server-table">
            <thead>
                <tr>
                    <th>Kolom</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>UUID</td>
                    <td><?= htmlspecialchars($account['uuid']) ?></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><?= htmlspecialchars($account['username']) ?></td>
                </tr>
                <tr>
                    <td>Protokol</td>
                    <td><?= htmlspecialchars($account['protocol']) ?></td>
                </tr>
                <tr>
                    <td>Server</td>
                    <td><?= htmlspecialchars($account['server_name']) ?></td>
                </tr>
                <tr>
                    <td>IP Address</td>
                    <td><?= htmlspecialchars($account['ip_address']) ?></td>
                </tr>
                <tr>
                    <td>Domain</td>
                    <td><?= htmlspecialchars($account['domain']) ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?= htmlspecialchars($account['status']) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Kedaluwarsa</td>
                    <td><?= htmlspecialchars($account['expiration_date']) ?></td>
                </tr>

                <?php if ($account['protocol'] === 'vmess') : ?>
                    <tr>
                        <td>VMess URL TLS</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['vmess_url_tls']) ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>VMess URL Non-TLS</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['vmess_url_non_tls']) ?>
                            </div>
                        </td>
                    </tr>
                <?php elseif ($account['protocol'] === 'vless') : ?>
                    <tr>
                        <td>VLess URL TLS</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['vless_url_tls']) ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>VLess URL Non-TLS</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['vless_url_non_tls']) ?>
                            </div>
                        </td>
                    </tr>
                <?php elseif ($account['protocol'] === 'trojan') : ?>
                    <tr>
                        <td>Trojan URL WS</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['trojan_url_ws']) ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Trojan URL gRPC</td>
                        <td>
                            <div class="scrollable-url">
                                <?= htmlspecialchars($account['trojan_url_grpc']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <form action="../user/pindah_server.php?account_id=<?= htmlspecialchars($account_id) ?>" method="POST">
            <div class="form-group">
                <label>Pilih Server Baru:</label>
                <select name="server_id" required>
                    <option value="">-- Pilih Server --</option>
                    <?php
                    $server_query = "SELECT id, server_name, ip_address, domain, status FROM servers";
                    $server_result = $conn->query($server_query);
                    while ($server = $server_result->fetch_assoc()) {
                        echo "<option value='{$server['id']}'>{$server['server_name']} ({$server['ip_address']})</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="perpanjang-button">Pindah Server</button>
        </form>
    </div>

    <script>
        // Menambahkan event listener untuk setiap elemen yang memiliki class 'scrollable-url'
        document.querySelectorAll('.scrollable-url').forEach(function (urlElement) {
            urlElement.addEventListener('click', function () {
                // Toggle visibilitas URL saat diklik
                this.classList.toggle('visible');
            });
        });
    </script>
</body>
</html>
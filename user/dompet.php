<?php
// Memulai session dan memastikan user sudah login
session_start();

// Memeriksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Koneksi ke database
include '../config/config.php';
include '../layout/footer.php';
include '../layout/header.php';
include '../user/navbar.php';

// Ambil saldo pengguna dari tabel 'users'
$query = "SELECT saldo FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil riwayat transaksi dari tabel 'transaksi' untuk pengguna yang sedang login
$transaksiQuery = "SELECT * FROM transaksi WHERE user_id = ? ORDER BY created_at DESC LIMIT 20"; // Batasi transaksi
$transaksiStmt = $conn->prepare($transaksiQuery);
$transaksiStmt->bind_param('i', $_SESSION['user_id']);
$transaksiStmt->execute();
$transaksiResult = $transaksiStmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dompet Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/socket.io-client/dist/socket.io.min.js"></script> <!-- Socket.io for real-time notifications -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            padding-top: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-top: 10px;
            font-size: 1em;
        }
        .saldo {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #1abc9c;
            color: white;
            padding: 8px 15px;
            margin: 0 4%;
            border-radius: 6px;
            font-size: 14px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        table {
            width: 80%;
            margin: 15px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 0.8em;
        }
        th {
            background-color: #f1f1f1;
            font-weight: 500;
        }
        tr:hover td {
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            padding: 6px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-container input, .filter-container select {
            padding: 5px 10px;
            margin: 5px;
            font-size: 0.9em;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .notif-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            max-width: 300px;
        }
        .notification {
            background-color: #3498db;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: fadeIn 3s forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

<h2>Dompet Saya</h2>

<div class="saldo">
    <h3>Saldo Anda: Rp <?= number_format($user['saldo'], 0, ',', '.') ?></h3>
</div>

<!-- Filter dan Pencarian -->
<div class="filter-container">
    <input type="text" id="search-transaksi" placeholder="Cari Transaksi..." onkeyup="searchTransaksi()">
    <select id="filter-date" onchange="filterByDate()">
        <option value="">Filter Berdasarkan Tanggal</option>
        <option value="week">Minggu Ini</option>
        <option value="month">Bulan Ini</option>
        <option value="year">Tahun Ini</option>
    </select>
</div>

<!-- Grafik Riwayat Transaksi -->
<div class="chart-container">
    <canvas id="transaksiChart"></canvas>
</div>

<!-- Riwayat Transaksi -->
<table id="transaksi-table">
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>Jumlah</th>
            <th>Metode Pembayaran</th>
            <th>Status</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($transaksi = $transaksiResult->fetch_assoc()) { ?>
            <tr class="transaction-row">
                <td><?= $transaksi['order_id'] ?></td>
                <td>Rp <?= number_format($transaksi['amount'], 0, ',', '.') ?></td>
                <td><?= $transaksi['payment_method'] ?: 'N/A' ?></td>
                <td><?= ucfirst($transaksi['status']) ?></td>
                <td><?= date("d-m-Y H:i", strtotime($transaksi['created_at'])) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Button -->
<a href="#" class="btn">Tindakan Lain</a>

<!-- Notifikasi Real-Time -->
<div class="notif-container" id="notif-container"></div>

<script>
    // Menampilkan Grafik Riwayat Transaksi
    const ctx = document.getElementById('transaksiChart').getContext('2d');
    const transaksiChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php
                $dates = [];
                $transaksiResult->data_seek(0);
                while ($row = $transaksiResult->fetch_assoc()) {
                    $dates[] = '"' . date("d-m-Y", strtotime($row['created_at'])) . '"';
                }
                echo implode(',', $dates);
            ?>],
            datasets: [{
                label: 'Jumlah Transaksi (Rp)',
                data: [<?php
                    $amounts = [];
                    $transaksiResult->data_seek(0);
                    while ($row = $transaksiResult->fetch_assoc()) {
                        $amounts[] = $row['amount'];
                    }
                    echo implode(',', $amounts);
                ?>],
                borderColor: 'rgba(0, 123, 255, 0.6)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true }
            }
        }
    });

    // WebSocket untuk Notifikasi Real-Time
    const socket = io('../../pembayaran/midtrans_notification'); // Ganti dengan URL server Anda
    socket.on('new_transaction', function(data) {
        showNotification(data.message);
    });

    // Menampilkan Notifikasi
    function showNotification(message) {
        const notifContainer = document.getElementById('notif-container');
        const notif = document.createElement('div');
        notif.classList.add('notification');
        notif.textContent = message;
        notifContainer.appendChild(notif);
        setTimeout(() => notif.remove(), 5000);
    }

    // Fungsi pencarian transaksi
    function searchTransaksi() {
        let input = document.getElementById('search-transaksi').value.toLowerCase();
        let rows = document.querySelectorAll('#transaksi-table tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }

    // Fungsi filter berdasarkan tanggal
    function filterByDate() {
        let filter = document.getElementById('filter-date').value;
        let rows = document.querySelectorAll('#transaksi-table tbody tr');
        let today = new Date();
        let filterDate = new Date();
        rows.forEach(row => {
            let transactionDate = new Date(row.cells[4].textContent); // Mengambil tanggal transaksi
            let showRow = false;

            switch (filter) {
                case 'week':
                    filterDate.setDate(today.getDate() - 7);
                    showRow = transactionDate >= filterDate;
                    break;
                case 'month':
                    filterDate.setMonth(today.getMonth() - 1);
                    showRow = transactionDate >= filterDate;
                    break;
                case 'year':
                    filterDate.setFullYear(today.getFullYear() - 1);
                    showRow = transactionDate >= filterDate;
                    break;
                default:
                    showRow = true;
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Mengupdate grafik secara real-time (opsional)
    socket.on('update_chart', function(data) {
        transaksiChart.data.labels = data.labels;
        transaksiChart.data.datasets[0].data = data.amounts;
        transaksiChart.update();
    });
</script>

</body>
</html>
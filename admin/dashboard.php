<?php
// Koneksi ke database
include('../admin/app/autoload.php');

// Pastikan koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM accounts) AS total_accounts,
        (SELECT COUNT(*) FROM transaksi) AS total_transactions,
        (SELECT COUNT(*) FROM servers) AS total_servers
";

$result = mysqli_query($conn, $query);

// Pastikan query berhasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($result);

// Pastikan data tidak kosong
$total_users = $data['total_users'] ?? 0;
$total_accounts = $data['total_accounts'] ?? 0;
$total_transactions = $data['total_transactions'] ?? 0;
$total_servers = $data['total_servers'] ?? 0;
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Statistik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Warna terang */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            max-width: 700px;
            margin: 30px auto;
            padding: 15px;
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
        }
        body.dark-mode h1 { 
            color: #ffffff;
        }
        .stats-box { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); /* 2 kolom */ 
            gap: 50px;
        }
        body.dark-mode .stats-card  { 
            background-color: #2c3e50;
        }
        .stats-card { 
            background-color: #ffffff; 
            border: 1px solid #000; 
            border-radius: 8px; 
            text-align: center; 
            padding: 12px; 
            width: 48%; 
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
        }
        .stats-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); 
        }
        .stats-card i { 
            font-size: 28px; 
            color: #3498db; 
            margin-bottom: 8px; 
        }
        body.dark-mode .start-card   {
            color: #ddd;
        }
        
        body.dark-mode  p  {
            color: #ffffff;
        }
        
        body.dark-mode h3 {
            color: #ffffff;
        }
        .stats-card p { 
            font-size: 18px; 
            font-weight: bold; 
            color: #333; 
        }
        .stats-card h3 { 
            margin-top: 5px; 
            font-size: 12px; 
            color: #666; 
        }
        /* Responsif */
        @media (max-width: 600px) { 
            .stats-box { 
                flex-direction: column; 
            } 
            .stats-card { 
                width: 100%; 
            }
        }
        .chart-container {
            position: relative;
            width: 80%;
            height: 300px;
            margin: 0 auto;
            
            overflow: hidden;
        }
        .chart-container canvas {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .chart-container canvas:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
                hr {
            border: 0;
            border-top: 2px solid #3498db; /* Memperkecil ketebalan garis */
            margin: 15px 0;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Statistik</h1>
        <hr>
        <!-- Menampilkan statistik dalam 4 kolom menggunakan grid -->
        <div class="stats-box">
            <div class="stats-card" aria-label="Total Pengguna">
                <i class="fas fa-users"></i>
                <p><?php echo $total_users; ?></p>
                <h3>Total Pengguna</h3>
            </div>
            <div class="stats-card" aria-label="Total Akun">
                <i class="fas fa-id-card"></i>
                <p><?php echo $total_accounts; ?></p>
                <h3>Total Akun VPN</h3>
            </div>
            <div class="stats-card" aria-label="Total Transaksi">
                <i class="fas fa-exchange-alt"></i>
                <p><?php echo $total_transactions; ?></p>
                <h3>Total Transaksi</h3>
            </div>
            <div class="stats-card" aria-label="Total Server">
                <i class="fas fa-server"></i>
                <p><?php echo $total_servers; ?></p>
                <h3>Total Server</h3>
            </div>
        </div>
    </div>
    <!-- HTML container for the bar chart -->
    <div class="chart-container">
        <canvas id="chart"></canvas>
    </div>
<div class="server''>
	<?php
// Koneksi ke database
include('../admin/app/daftar_server');
?>	
</div>

    <script>
        // Data untuk grafik bar yang lebih modern, profesional, dan canggih
        const data = {
            labels: ['Pengguna', 'Akun VPN', 'Transaksi', 'Server'],
            datasets: [{
                label: 'Jumlah',
                data: [<?php echo $total_users; ?>, <?php echo $total_accounts; ?>, <?php echo $total_transactions; ?>, <?php echo $total_servers; ?>],
                backgroundColor: ['#42a5f5', '#66bb6a', '#ff7043', '#ffa726'],
                borderColor: ['#1e88e5', '#388e3c', '#f4511e', '#fb8c00'],
                borderWidth: 2,
                hoverBackgroundColor: ['#1e88e5', '#388e3c', '#f4511e', '#fb8c00'],
                hoverBorderColor: ['#0d47a1', '#2e7d32', '#c63e1e', '#f57c00'],
                hoverBorderWidth: 3,
                barPercentage: 0.7,
                categoryPercentage: 0.6,
            }]
        };

        // Opsi untuk grafik dengan desain halus dan interaktif
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuad',
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)',
                        borderColor: '#ddd',
                        borderWidth: 1,
                    },
                    ticks: {
                        font: {
                            family: 'Poppins, sans-serif',
                            size: 14,
                            weight: 'bold',
                            lineHeight: 1.5,
                        },
                        padding: 10,
                        color: '#333',
                    },
                },
                y: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        borderColor: '#ddd',
                        borderWidth: 1,
                    },
                    ticks: {
                        font: {
                            family: 'Poppins, sans-serif',
                            size: 14,
                            weight: 'bold',
                            lineHeight: 1.5,
                        },
                        padding: 10,
                        color: '#333',
                        beginAtZero: true,
                    },
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Poppins, sans-serif',
                            size: 12,
                            weight: 'bold',
                            lineHeight: 1.5,
                        },
                        padding: 20,
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    footerColor: '#fff',
                    cornerRadius: 6,
                    padding: 20,
                    bodyFont: {
                        family: 'Poppins, sans-serif',
                        size: 12,
                    },
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItem) {
                            return tooltipItem.raw + ' Akun';
                        },
                    },
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
            },
        };

        // Membuat grafik menggunakan Chart.js
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Jenis grafik bar
            data: data,
            options: options,
        });
    </script>
</body>
</html>
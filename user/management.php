<?php
session_start();
include('../script/autoload.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit;
}

// Ambil ID pengguna yang sedang login
$user_id = $_SESSION['user_id'];

// Inisialisasi filter
$filter_protocol = isset($_GET['protocol']) ? $_GET['protocol'] : '';

// Fetch akun yang dibuat oleh pengguna yang sedang login dengan filter jika ada
$query = "SELECT a.*, s.ip_address 
          FROM accounts a
          LEFT JOIN servers s ON a.server_id = s.id
          WHERE a.user_id = ?";
if ($filter_protocol) {
    $query .= " AND a.protocol = ?";
}
$query .= " ORDER BY a.created_at DESC"; // Sorting berdasarkan tanggal pembuatan
$stmt = $conn->prepare($query);

if ($filter_protocol) {
    $stmt->bind_param("ss", $user_id, $filter_protocol); // Ganti ke 'ss' untuk string
} else {
    $stmt->bind_param("s", $user_id); // Ganti ke 's' untuk string
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Saya</title>

    <style>
        /* Global Styling */
        body {
    font-family: 'Roboto', sans-serif;
    font-size: 12px;
    background-color: #f4f6f9;
    color: #34495e;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

        body.dark-mode {
            background-color: #23272a;
            color: #dfe2e5;
        }
        
        .container-custom {
            width: 100%;
            max-width: 600px; /* Reduced width */
            margin: 0 auto; /* Center align container */
            padding: 10px 15px; /* Added padding to ensure no elements touch the sides */
            background-color: #ffffff;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden; /* Prevents content overflow */
            box-sizing: border-box; /* Ensures padding is included in the width */
        }

        body.dark-mode .container-custom {
            background-color: #3A444F; /* Ensure consistency */
        }

        /* Header Styling */
        .account-header {
            color: #333;
            font-weight: bold;
            margin-bottom: 8px;
        }

        body.dark-mode .account-header {
            color: #E5E9F2;
        }

        /* Account Cards */
        .account-card {
            background-color: #fff;
            border: 1px solid #6082B6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 14px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden; /* Prevents content overflow within the card */
        }

        body.dark-mode .account-card {
            background-color: #2F3B46;
            border-color: #3E4A58;
        }

        .account-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        /* Account Details */
        .account-details p {
            margin: 4px 0;
            font-size: 12px;
            color: #777;
            word-wrap: break-word;
        }

        body.dark-mode .account-details p {
            color: #B4B9C2;
        }

        /* Action Buttons */
        .account-actions a {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 8px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            text-align: center;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .account-actions a:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        .extend-btn {
            background-color: #28A745;
            color: #fff;
        }

        .move-btn {
            background-color: #007BFF;
            color: #fff;
        }

        .detail-btn {
            background-color: #FF8C00;
            color: #fff;
        }

        /* Input and Select Styles */
        .filter-container-custom input,
        .filter-container-custom select {
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid #E4E4E4;
            border-radius: 5px;
            background-color: #F5F7FA;
            color: #333;
            width: 100%;
            max-width: 200px; /* Reduced max-width */
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }

        body.dark-mode .filter-container-custom input,
        body.dark-mode .filter-container-custom select {
            background-color: #3B4A59;
            border-color: #5A6977;
            color: #E5E9F2;
        }

        /* Filter Section */
        .filter-container-custom {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 16px;
            gap: 10px;
        }

        /* Account List Grid */
        .account-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Adjust grid items */
            gap: 16px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .filter-container-custom {
                flex-direction: column;
                align-items: flex-start;
            }

            .account-list {
                grid-template-columns: 1fr;
            }
        }

        /* Horizontal Line */
        hr {
            border: 0;
            border-top: 1px solid #E4E4E4;
            margin: 20px 0;
        }

        body.dark-mode hr {
            border-top: 1px solid #5A6977;
        }
    </style>
</head>

<div class="container-custom">
    <!-- Filter Section -->
    <div class="filter-container-custom">
        <form method="GET" style="flex: 1;">
            <select name="protocol" onchange="this.form.submit()">
                <option value="">-- Pilih Protokol --</option>
                <option value="vmess" <?= $filter_protocol == 'vmess' ? 'selected' : '' ?>>VMess</option>
                <option value="vless" <?= $filter_protocol == 'vless' ? 'selected' : '' ?>>Vless</option>
                <option value="trojan" <?= $filter_protocol == 'trojan' ? 'selected' : '' ?>>Trojan</option>
            </select>
        </form>
        <input type="text" id="search-box" placeholder="Cari berdasarkan UUID" onkeyup="searchAccount()">
    </div>

    <hr> <!-- Horizontal Line -->

    <!-- Account List -->
    <?php if ($result->num_rows > 0): ?>
        <div class="account-list" id="account-list">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="account-card" data-uuid="<?= htmlspecialchars($row['uuid']) ?>" id="account-<?= $row['id'] ?>">
                <div class="account-header"><?= strtoupper($row['protocol']) ?> Account</div>
                <div class="account-details">
                    <p><strong>UUID:</strong> <?= htmlspecialchars($row['uuid']) ?></p>
                    <p><strong>Expiration Date:</strong> <?= htmlspecialchars($row['expiration_date']) ?> 
                    <?php if (strtotime($row['expiration_date']) < time()): ?>
                        <span class="expired">(Kadaluarsa)</span>
                    <?php endif; ?>
                    </p>
                    <p><strong>IP Server:</strong> <?= htmlspecialchars($row['ip_address']) ?></p>
                </div>
                
                <div class="account-actions">
                    <a href="perpanjang.php?account_id=<?= $row['id'] ?>" class="extend-btn">Perpanjang</a>
                    <a href="pindah_server.php?account_id=<?= $row['id'] ?>" class="move-btn">Pindah Server</a>
                    <a href="detail_akun.php?account_id=<?= $row['id'] ?>" class="detail-btn">Detail</a>
                </div>
            </div>
           <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center; color: #999;">Tidak ada akun yang ditemukan.</p>
    <?php endif; ?>

</div>

<script>
    function searchAccount() {
        let searchTerm = document.getElementById('search-box').value.toLowerCase();
        let accounts = document.querySelectorAll('.account-card');
        accounts.forEach(account => {
            let uuid = account.getAttribute('data-uuid').toLowerCase();
            if (uuid.includes(searchTerm)) {
                account.style.display = '';
            } else {
                account.style.display = 'none';
            }
        });
    }
</script>

</body>
</html>
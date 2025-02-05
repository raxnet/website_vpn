<?php
ob_start();
include('../admin/app/autoload.php');

// Proses filter dan pencarian
$filter_protocol = isset($_GET['protocol']) ? $_GET['protocol'] : '';
$search_username = isset($_GET['search_username']) ? $_GET['search_username'] : '';

// Membuat query dinamis berdasarkan filter dan pencarian
$sql = "SELECT accounts.*, users.username AS user_name FROM accounts 
        INNER JOIN users ON accounts.user_id = users.id";

if ($filter_protocol || $search_username) {
    $sql .= " WHERE";
    $conditions = [];

    if ($filter_protocol) {
        $conditions[] = "accounts.protocol = '$filter_protocol'";
    }

    if ($search_username) {
        $conditions[] = "accounts.username LIKE '%$search_username%'";
    }

    $sql .= " " . implode(" AND ", $conditions);
}

$result = $conn->query($sql);

// Fungsi untuk menghapus data akun
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: account"); // Mengarahkan kembali setelah menghapus
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .search-filter-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .search-filter-wrapper input,
        .search-filter-wrapper select {
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 48%;
            transition: all 0.3s ease;
        }

        .search-filter-wrapper input:focus,
        .search-filter-wrapper select:focus {
            border-color: #3498db;
            outline: none;
        }

        .search-filter-wrapper button {
            padding: 8px 15px;
            font-size: 12px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-filter-wrapper button:hover {
            background-color: #2980b9;
        }

        .account-table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
        }

        .account-table th, .account-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .account-table th {
            background-color: #34495e;
            color: #ffffff;
            font-size: 14px;
        }

        .account-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .account-table td {
            font-size: 12px;
        }

        .delete-btn {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .delete-btn:hover {
            color: #c0392b;
        }

        /* Responsivitas lebih baik untuk perangkat mobile */
        @media (max-width: 768px) {
            .search-filter-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-filter-wrapper input,
            .search-filter-wrapper select {
                width: 100%;
                margin-bottom: 10px;
            }

            .account-table-wrapper {
                max-height: 300px;
            }

            .account-table th,
            .account-table td {
                padding: 8px;
                font-size: 11px;
            }

            .account-table td {
                word-wrap: break-word;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Akun</h1>
        
        <!-- Filter dan Search -->
        <div class="search-filter-wrapper">
            <form method="GET" action="">
                <input type="text" name="search_username" placeholder="Cari berdasarkan Username" value="<?php echo htmlspecialchars($search_username); ?>">
                <select name="protocol">
                    <option value="">Pilih Protocol</option>
                    <option value="VMess" <?php echo ($filter_protocol == 'VMess') ? 'selected' : ''; ?>>VMess</option>
                    <option value="Vless" <?php echo ($filter_protocol == 'Vless') ? 'selected' : ''; ?>>Vless</option>
                    <option value="Trojan" <?php echo ($filter_protocol == 'Trojan') ? 'selected' : ''; ?>>Trojan</option>
                    <option value="SSH" <?php echo ($filter_protocol == 'SSH') ? 'selected' : ''; ?>>SSH</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Tabel Akun -->
        <div class="account-table-wrapper">
            <table class="account-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengguna</th>
                        <th>Username</th>
                        <th>Protocol</th>
                        <th>UUID</th>
                        <th>Expiration Date</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['user_name']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['protocol']; ?></td>
                            <td><?php echo $row['uuid']; ?></td>
                            <td><?php echo $row['expiration_date']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php ob_end_flush(); ?>
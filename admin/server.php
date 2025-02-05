<?php
ob_start();
// Menghubungkan ke database
include('../admin/app/autoload.php');

// Menangani tambah server
if (isset($_POST['submit_tambah'])) {
    $server_name = $_POST['server_name'];
    $country = $_POST['country'];
    $ip_address = $_POST['ip_address'];
    $domain = $_POST['domain'];
    $price = $_POST['price'];
    $jumlah_akun_maksimal = $_POST['jumlah_akun_maksimal'];
    $status = $_POST['status'];

    $query = "INSERT INTO servers (server_name, country, ip_address, domain, price, status, jumlah_akun_maksimal) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdsi", $server_name, $country, $ip_address, $domain, $price, $status, $jumlah_akun_maksimal);
    $stmt->execute();
    header("Location: $_SERVER[PHP_SELF]"); // Refresh halaman setelah tambah
    exit();
}

// Menangani edit server
if (isset($_POST['submit_edit'])) {
    $id = $_POST['id'];
    $server_name = $_POST['server_name'];
    $country = $_POST['country'];
    $ip_address = $_POST['ip_address'];
    $domain = $_POST['domain'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $jumlah_akun_maksimal = $_POST['jumlah_akun_maksimal'];
    
    // Update data server ke database
    $query = "UPDATE servers SET server_name = ?, country = ?, ip_address = ?, domain = ?, price = ?, status = ?, jumlah_akun_maksimal = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdsi", $server_name, $country, $ip_address, $domain, $price, $status, $jumlah_akun_maksimal, $id);
    $stmt->execute();
    header("Location: $_SERVER[PHP_SELF]"); // Refresh halaman setelah edit
    exit();
}

// Menangani hapus server
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM servers WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    header("Location: $_SERVER[PHP_SELF]"); // Refresh halaman setelah hapus
    exit();
}

// Query untuk menampilkan data server
$query = "SELECT * FROM servers";
$result = $conn->query($query);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Server</title>
    

<style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3e7ed;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
            padding: 20px;
        }

        h2 {
            font-size: 20px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .container {
            max-width: 850px;
            margin: 15px auto;
            background: ;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .form-container {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 15px;
        }

        body.dark-mode .container {
            background-color: #2c3e50;
        }

        .form-container input,
        .form-container select,
        .form-container button {
            padding: 8px;
            border: 1px solid #ccd1d9;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #3498db;
            box-shadow: 0px 0px 5px rgba(52, 152, 219, 0.5);
        }

        .form-container button {
            background: #3498db;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            border: none;
        }

        .form-container button:hover {
            background: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }

        .btn {
        	
            padding: 6px 10px;
            color: white;
            background: #2ecc71;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px;
            transition: background 0.3s ease;
            text-decoration: none;
            right : 10px;
        }

        .btn:hover {
            background: #27ae60;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
            }

            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            .form-container button {
                width: 100%;
            }
        }

        .table-container {
            overflow-x: auto;
        }

        body.dark-mode {
            background-color: #1e1e1e;
            color: #ffffff;
        }

        body.dark-mode .container {
            background-color: #2c3e50;
            box-shadow: 0px 2px 10px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .form-container input,
        body.dark-mode .form-container select {
            background-color: #34495e;
            color: white;
            border: 1px solid #ffffff;
        }

        body.dark-mode .form-container input:focus,
        body.dark-mode .form-container select:focus {
            border-color: #3498db;
            box-shadow: 0px 0px 5px rgba(52, 152, 219, 0.5);
        }

        body.dark-mode .form-container button {
            background: #2980b9;
            color: white;
        }

        body.dark-mode table {
            background-color: #34495e;
            color: white;
        }

        body.dark-mode table th {
            background: #2980b9;
        }

        body.dark-mode table td {
            background-color: #2c3e50;
        }

        body.dark-mode .btn {
            background: #27ae60;
        }

        body.dark-mode .btn:hover {
            background: #1f8a50;
        }

        body.dark-mode .btn-danger {
            background: #c0392b;
        }

        body.dark-mode .btn-danger:hover {
            background: #a93226;
        }

        body.dark-mode h2 {
            color: #ffffff;
        }
    
    </style>
    
</head>
<body>
    <div class="container">
        <h2>Tambah Server</h2>
        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="server_name" placeholder="Nama Server" required>
                <input type="text" name="country" placeholder="Negara" required>
                <input type="text" name="ip_address" placeholder="IP Address" required>
                <input type="text" name="domain" placeholder="Domain" required>
                <input type="number" name="price" placeholder="Harga" required>
                <input type="number" name="jumlah_akun_maksimal" placeholder="Maksimal User" required>
                <select name="status" required>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
                <button type="submit" name="submit_tambah">Tambah Server</button>
            </form>
        </div>
    </div>

    <!-- Form Edit Server -->
    <div class="container" id="editFormContainer" style="display:none;">
        <h2>Edit Server</h2>
        <div class="form-container">
            <form method="POST" action="">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="server_name" id="edit_server_name" placeholder="Nama Server" required>
                <input type="text" name="country" id="edit_country" placeholder="Negara" required>
                <input type="text" name="ip_address" id="edit_ip_address" placeholder="IP Address" required>
                <input type="text" name="domain" id="edit_domain" placeholder="Domain" required>
                <input type="number" name="price" id="edit_price" placeholder="Harga" required>
                <input type="number" name="jumlah_akun_maksimal" id="edit_jumlah_akun_maksimal" placeholder="Maksimal User" required>
                <select name="status" id="edit_status" required>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
                <button type="submit" name="submit_edit">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Server</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Server</th>
                        <th>Negara</th>
                        <th>IP Address</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($server = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($server['server_name']); ?></td>
                        <td><?php echo htmlspecialchars($server['country']); ?></td>
                        <td><?php echo htmlspecialchars($server['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($server['price']); ?></td>
                        <td><?php echo htmlspecialchars($server['status']); ?></td>
                        <td class="action-btns">
                            <a href="#" class="btn" onclick="editServer(<?php echo $server['id']; ?>, '<?php echo addslashes($server['server_name']); ?>', '<?php echo addslashes($server['country']); ?>', '<?php echo addslashes($server['ip_address']); ?>', '<?php echo addslashes($server['domain']); ?>', <?php echo $server['price']; ?>, '<?php echo $server['status']; ?>')">Edit</a>
                            <a href="?delete_id=<?php echo $server['id']; ?>" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus server ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editServer(id, server_name, country, ip_address, domain, price, status) {
            document.getElementById('editFormContainer').style.display = 'block';
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_server_name').value = server_name;
            document.getElementById('edit_country').value = country;
            document.getElementById('edit_ip_address').value = ip_address;
            document.getElementById('edit_domain').value = domain;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_status').value = status;
        }
    </script>
</body>
</html>
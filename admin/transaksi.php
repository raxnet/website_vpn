<?php
ob_start();
include('../admin/app/autoload.php'); // Menggunakan koneksi yang sudah ada

// Ambil status filter jika ada
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Query dasar untuk mengambil data transaksi dengan nama pengguna
$sql = "SELECT transaksi.*, users.username 
        FROM transaksi 
        LEFT JOIN users ON transaksi.user_id = users.id";

// Jika status filter diterapkan, sesuaikan query dengan kondisi status
if ($statusFilter != '') {
    $sql .= " WHERE transaksi.status = '$statusFilter'";
}

$sql .= " ORDER BY transaksi.created_at DESC";

// Eksekusi query
$result = $conn->query($sql);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = "transaksi_" . date("Y-m-d_H-i-s") . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Username', 'Order ID', 'Jumlah', 'Metode Pembayaran', 'Status', 'Tanggal']);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 10px;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .badge {
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 8px;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-completed {
            background-color: #28a745;
        }
        .search-box {
            max-width: 200px;
            margin-bottom: 12px;
        }
        th, td {
            font-size: 11px;
            vertical-align: middle;
        }
        .pagination {
            font-size: 12px;
        }
        .table thead th {
            background-color: #f1f1f1;
        }
        .sort-icon {
            cursor: pointer;
        }
        .filter-section {
            margin-bottom: 15px;
        }
        .modal-body {
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-3" style="font-size: 16px;">Riwayat Transaksi</h2>
    
    <!-- Filter by Status -->
    <div class="filter-section">
        <label for="statusFilter">Filter Berdasarkan Status:</label>
        <select id="statusFilter" class="form-select" onchange="filterStatus()">
            <option value="">Semua</option>
            <option value="completed" <?php echo ($statusFilter == 'completed') ? 'selected' : ''; ?>>Completed</option>
            <option value="pending" <?php echo ($statusFilter == 'pending') ? 'selected' : ''; ?>>Pending</option>
        </select>
    </div>
    
    <!-- Export Button -->
    <a href="?export=csv" class="btn btn-success mb-3">Export to csv</a>

    <!-- Table with sortable and paginated data -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama User</th>
                    <th>Order ID</th>
                    <th>Jumlah</th>
                    <th>Metode Pembayaran</th>
                    <th>Status <i class="fas fa-sort sort-icon" id="sortStatus"></i></th>
                    <th>Tanggal <i class="fas fa-sort sort-icon" id="sortDate"></i></th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="transactionTable">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#transactionDetailModal">
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['username'] ? $row['username'] : '<span class="text-muted">Tidak Diketahui</span>' ?></td>
                            <td><?= $row['order_id'] ?></td>
                            <td>Rp <?= number_format($row['amount'], 0, ',', '.') ?></td>
                            <td>
                                <?= $row['payment_method'] ? ucfirst(str_replace('_', ' ', $row['payment_method'])) : '<span class="text-muted">Tidak Diketahui</span>' ?>
                            </td>
                            <td>
                                <span class="badge <?= $row['status'] == 'completed' ? 'status-completed' : 'status-pending' ?>" data-bs-toggle="tooltip" title="<?= ucfirst($row['status']) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></td>
                            <td><button class="btn btn-info btn-sm" onclick="showDetail(<?= $row['id'] ?>)">Detail</button></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav>
</div>

<!-- Modal for Transaction Details -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailModalLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Detail Transaction Content -->
                <p>Order ID: <span id="detailOrderId"></span></p>
                <p>Jumlah: <span id="detailAmount"></span></p>
                <p>Metode Pembayaran: <span id="detailPaymentMethod"></span></p>
                <p>Status: <span id="detailStatus"></span></p>
                <p>Tanggal: <span id="detailDate"></span></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tooltip Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Filter by Status
    function filterStatus() {
        const status = document.getElementById('statusFilter').value;
        window.location.href = status ? `?status=${status}` : '?';
    }

    // Show Transaction Details
    function showDetail(transactionId) {
        const rows = document.querySelectorAll('tr[data-id]');
        rows.forEach(row => {
            if (row.getAttribute('data-id') == transactionId) {
                document.getElementById('detailOrderId').textContent = row.cells[2].textContent;
                document.getElementById('detailAmount').textContent = row.cells[3].textContent;
                document.getElementById('detailPaymentMethod').textContent = row.cells[4].textContent;
                document.getElementById('detailStatus').textContent = row.cells[5].textContent;
                document.getElementById('detailDate').textContent = row.cells[6].textContent;
            }
        });
    }
</script>

</body>
</html>
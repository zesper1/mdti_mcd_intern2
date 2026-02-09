<?php
require_once '../../src/config.php'; // Adjust path if needed
require_once '../../src/Models/BaseModel.php';
require_once '../../src/Models/AuditModel.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Superadmin') {
    header("Location: ../login.php");
    exit;
}

$userName = $_SESSION['first_name'] ?? 'Superadmin';

$auditModel = new AuditModel();

$stats = [
    'total_sales' => 1250.50,
    'pending_quotes' => 14,
    'active_suppliers' => 48,
    'low_stock' => 5
];

$chartLabels = json_encode(['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb']);
$chartData   = json_encode([450000, 520000, 480000, 750000, 620000, 1250000]);

$auditLogs = $auditModel->getLogs( 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Dashboard - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#"><i class="fas fa-shield-alt me-2"></i><?php echo APP_NAME; ?> Admin</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Welcome, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
                <a href="../../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Total Quoted (Feb)</h6>
                                <h3 class="fw-bold mb-0">₱ <?php echo number_format($stats['total_sales'], 2); ?></h3>
                            </div>
                            <i class="fas fa-file-invoice-dollar fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Pending Approval</h6>
                                <h3 class="fw-bold mb-0"><?php echo $stats['pending_quotes']; ?></h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-warning border-0 bg-opacity-25 small">
                        <a href="#" class="text-dark text-decoration-none">Review pending items &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Active Suppliers</h6>
                                <h3 class="fw-bold mb-0"><?php echo $stats['active_suppliers']; ?></h3>
                            </div>
                            <i class="fas fa-truck fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Low Stock Products</h6>
                                <h3 class="fw-bold mb-0"><?php echo $stats['low_stock']; ?></h3>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-danger border-0 bg-opacity-25 small">
                        <a href="#" class="text-white text-decoration-none">View inventory &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Quotation Volume Trends</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="quotationChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="users/manage.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-users-cog text-primary me-2"></i> Manage Users
                                <small class="d-block text-muted">Add, remove, or change roles</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="settings/company.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-building text-info me-2"></i> Company Preferences
                                <small class="d-block text-muted">Logo, Tax Rate, Address</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="suppliers/list.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-file-contract text-success me-2"></i> Supplier Masterlist
                                <small class="d-block text-muted">Update supplier contacts</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="settings/backup.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 bg-light">
                            <div>
                                <i class="fas fa-database text-secondary me-2"></i> System Backup
                            </div>
                            <i class="fas fa-download text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Recent System Activity</h6>
                <a href="audit_logs.php" class="btn btn-sm btn-primary">View All Logs</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">User</th>
                                <th>Action</th>
                                <th>Target / Details</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $log): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $log['user']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $log['badge']; ?> bg-opacity-10 text-<?php echo $log['badge']; ?> px-3 py-2 rounded-pill">
                                        <?php echo $log['event_code']; ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?php echo $log['description']; ?></td>
                                <td class="text-secondary small"><?php echo $log['time']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const ctx = document.getElementById('quotationChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line', // Can be 'bar', 'line', 'pie'
            data: {
                labels: <?php echo $chartLabels; ?>,
                datasets: [{
                    label: 'Quotation Value (PHP)',
                    data: <?php echo $chartData; ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)', // Bootstrap Primary with opacity
                    borderColor: 'rgba(13, 110, 253, 1)',       // Bootstrap Primary
                    borderWidth: 2,
                    tension: 0.3, // Makes lines curved
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>
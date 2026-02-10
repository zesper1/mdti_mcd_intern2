<?php
require_once '../../src/config.php';
require_once '../../src/Models/BaseModel.php';
require_once '../../src/Models/AuditModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$userName = $_SESSION['first_name'] ?? 'Superadmin';
$stats = [
    'total_sales' => 1250.50,
    'pending_quotes' => 14,
    'active_suppliers' => 48,
    'low_stock' => 5
];

$chartLabels = json_encode(['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb']);
$chartData   = json_encode([450000, 520000, 480000, 750000, 620000, 1250000]);

$auditLogs = [
    ['user' => 'Admin', 'badge' => 'primary', 'event_code' => 'LOGIN', 'description' => 'User logged in', 'time' => '10:00 AM'],
    ['user' => 'Staff', 'badge' => 'success', 'event_code' => 'CREATE', 'description' => 'Created Quote #1001', 'time' => '10:15 AM'],
    ['user' => 'Admin', 'badge' => 'warning', 'event_code' => 'UPDATE', 'description' => 'Updated Supplier Info', 'time' => '11:00 AM'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-width-collapsed: 70px; --sidebar-width-expanded: 260px; --top-navbar-height: 60px; }
        body { overflow-x: hidden; }
        .main-content { flex-grow: 1; width: 100%; }
        /* Dropdown arrow fix */
        .dropdown-toggle::after { display: none; margin-left: auto; transition: 0.3s; }
        .sidebar:hover .dropdown-toggle::after { display: inline-block; }
        .collapse.show { background: rgba(0,0,0,0.2); }
        .submenu-item { padding-left: 3.5rem !important; font-size: 0.9em; }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main-content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4" style="height: var(--top-navbar-height);">
                <div class="container-fluid px-4">
                    <span class="navbar-text text-dark">Dashboard Overview</span>
                    <div class="d-flex align-items-center">
                        <span class="text-dark me-3">Welcome, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu text-small shadow">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../../logout.php">Sign out</a></li>
                            </ul>
                        </div>
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
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-primary">Trends</h6></div>
                            <div class="card-body"><canvas id="quotationChart" style="max-height: 300px;"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6></div>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action py-3"><i class="fas fa-users-cog text-primary me-2"></i> Manage Users</a>
                                <a href="#" class="list-group-item list-group-item-action py-3"><i class="fas fa-building text-info me-2"></i> Company Preferences</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-primary">Activity</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead><tr><th class="ps-4">User</th><th>Action</th><th>Details</th><th>Time</th></tr></thead>
                                <tbody>
                                    <?php foreach ($auditLogs as $log): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo $log['user']; ?></td>
                                        <td><span class="badge bg-<?php echo $log['badge']; ?> bg-opacity-10 text-<?php echo $log['badge']; ?> px-3 py-2 rounded-pill"><?php echo $log['event_code']; ?></span></td>
                                        <td><?php echo $log['description']; ?></td>
                                        <td><?php echo $log['time']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('quotationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $chartLabels; ?>,
                datasets: [{ label: 'PHP', data: <?php echo $chartData; ?>, borderColor: 'rgba(13, 110, 253, 1)', tension: 0.3, fill: true }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>
</html>
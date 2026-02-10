<?php
require_once __DIR__ . '/../../../src/config.php'; // use __DIR__ for reliable path
if (session_status() === PHP_SESSION_NONE) session_start();

$catalogs = [
    ['name' => 'IT Equipment', 'items' => 25],
    ['name' => 'Office Supplies', 'items' => 110]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalogs - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content { flex-grow: 1; width: 100%; }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php
        $sidebarPath = __DIR__ . '/../layout/sidebar.php';
        if (file_exists($sidebarPath)) {
            include_once $sidebarPath;
        }
        ?>

        <div class="main-content w-100">
            <div class="container-fluid px-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Product Catalogs</h2>
                    <button class="btn btn-primary">Add New Catalog</button>
                </div>

                <div class="row">
                    <?php foreach ($catalogs as $cat): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5><?php echo $cat['name']; ?></h5>
                                <p class="text-muted"><?php echo $cat['items']; ?> Products</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Items</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
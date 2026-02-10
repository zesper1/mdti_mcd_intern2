<?php
require_once '../../src/config.php';
// Catalog specific logic (e.g., fetching from DB)
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
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main-content w-100">
            <?php include 'layout/navbar.php'; ?>

            <div class="container-fluid px-4">
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
</body>
</html>
<?php
require_once 'src/config.php';
session_start();

// 1. Session Guard: If no user_id is set, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Optional: Retrieve the user's name from the session 
// (Make sure you set $_SESSION['first_name'] in your AuthService!)
$userName = $_SESSION['first_name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-5">
        <div class="container">
            <span class="navbar-brand mb-0 h1"><?php echo APP_NAME; ?></span>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Logged in as: <strong><?php echo htmlspecialchars($userName); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <h1 class="display-4">Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
                        <p class="lead">You have successfully accessed the secure dashboard.</p>
                        <hr class="my-4">
                        <p>This is your central hub for managing the system.</p>
                        
                        <div class="mt-4">
                            <button class="btn btn-primary">View Reports</button>
                            <button class="btn btn-secondary">Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
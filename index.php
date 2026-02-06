<?php 
require_once 'src/config.php'; 
session_start();

if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'Superadmin'){
        header('Location: ./pages/Superadmin/dashboard.php');
    } else {
        header('Location: ./pages/Admin/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card shadow login-container">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Login</h3>
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger py-2 text-center" style="font-size: 0.9rem;">
                    <?php 
                        echo $_SESSION['login_error']; 
                        unset($_SESSION['login_error']); 
                    ?>
                </div>
            <?php endif; ?>

            <form action="./src/Controller/LoginController.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="mb-4 d-flex justify-content-center">
                    <div class="cf-turnstile" data-sitekey="<?php echo CF_SITE_KEY; ?>"></div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
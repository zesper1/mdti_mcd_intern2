<?php
// 1. Start the session so we can access current user data
session_start();

// 2. Load necessary files
require_once 'src/config.php';
require_once 'src/Models/BaseModel.php';
require_once 'src/Models/AuditModel.php';
require_once 'src/Models/UserModel.php'; // Required if AuthService uses it
require_once 'src/Services/AuthService.php';

// 3. Log the logout event in the database
if (isset($_SESSION['user_id'])) {
    try {
        $auth = new AuthService();
        // This calls the method that runs 'sp_log_event' -> 'AUTH_LOGOUT'
        $auth->logout($_SESSION['user_id']); 
    } catch (Exception $e) {
        // If the database fails, we still want to log the user out locally,
        // so we just catch the error and continue.
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// 4. Destroy the session
// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// 5. Redirect to Login Page
header("Location: index.php");
exit;
?>
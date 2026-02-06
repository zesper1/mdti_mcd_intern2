<?php
// 1. Start session
session_start();

// 2. Include your classes
require_once '../config.php';
require_once '../Models/BaseModel.php';
require_once '../Models/UserModel.php';
require_once '../Models/AuditModel.php';
require_once '../Services/AuthService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthService();

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['cf-turnstile-response'] ?? '';

    $result = $auth->login($email, $password, $captcha);

    if ($result['success']) {
        $role = $_SESSION['role'] ?? 'Member'; // Default to Member if null
        echo "<script>alert.log('$role');</script>";
        switch ($role) {
            case 'Admin':
                header("Location: ../../pages/Admin/dashboard.php");
                break;
            
            case 'Superadmin':
                header("Location: ../../pages/Superadmin/dashboard.php");
                break;

            case 'Member':
            default:
                header("Location: member/dashboard.php");
                break;
        }
        exit;

    } else {
        // Login Failed
        $_SESSION['login_error'] = $result['message'];
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
<?php
require_once '../../src/config.php';
session_start();

// 1. Auth Check only (Any logged-in user can be here)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<h1>Welcome, Admin!</h1>
<p>This is your personal area.</p>
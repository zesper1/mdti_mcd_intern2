<?php
// processes/user_process.php
require_once '../config.php';
require_once '../Models/BaseModel.php';
require_once '../Models/UserModel.php';
session_start();

header('Content-Type: application/json');

// 1. Security Guard
if (($_SESSION['role'] ?? '') !== 'Superadmin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized Access']);
    exit;
}

$userModel = new UserModel();
$action = $_GET['action'] ?? '';

// 2. Handle GET (Fetch Data)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
    try {
        $users = $userModel->getAllUsers();
        echo json_encode(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// 3. Handle POST (Save/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'delete') {
        if ($input['id'] == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete yourself.']);
            exit;
        }
        
        if ($userModel->deleteUser($input['id'])) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
        exit;
    }

    if ($action === 'save') {
        // Pass the entire input array to the Model to handle validation & logic
        $result = $userModel->saveUser($input);
        echo json_encode($result);
        exit;
    }
}

// Default response if no action matched
echo json_encode(['success' => false, 'message' => 'Invalid action']);
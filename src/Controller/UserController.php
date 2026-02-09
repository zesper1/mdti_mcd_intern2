<?php
require_once '../config.php';
require_once '../Models/BaseModel.php';
require_once '../Models/UserModel.php';
require_once '../Models/AuditModel.php'; 

session_start();

header('Content-Type: application/json');

// Security Guard
if (($_SESSION['role'] ?? '') !== 'Superadmin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized Access']);
    exit;
}

$userModel = new UserModel();
$auditModel = new AuditModel(); 

$action = $_GET['action'] ?? '';

// Handle GET (Fetch Data)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
    try {
        $users = $userModel->getAllUsers();
        echo json_encode(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle POST (Save/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $currentUserId = $_SESSION['user_id']; // The Actor (Superadmin)

    if ($action === 'delete') {
        if ($input['id'] == $currentUserId) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete yourself.']);
            exit;
        }
        
        if ($userModel->deleteUser($input['id'])) {
            $auditModel->log(
                $currentUserId, 
                'USER_DELETE', 
                $input['id'], 
                "Deleted user account"
            );

            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
        exit;
    }

    // --- SAVE ACTION (CREATE or UPDATE) ---
    if ($action === 'save') {
        // Determine if this is a Create or Update BEFORE saving
        $isUpdate = !empty($input['id']);
        $targetEmail = $input['email'] ?? 'Unknown';
        $result = $userModel->saveUser($input);
        if ($result['success']) {
            // 3b. LOG SAVE
            if ($isUpdate) {
                // UPDATE
                $auditModel->log(
                    $currentUserId, 
                    'USER_UPDATE', 
                    $input['id'], 
                    "Updated profile details for " . $targetEmail
                );
            } else {
                $newTargetId = $result['insert_id'] ?? null; 

                $auditModel->log(
                    $currentUserId, 
                    'USER_CREATE', 
                    $input['id'] ?? $newTargetId, 
                    "Created new user account for " . $targetEmail
                );
            }
        }

        echo json_encode($result);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);

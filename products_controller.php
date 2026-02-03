<?php
    require_once("db.php");
    require_once("products_service.php");
    header('Content-Type: application/json');
    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; 
    switch($action) {
        case "update":
            $id    = $_POST['id'] ?? null;
            $price = $_POST['price'] ?? null;
            $name  = $_POST['product_name'] ?? ''; 
            if ($id && $price) {
                // Ensure the arguments match your function definition
                $success = updateProduct($pdo, $id, $name, $price);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Missing data']);
            }
            break; // Always break in a switch!
        case "create":
            $name = $_POST['product_name'] ?? '';
            $price = $_POST['price'] ?? 0;
            
            if (!empty($name)) {
                $success = createProduct($pdo, $name, $price);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Name is required']);
            }
            break;  
        case "delete":
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
    exit;
}
?>
<?php
    function createProduct($pdo, $name, $price) {
    $sql = "INSERT INTO products (product_name, price) VALUES (:name, :price)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':price', $price); 
    
    return $stmt->execute();
    }

    function getProducts($pdo) {
        $stmt = $pdo->query("SELECT id, product_name, price FROM products");
        return $stmt->fetchAll();
    }

    function getProductById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function updateProduct($pdo, $id, $name, $price) {
    $sql = "UPDATE products SET product_name = :name, price = :price WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    return $stmt->execute([
        ':name'  => $name,
        ':price' => $price,
        ':id'    => $id
    ]);
    }

    function deleteProduct($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
    }
?>
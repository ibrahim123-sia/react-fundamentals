<?php
require_once 'db_connect.php';

try {
    $query = "SELECT id, name, email, phone, created_at FROM users WHERE role = 'delivery_boy' ORDER BY name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $delivery_boys = $stmt->fetchAll();
    
    echo json_encode(array(
        "success" => true,
        "delivery_boys" => $delivery_boys
    ));
} catch(PDOException $e) {
    echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
}
?>

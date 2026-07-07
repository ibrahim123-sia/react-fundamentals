<?php
require_once 'db_connect.php';

try {
    $query = "SELECT o.order_id, o.user_id, o.product_name, o.quantity, o.status, o.assigned_to, o.created_at,
                     c.name AS customer_name, r.name AS assigned_to_name
              FROM orders o
              LEFT JOIN users c ON o.user_id = c.id
              LEFT JOIN users r ON o.assigned_to = r.id
              ORDER BY o.order_id DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $orders = $stmt->fetchAll();
    
    echo json_encode(array(
        "success" => true,
        "orders" => $orders
    ));
} catch(PDOException $e) {
    echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
}
?>

<?php
require_once 'db_connect.php';

try {
    $query = "SELECT o.order_id, o.user_id, o.product_name, o.quantity, o.status, o.created_at,
                     c.name AS customer_name
              FROM orders o
              LEFT JOIN users c ON o.user_id = c.id
              WHERE o.status = 'Pending'
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

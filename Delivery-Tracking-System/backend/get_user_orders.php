<?php
require_once 'db_connect.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    try {
        $query = "SELECT o.order_id, o.user_id, o.product_name, o.quantity, o.status, o.assigned_to, o.created_at,
                         r.name AS assigned_to_name
                  FROM orders o
                  LEFT JOIN users r ON o.assigned_to = r.id
                  WHERE o.user_id = :user_id
                  ORDER BY o.order_id DESC";
                  
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $orders = $stmt->fetchAll();
        
        echo json_encode(array(
            "success" => true,
            "orders" => $orders
        ));
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid or missing user_id."));
}
?>

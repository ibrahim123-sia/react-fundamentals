<?php
require_once 'db_connect.php';

$delivery_boy_id = isset($_GET['delivery_boy_id']) ? intval($_GET['delivery_boy_id']) : 0;

if ($delivery_boy_id > 0) {
    try {
        $query = "SELECT o.order_id, o.user_id, o.product_name, o.quantity, o.status, o.assigned_to, o.created_at,
                         c.name AS customer_name
                  FROM orders o
                  LEFT JOIN users c ON o.user_id = c.id
                  WHERE o.assigned_to = :delivery_boy_id
                  ORDER BY o.order_id DESC";
                  
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':delivery_boy_id', $delivery_boy_id);
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
    echo json_encode(array("success" => false, "message" => "Invalid or missing delivery_boy_id."));
}
?>

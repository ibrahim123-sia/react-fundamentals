<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_id) && !empty($data->product_name) && !empty($data->quantity)) {
    try {
        $query = "INSERT INTO orders (user_id, product_name, quantity, status) VALUES (:user_id, :product_name, :quantity, 'Pending')";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':user_id', $data->user_id);
        $stmt->bindParam(':product_name', $data->product_name);
        $stmt->bindParam(':quantity', $data->quantity);
        
        if ($stmt->execute()) {
            $order_id = $conn->lastInsertId();
            echo json_encode(array(
                "success" => true,
                "message" => "Order created successfully.",
                "order_id" => $order_id
            ));
        } else {
            echo json_encode(array("success" => false, "message" => "Unable to create order."));
        }
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Incomplete data. Required: user_id, product_name, quantity."));
}
?>

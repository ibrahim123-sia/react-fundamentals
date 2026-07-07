<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->order_id) && !empty($data->assigned_to)) {
    try {
        $query = "UPDATE orders SET assigned_to = :assigned_to, status = 'Assigned' WHERE order_id = :order_id";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':assigned_to', $data->assigned_to);
        $stmt->bindParam(':order_id', $data->order_id);
        
        if ($stmt->execute()) {
            echo json_encode(array(
                "success" => true,
                "message" => "Order successfully assigned to rider."
            ));
        } else {
            echo json_encode(array("success" => false, "message" => "Unable to assign order."));
        }
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Incomplete data. Required: order_id, assigned_to."));
}
?>

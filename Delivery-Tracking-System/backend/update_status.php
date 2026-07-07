<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->order_id) && !empty($data->status)) {
    // Validate status values
    $allowed_statuses = array('Pending', 'Assigned', 'Accepted', 'On the way', 'Delivered');
    if (!in_array($data->status, $allowed_statuses)) {
        echo json_encode(array("success" => false, "message" => "Invalid status value."));
        exit();
    }

    try {
        $query = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':order_id', $data->order_id);
        
        if ($stmt->execute()) {
            echo json_encode(array(
                "success" => true,
                "message" => "Order status updated to " . $data->status
            ));
        } else {
            echo json_encode(array("success" => false, "message" => "Unable to update status."));
        }
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Incomplete data. Required: order_id, status."));
}
?>

<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->id) &&
    !empty($data->customer_name) &&
    !empty($data->customer_address) &&
    !empty($data->purpose)
) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Check if visit exists
        $check_query = "SELECT id, image FROM visits WHERE id = :id";
        $stmt_check = $db->prepare($check_query);
        $stmt_check->bindParam(":id", $data->id);
        $stmt_check->execute();

        if ($stmt_check->rowCount() === 0) {
            sendResponse(404, array("message" => "Visit not found."));
        }

        $visit = $stmt_check->fetch(PDO::FETCH_ASSOC);
        $image_filename = $visit['image'];

        // If new image base64 is provided
        if (!empty($data->image) && strpos($data->image, 'visit_') === false) {
            $base64_string = $data->image;
            $upload_dir = "../uploads/";
            
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type)) {
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                $image_type = strtolower($type[1]);
            } else {
                $image_type = 'jpg';
            }
            
            $base64_string = str_replace(' ', '+', $base64_string);
            $decoded_image = base64_decode($base64_string);
            
            if ($decoded_image !== false) {
                // Delete old image if it exists
                if ($image_filename && file_exists($upload_dir . $image_filename)) {
                    unlink($upload_dir . $image_filename);
                }
                
                $image_filename = "visit_" . time() . "_" . uniqid() . "." . $image_type;
                file_put_contents($upload_dir . $image_filename, $decoded_image);
            }
        }

        $query = "UPDATE visits SET 
                    customer_name = :customer_name, 
                    customer_address = :customer_address, 
                    purpose = :purpose, 
                    notes = :notes";
        
        // Only update coords if provided
        if (isset($data->latitude) && isset($data->longitude)) {
            $query .= ", latitude = :latitude, longitude = :longitude";
        }
        
        $query .= ", image = :image WHERE id = :id";

        $stmt = $db->prepare($query);

        $customer_name = htmlspecialchars(strip_tags($data->customer_name));
        $customer_address = htmlspecialchars(strip_tags($data->customer_address));
        $purpose = htmlspecialchars(strip_tags($data->purpose));
        $notes = isset($data->notes) ? htmlspecialchars(strip_tags($data->notes)) : "";

        $stmt->bindParam(":customer_name", $customer_name);
        $stmt->bindParam(":customer_address", $customer_address);
        $stmt->bindParam(":purpose", $purpose);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":image", $image_filename);
        $stmt->bindParam(":id", $data->id);

        if (isset($data->latitude) && isset($data->longitude)) {
            $stmt->bindParam(":latitude", $data->latitude);
            $stmt->bindParam(":longitude", $data->longitude);
        }

        if ($stmt->execute()) {
            sendResponse(200, array("message" => "Visit was updated successfully."));
        } else {
            sendResponse(500, array("message" => "Unable to update visit."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to update visit. Data is incomplete. Required: id, customer_name, customer_address, purpose."));
}
?>

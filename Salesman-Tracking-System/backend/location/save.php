<?php
require_once "../config/database.php";

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->user_id) &&
    isset($data->latitude) &&
    isset($data->longitude)
) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Verify user exists and is a salesman
        $check_user = "SELECT role FROM users WHERE id = :user_id LIMIT 1";
        $stmt_check = $db->prepare($check_user);
        $stmt_check->bindParam(":user_id", $data->user_id);
        $stmt_check->execute();

        if ($stmt_check->rowCount() === 0) {
            sendResponse(404, array("message" => "User not found."));
        }

        $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($user['role'] !== 'salesman') {
            sendResponse(400, array("message" => "Only salesmen locations can be tracked."));
        }

        // Insert coordinates
        $query = "INSERT INTO locations (user_id, latitude, longitude) VALUES (:user_id, :latitude, :longitude)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":user_id", $data->user_id);
        $stmt->bindParam(":latitude", $data->latitude);
        $stmt->bindParam(":longitude", $data->longitude);

        if ($stmt->execute()) {
            sendResponse(201, array("message" => "Location logged successfully."));
        } else {
            sendResponse(500, array("message" => "Unable to save location."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to save location. Data is incomplete. Require user_id, latitude, and longitude."));
}
?>

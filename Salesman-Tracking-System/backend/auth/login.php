<?php
require_once "../config/database.php";

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        $query = "SELECT id, name, email, phone, password, role FROM users WHERE email = :email LIMIT 0,1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $data->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($data->password, $row['password'])) {
                // Remove password from response
                unset($row['password']);
                
                sendResponse(200, array(
                    "message" => "Login successful.",
                    "user" => $row
                ));
            } else {
                sendResponse(401, array("message" => "Login failed. Invalid password."));
            }
        } else {
            sendResponse(401, array("message" => "Login failed. User not found."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to login. Data is incomplete. Require email and password."));
}
?>

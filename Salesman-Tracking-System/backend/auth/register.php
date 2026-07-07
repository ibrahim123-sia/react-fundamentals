<?php
require_once "../config/database.php";

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->phone) &&
    !empty($data->password) &&
    !empty($data->role)
) {
    // Validate role
    if ($data->role !== 'salesman' && $data->role !== 'manager') {
        sendResponse(400, array("message" => "Invalid role. Must be 'salesman' or 'manager'."));
    }

    $database = new Database();
    $db = $database->getConnection();

    try {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = :email LIMIT 0,1";
        $stmt = $db->prepare($check_query);
        $stmt->bindParam(":email", $data->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            sendResponse(400, array("message" => "Email is already registered."));
        }

        // Insert new user
        $query = "INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)";
        $stmt = $db->prepare($query);

        // Sanitize data
        $name = htmlspecialchars(strip_tags($data->name));
        $email = htmlspecialchars(strip_tags($data->email));
        $phone = htmlspecialchars(strip_tags($data->phone));
        $role = htmlspecialchars(strip_tags($data->role));
        
        // Hash password
        $password_hash = password_hash($data->password, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            sendResponse(201, array(
                "message" => "User was registered successfully.",
                "user" => array(
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "role" => $role
                )
            ));
        } else {
            sendResponse(500, array("message" => "Unable to register the user."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to register. Data is incomplete. Require name, email, phone, password, and role."));
}
?>

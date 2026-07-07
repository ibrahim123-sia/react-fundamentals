<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->phone) &&
    !empty($data->password) &&
    !empty($data->role)
) {
    try {
        // Check if user already exists
        $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($check_query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(array("success" => false, "message" => "Email is already registered."));
            exit();
        }

        // Insert new user
        $query = "INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':phone', $data->phone);
        
        // Using plain text password as requested by simplified school quiz/requirements context, 
        // normally we use password_hash($data->password, PASSWORD_BCRYPT)
        $stmt->bindParam(':password', $data->password);
        $stmt->bindParam(':role', $data->role);
        
        if ($stmt->execute()) {
            $user_id = $conn->lastInsertId();
            echo json_encode(array(
                "success" => true,
                "message" => "User registered successfully.",
                "user" => array(
                    "id" => $user_id,
                    "name" => $data->name,
                    "email" => $data->email,
                    "phone" => $data->phone,
                    "role" => $data->role
                )
            ));
        } else {
            echo json_encode(array("success" => false, "message" => "Unable to register user."));
        }
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Incomplete data. Required: name, email, phone, password, role."));
}
?>

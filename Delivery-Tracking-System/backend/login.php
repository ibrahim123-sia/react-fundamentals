<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    try {
        $query = "SELECT id, name, email, phone, password, role, created_at FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Password verification (simplified direct comparison for testing)
            if ($data->password === $user['password']) {
                // Remove password from returned data
                unset($user['password']);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Login successful.",
                    "user" => $user
                ));
            } else {
                echo json_encode(array("success" => false, "message" => "Incorrect password."));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "User not found."));
        }
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Incomplete data. Required: email, password."));
}
?>

<?php
require_once "../config/database.php";

$database = new Database();
$db = $database->getConnection();

// Create uploads directory if it doesn't exist
$upload_dir = "../uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Check content type to see if it is JSON or multipart/form-data
$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
$is_json = (strpos($content_type, "application/json") !== false);

$user_id = null;
$customer_name = null;
$customer_address = null;
$purpose = null;
$notes = "";
$latitude = null;
$longitude = null;
$image_filename = null;

if ($is_json) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (
        !empty($data->user_id) &&
        !empty($data->customer_name) &&
        !empty($data->customer_address) &&
        !empty($data->purpose) &&
        isset($data->latitude) &&
        isset($data->longitude)
    ) {
        $user_id = intval($data->user_id);
        $customer_name = htmlspecialchars(strip_tags($data->customer_name));
        $customer_address = htmlspecialchars(strip_tags($data->customer_address));
        $purpose = htmlspecialchars(strip_tags($data->purpose));
        $notes = isset($data->notes) ? htmlspecialchars(strip_tags($data->notes)) : "";
        $latitude = floatval($data->latitude);
        $longitude = floatval($data->longitude);
        
        // Handle base64 image if provided
        if (!empty($data->image)) {
            // Check if it's data URI (e.g. data:image/jpeg;base64,...)
            $base64_string = $data->image;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type)) {
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                $image_type = strtolower($type[1]); // jpg, png, etc.
            } else {
                $image_type = 'jpg';
            }
            
            $base64_string = str_replace(' ', '+', $base64_string);
            $decoded_image = base64_decode($base64_string);
            
            if ($decoded_image !== false) {
                $image_filename = "visit_" . time() . "_" . uniqid() . "." . $image_type;
                $file_path = $upload_dir . $image_filename;
                
                if (!file_put_contents($file_path, $decoded_image)) {
                    $image_filename = null; // Failed to write file
                }
            }
        }
    }
} else {
    // Treat as multipart/form-data
    if (
        !empty($_POST['user_id']) &&
        !empty($_POST['customer_name']) &&
        !empty($_POST['customer_address']) &&
        !empty($_POST['purpose']) &&
        isset($_POST['latitude']) &&
        isset($_POST['longitude'])
    ) {
        $user_id = intval($_POST['user_id']);
        $customer_name = htmlspecialchars(strip_tags($_POST['customer_name']));
        $customer_address = htmlspecialchars(strip_tags($_POST['customer_address']));
        $purpose = htmlspecialchars(strip_tags($_POST['purpose']));
        $notes = isset($_POST['notes']) ? htmlspecialchars(strip_tags($_POST['notes'])) : "";
        $latitude = floatval($_POST['latitude']);
        $longitude = floatval($_POST['longitude']);
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            // Allow certain formats
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_ext, $allowed)) {
                $image_filename = "visit_" . time() . "_" . uniqid() . "." . $file_ext;
                $file_path = $upload_dir . $image_filename;
                
                if (!move_uploaded_file($file_tmp, $file_path)) {
                    $image_filename = null;
                }
            }
        }
    }
}

if ($user_id && $customer_name && $customer_address && $purpose && isset($latitude) && isset($longitude)) {
    try {
        $query = "INSERT INTO visits (user_id, customer_name, customer_address, purpose, notes, latitude, longitude, image) 
                  VALUES (:user_id, :customer_name, :customer_address, :purpose, :notes, :latitude, :longitude, :image)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":customer_name", $customer_name);
        $stmt->bindParam(":customer_address", $customer_address);
        $stmt->bindParam(":purpose", $purpose);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":latitude", $latitude);
        $stmt->bindParam(":longitude", $longitude);
        $stmt->bindParam(":image", $image_filename);
        
        if ($stmt->execute()) {
            sendResponse(201, array(
                "message" => "Visit was logged successfully.",
                "visit" => array(
                    "id" => intval($db->lastInsertId()),
                    "customer_name" => $customer_name,
                    "customer_address" => $customer_address,
                    "purpose" => $purpose,
                    "notes" => $notes,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "image" => $image_filename
                )
            ));
        } else {
            sendResponse(500, array("message" => "Unable to save visit."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to add visit. Data is incomplete. Required: user_id, customer_name, customer_address, purpose, latitude, longitude."));
}
?>

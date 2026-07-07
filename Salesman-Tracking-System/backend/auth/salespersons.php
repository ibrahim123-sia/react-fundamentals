<?php
require_once "../config/database.php";

// Fetch all salespersons
$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT id, name, email, phone, created_at FROM users WHERE role = 'salesman' ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $salespersons = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $salespersons[] = $row;
    }

    sendResponse(200, $salespersons);
} catch (PDOException $e) {
    sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
}
?>

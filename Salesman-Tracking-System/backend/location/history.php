<?php
require_once "../config/database.php";

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        $query = "SELECT id, latitude, longitude, tracked_at FROM locations WHERE user_id = :user_id ORDER BY tracked_at DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $history = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $history[] = array(
                "id" => intval($row['id']),
                "latitude" => floatval($row['latitude']),
                "longitude" => floatval($row['longitude']),
                "tracked_at" => $row['tracked_at']
            );
        }

        sendResponse(200, $history);
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to fetch location history. User ID is required in query parameters (e.g. ?user_id=X)."));
}
?>

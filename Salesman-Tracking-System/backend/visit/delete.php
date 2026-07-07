<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"));
$id = 0;

if (!empty($data->id)) {
    $id = intval($data->id);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if ($id > 0) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Find visit to get image filename
        $query_find = "SELECT image FROM visits WHERE id = :id";
        $stmt_find = $db->prepare($query_find);
        $stmt_find->bindParam(":id", $id);
        $stmt_find->execute();

        if ($stmt_find->rowCount() > 0) {
            $row = $stmt_find->fetch(PDO::FETCH_ASSOC);
            $image_filename = $row['image'];

            // Delete database record
            $query_delete = "DELETE FROM visits WHERE id = :id";
            $stmt_delete = $db->prepare($query_delete);
            $stmt_delete->bindParam(":id", $id);

            if ($stmt_delete->execute()) {
                // Delete photo from filesystem if it exists
                if ($image_filename) {
                    $file_path = "../uploads/" . $image_filename;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                sendResponse(200, array("message" => "Visit was deleted successfully."));
            } else {
                sendResponse(500, array("message" => "Unable to delete visit."));
            }
        } else {
            sendResponse(404, array("message" => "Visit not found."));
        }
    } catch (PDOException $e) {
        sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    sendResponse(400, array("message" => "Unable to delete visit. ID is required."));
}
?>

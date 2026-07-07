<?php
require_once "../config/database.php";

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$date = isset($_GET['date']) ? htmlspecialchars(strip_tags($_GET['date'])) : '';

$database = new Database();
$db = $database->getConnection();

try {
    // We want to join with users table to get the name of the salesman
    $query = "SELECT v.*, u.name as salesman_name, u.email as salesman_email, u.phone as salesman_phone 
              FROM visits v 
              JOIN users u ON v.user_id = u.id";
    
    $conditions = array();
    $params = array();
    
    if ($user_id > 0) {
        $conditions[] = "v.user_id = :user_id";
        $params[':user_id'] = $user_id;
    }
    
    if (!empty($date)) {
        $conditions[] = "DATE(v.visit_date) = :date";
        $params[':date'] = $date;
    }
    
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $query .= " ORDER BY v.visit_date DESC";
    
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->execute();
    
    $visits = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $visits[] = array(
            "id" => intval($row['id']),
            "user_id" => intval($row['user_id']),
            "salesman_name" => $row['salesman_name'],
            "salesman_email" => $row['salesman_email'],
            "salesman_phone" => $row['salesman_phone'],
            "customer_name" => $row['customer_name'],
            "customer_address" => $row['customer_address'],
            "purpose" => $row['purpose'],
            "notes" => $row['notes'],
            "latitude" => floatval($row['latitude']),
            "longitude" => floatval($row['longitude']),
            "image" => $row['image'],
            "visit_date" => $row['visit_date']
        );
    }
    
    sendResponse(200, $visits);
} catch (PDOException $e) {
    sendResponse(500, array("message" => "Database error: " . $e->getMessage()));
}
?>

<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

include("../../../includes/db.php");

try {
    $result = $conn->query("SELECT * FROM dbusers ORDER BY userID ASC");
    $users = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format dates
            $createdDate = '';
            if (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00') {
                $cleanDate = preg_replace('/\.\d+/', '', $row['created_at']);
                $timestamp = strtotime($cleanDate);
                if ($timestamp !== false) {
                    $date = date('F j, Y', $timestamp);
                    $time = date('g:ia', $timestamp);
                    $createdDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                } else {
                    $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
                }
            } else {
                $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
            }
            
            $updatedDate = '';
            if (!empty($row['updated_at']) && $row['updated_at'] !== '0000-00-00 00:00:00') {
                $cleanDate = preg_replace('/\.\d+/', '', $row['updated_at']);
                $timestamp = strtotime($cleanDate);
                if ($timestamp !== false) {
                    $date = date('F j, Y', $timestamp);
                    $time = date('g:ia', $timestamp);
                    $updatedDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                } else {
                    $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
                }
            } else {
                $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
            }
            
            $users[] = [
                'userID' => $row['userID'],
                'userName' => $row['userName'],
                'userRole' => $row['userRole'],
                'created_at' => $createdDate,
                'updated_at' => $updatedDate,
                'canDelete' => (isset($_SESSION["userRole"]) && $_SESSION["userRole"] === "Admin" && $row['userRole'] === "Employee")
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['users' => $users]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

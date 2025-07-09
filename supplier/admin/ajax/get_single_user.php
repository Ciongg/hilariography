<?php
header('Content-Type: application/json');
include("../../../includes/db.php");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

$userId = intval($_GET['id']);

$stmt = $conn->prepare("SELECT userID, userName, userRole, created_at, updated_at FROM dbusers WHERE userID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if ($row !== null) {
        // Format dates
        $createdDate = '';
        if (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00') {
            $cleanDate = preg_replace('/\.\d+/', '', $row['created_at']);
            $timestamp = strtotime($cleanDate);
            if ($timestamp !== false) {
                $date = date('F j, Y', $timestamp);
                $time = date('g:ia', $timestamp);
                $createdDate = $date . " - " . $time;
            } else {
                $createdDate = "Not available";
            }
        } else {
            $createdDate = "Not available";
        }

        $updatedDate = '';
        if (!empty($row['updated_at']) && $row['updated_at'] !== '0000-00-00 00:00:00') {
            $cleanDate = preg_replace('/\.\d+/', '', $row['updated_at']);
            $timestamp = strtotime($cleanDate);
            if ($timestamp !== false) {
                $date = date('F j, Y', $timestamp);
                $time = date('g:ia', $timestamp);
                $updatedDate = $date . " - " . $time;
            } else {
                $updatedDate = "Not updated";
            }
        } else {
            $updatedDate = "Not updated";
        }
        
        $user = [
            'userID' => $row['userID'],
            'userName' => htmlspecialchars($row['userName']),
            'userRole' => htmlspecialchars($row['userRole']),
            'created_at' => $createdDate,
            'updated_at' => $updatedDate
        ];
        
        echo json_encode(['success' => true, 'user' => $user, 'timestamp' => time()]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
?>

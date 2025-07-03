<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

include("../../../includes/db.php");

try {
    // Test database connection
    $userCount = $conn->query("SELECT COUNT(*) as count FROM dbusers")->fetch_assoc()['count'];
    $productCount = $conn->query("SELECT COUNT(*) as count FROM dbproduct")->fetch_assoc()['count'];
    
    // Check temp directory
    $tempDir = '../../../temp/';
    $tempWritable = is_writable($tempDir);
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'online',
        'timestamp' => time(),
        'user_count' => $userCount,
        'product_count' => $productCount,
        'temp_writable' => $tempWritable,
        'logged_user' => $_SESSION['username']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'System error: ' . $e->getMessage()]);
}
?>

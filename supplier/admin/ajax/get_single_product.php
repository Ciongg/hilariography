<?php
header('Content-Type: application/json');
include("../../../includes/db.php");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Product ID is required']);
    exit();
}

$productId = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM dbproduct WHERE prodID = ?");
$stmt->bind_param("i", $productId);
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
        
        $product = [
            'prodID' => $row['prodID'],
            'prodName' => htmlspecialchars($row['prodName']),
            'prodDescription' => htmlspecialchars($row['prodDescription']),
            'price' => number_format($row['price'], 2),
            'quantity' => $row['quantity'],
            'color' => htmlspecialchars($row['color'] ?: ''),
            'size' => htmlspecialchars($row['size'] ?: ''),
            'image_path' => htmlspecialchars($row['image_path'] ?: ''),
            'created_at' => $createdDate,
            'updated_at' => $updatedDate
        ];
        
        echo json_encode(['success' => true, 'product' => $product, 'timestamp' => time()]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'Product not found']);
}

$stmt->close();
?>

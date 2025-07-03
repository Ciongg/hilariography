<?php
header('Content-Type: application/json');
include("../../includes/db.php");

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
        $imagePath = !empty($row['image_path']) ? "../uploads/" . htmlspecialchars($row['image_path']) : 'assets/img/no-image.png';
        
        $product = [
            'prodID' => $row['prodID'],
            'prodName' => htmlspecialchars($row['prodName']),
            'prodDescription' => htmlspecialchars($row['prodDescription']),
            'price' => number_format($row['price'], 2),
            'quantity' => $row['quantity'],
            'color' => htmlspecialchars($row['color'] ?? ''),
            'size' => htmlspecialchars($row['size'] ?? ''),
            'imagePath' => $imagePath,
            'imageExists' => !empty($row['image_path']) && file_exists("../../uploads/" . $row['image_path']),
            'updated_at' => $row['updated_at'] ?? ''
        ];
        
        echo json_encode(['success' => true, 'product' => $product, 'timestamp' => time()]);
    } else {
        echo json_encode(['error' => 'Product unavailable']);
    }
} else {
    echo json_encode(['error' => 'Product unavailable']);
}

$stmt->close();
?>

<?php
header('Content-Type: application/json');
include("../../includes/db.php");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$result = $conn->query("SELECT * FROM dbproduct ORDER BY prodID ASC");
$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row !== null) {
            $imagePath = !empty($row['image_path']) ? "../uploads/" . htmlspecialchars($row['image_path']) : 'assets/img/no-image.png';
            
            $products[] = [
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
        }
    }
}

echo json_encode(['products' => $products, 'timestamp' => time()]);
?>

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../utils/helpers.php';
require_once '../models/Product.php';

// Get product ID from query parameter
$productId = $_GET['id'] ?? null;

if (!$productId) {
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
    exit;
}

try {
    $productModel = new Product();
    $product = $productModel->getSingleProductForAjax($productId);
    
    if ($product) {
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch product: ' . $e->getMessage()
    ]);
}
?>

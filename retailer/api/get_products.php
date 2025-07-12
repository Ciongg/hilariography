<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../utils/helpers.php';
require_once '../models/Product.php';

try {
    $productModel = new Product();
    $products = $productModel->getProductsForAjax();
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
?>

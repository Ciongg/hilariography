<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/helpers.php';

class Product {
    private $db;
    private $supplierDb;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Connect to supplier database for products
        try {
            $this->supplierDb = new mysqli(DB_HOST, DB_USER, DB_PASS, SUPPLIER_DB_NAME);
            if ($this->supplierDb->connect_error) {
                throw new Exception("Supplier database connection failed: " . $this->supplierDb->connect_error);
            }
            $this->supplierDb->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Supplier database connection error: " . $e->getMessage());
        }
    }
    
    /**
     * Get all products from supplier database
     */
    public function getAll() {
        $sql = "SELECT * FROM dbproduct ORDER BY prodID ASC";
        $result = $this->supplierDb->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $this->formatProductData($row);
        }
        
        return $products;
    }
    
    /**
     * Get product by ID from supplier database
     */
    public function getById($id) {
        $id = $this->supplierDb->real_escape_string($id);
        $sql = "SELECT * FROM dbproduct WHERE prodID = '$id'";
        $result = $this->supplierDb->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $this->formatProductData($result->fetch_assoc());
        }
        
        return null;
    }
    
    /**
     * Search products
     */
    public function search($term, $minPrice = null, $maxPrice = null) {
        $term = $this->supplierDb->real_escape_string($term);
        $sql = "SELECT * FROM dbproduct WHERE prodName LIKE '%$term%'";
        
        if ($minPrice !== null) {
            $minPrice = (float)$minPrice;
            $sql .= " AND price >= $minPrice";
        }
        
        if ($maxPrice !== null) {
            $maxPrice = (float)$maxPrice;
            $sql .= " AND price <= $maxPrice";
        }
        
        $sql .= " ORDER BY prodID ASC";
        
        $result = $this->supplierDb->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $this->formatProductData($row);
        }
        
        return $products;
    }
    
    /**
     * Get products for AJAX response
     */
    public function getProductsForAjax() {
        $products = $this->getAll();
        return array_map(function($product) {
            return [
                'prodID' => $product['prodID'],
                'prodName' => $product['prodName'],
                'prodDescription' => $product['prodDescription'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'color' => $product['color'],
                'size' => $product['size'],
                'image_path' => $product['image_path'],
                'imagePath' => $product['imagePath'],
                'imageExists' => $product['imageExists'],
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ];
        }, $products);
    }
    
    /**
     * Get single product for AJAX response
     */
    public function getSingleProductForAjax($id) {
        $product = $this->getById($id);
        if ($product) {
            return [
                'prodID' => $product['prodID'],
                'prodName' => $product['prodName'],
                'prodDescription' => $product['prodDescription'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'color' => $product['color'],
                'size' => $product['size'],
                'image_path' => $product['image_path'],
                'imagePath' => $product['imagePath'],
                'imageExists' => $product['imageExists'],
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ];
        }
        return null;
    }
    
    /**
     * Format product data for display
     */
    private function formatProductData($row) {
        $imagePath = $row['image_path'] ?? '';
        $imageExists = Helpers::productImageExists($imagePath);
        $displayImagePath = Helpers::getProductImagePath($imagePath);
        
        return [
            'prodID' => $row['prodID'],
            'prodName' => $row['prodName'],
            'prodDescription' => $row['prodDescription'],
            'quantity' => $row['quantity'],
            'price' => Helpers::formatPrice($row['price']),
            'color' => $row['color'] ?: 'N/A',
            'size' => $row['size'] ?: 'N/A',
            'image_path' => $imagePath,
            'imagePath' => $displayImagePath,
            'imageExists' => $imageExists,
            'created_at' => Helpers::formatDate($row['created_at']),
            'updated_at' => Helpers::formatDate($row['updated_at'])
        ];
    }
    
    /**
     * Close database connections
     */
    public function __destruct() {
        if ($this->supplierDb) {
            $this->supplierDb->close();
        }
    }
} 
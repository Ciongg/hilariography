<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/helpers.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all products
     */
    public function getAll() {
        $sql = "SELECT * FROM dbproduct ORDER BY prodID ASC";
        $result = $this->db->query($sql);
        
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
     * Get product by ID
     */
    public function getById($id) {
        $id = $this->db->escape($id);
        $sql = "SELECT * FROM dbproduct WHERE prodID = '$id'";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $this->formatProductData($result->fetch_assoc());
        }
        
        return null;
    }
    
    /**
     * Create new product
     */
    public function create($data) {
        $name = $this->db->escape($data['productName']);
        $description = $this->db->escape($data['productDescription']);
        $quantity = (int)$data['productQty'];
        $price = (float)$data['productPrice'];
        $color = $this->db->escape($data['productColor'] ?? '');
        $size = $this->db->escape($data['productSize'] ?? '');
        $imagePath = $this->db->escape($data['image_path'] ?? '');
        
        $sql = "INSERT INTO dbproduct (prodName, prodDescription, quantity, price, color, size, image_path, created_at, updated_at) 
                VALUES ('$name', '$description', $quantity, $price, '$color', '$size', '$imagePath', NOW(), NOW())";
        
        if ($this->db->query($sql)) {
            return $this->db->getConnection()->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update product
     */
    public function update($id, $data) {
        $id = $this->db->escape($id);
        $name = $this->db->escape($data['productName']);
        $description = $this->db->escape($data['productDescription']);
        $quantity = (int)$data['productQty'];
        $price = (float)$data['productPrice'];
        $color = $this->db->escape($data['productColor'] ?? '');
        $size = $this->db->escape($data['productSize'] ?? '');
        $imagePath = $this->db->escape($data['image_path'] ?? '');
        
        $sql = "UPDATE dbproduct SET 
                prodName = '$name', 
                prodDescription = '$description', 
                quantity = $quantity, 
                price = $price, 
                color = '$color', 
                size = '$size', 
                image_path = '$imagePath', 
                updated_at = NOW() 
                WHERE prodID = '$id'";
        
        return $this->db->query($sql);
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        $id = $this->db->escape($id);
        
        // Get image path before deletion
        $product = $this->getById($id);
        if ($product && $product['image_path']) {
            $imagePath = UPLOAD_PATH . $product['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $sql = "DELETE FROM dbproduct WHERE prodID = '$id'";
        return $this->db->query($sql);
    }
    
    /**
     * Handle image upload
     */
    public function handleImageUpload($file, $oldImagePath = null) {
        // Remove old image if exists
        if ($oldImagePath && file_exists(UPLOAD_PATH . $oldImagePath)) {
            unlink(UPLOAD_PATH . $oldImagePath);
        }
        
        // Validate new image
        $validation = Helpers::validateImage($file);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }
        
        // Generate unique filename
        $filename = Helpers::generateUniqueFilename($file['name']);
        $uploadPath = UPLOAD_PATH . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload image'];
    }
    
    /**
     * Format product data for display
     */
    private function formatProductData($row) {
        return [
            'prodID' => $row['prodID'],
            'prodName' => $row['prodName'],
            'prodDescription' => $row['prodDescription'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'color' => $row['color'] ?: 'N/A',
            'size' => $row['size'] ?: 'N/A',
            'image_path' => $row['image_path'],
            'created_at' => Helpers::formatDate($row['created_at']),
            'updated_at' => Helpers::formatDate($row['updated_at'])
        ];
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
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ];
        }, $products);
    }
    
    /**
     * Search products
     */
    public function search($term, $minPrice = null, $maxPrice = null) {
        $term = $this->db->escape($term);
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
        
        $result = $this->db->query($sql);
        
        if (!$result) {
            return [];
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $this->formatProductData($row);
        }
        
        return $products;
    }
} 
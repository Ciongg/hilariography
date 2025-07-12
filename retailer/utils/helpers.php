<?php
require_once __DIR__ . '/../config/config.php';

class Helpers {
    
    /**
     * Format date for display
     */
    public static function formatDate($dateString, $includeTime = true) {
        if (empty($dateString) || $dateString === '0000-00-00 00:00:00') {
            return "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
        }
        
        // Clean microseconds from timestamp
        $cleanDate = preg_replace('/\.\d+/', '', $dateString);
        $timestamp = strtotime($cleanDate);
        
        if ($timestamp === false) {
            return "<span style='color: #9ca3af; font-style: italic;'>Invalid date</span>";
        }
        
        $date = date('F j, Y', $timestamp);
        if ($includeTime) {
            $time = date('g:ia', $timestamp);
            return $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
        }
        
        return $date;
    }
    
    /**
     * Format price for display
     */
    public static function formatPrice($price) {
        return '₱' . number_format((float)$price, 2);
    }
    
    /**
     * Truncate text with ellipsis
     */
    public static function truncateText($text, $length = 50) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
    
    /**
     * Validate image file
     */
    public static function validateImage($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['valid' => false, 'error' => 'File size too large'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate unique filename
     */
    public static function generateUniqueFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        return $timestamp . '_' . $randomString . '.' . $extension;
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['valid' => false, 'error' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }
        return ['valid' => true];
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin($userRole) {
        return $userRole === USER_ROLE_ADMIN;
    }
    
    /**
     * Check if user is regular user
     */
    public static function isUser($userRole) {
        return $userRole === USER_ROLE_USER;
    }
    
    /**
     * Get user role display name
     */
    public static function getUserRoleDisplay($userRole) {
        switch ($userRole) {
            case USER_ROLE_ADMIN:
                return 'Admin';
            case USER_ROLE_USER:
                return 'User';
            default:
                return 'Unknown';
        }
    }
    
    /**
     * Validate order quantity
     */
    public static function validateOrderQuantity($quantity, $availableQuantity) {
        $quantity = (int)$quantity;
        
        if ($quantity < MIN_ORDER_QUANTITY) {
            return ['valid' => false, 'error' => 'Minimum order quantity is ' . MIN_ORDER_QUANTITY];
        }
        
        if ($quantity > MAX_ORDER_QUANTITY) {
            return ['valid' => false, 'error' => 'Maximum order quantity is ' . MAX_ORDER_QUANTITY];
        }
        
        if ($quantity > $availableQuantity) {
            return ['valid' => false, 'error' => 'Order quantity exceeds available stock'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Calculate total price
     */
    public static function calculateTotal($price, $quantity) {
        return $price * $quantity;
    }
    
    /**
     * Create JSON response
     */
    public static function jsonResponse($success, $data = null, $message = '') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message
        ]);
        exit;
    }
    
    /**
     * Redirect with message
     */
    public static function redirect($url, $message = '', $type = 'success') {
        if ($message) {
            $_SESSION['message'] = $message;
            $_SESSION['message_type'] = $type;
        }
        header("Location: $url");
        exit;
    }
    
    /**
     * Display flash message
     */
    public static function displayMessage() {
        if (isset($_SESSION['message'])) {
            $type = $_SESSION['message_type'] ?? 'success';
            $message = $_SESSION['message'];
            unset($_SESSION['message'], $_SESSION['message_type']);
            
            $class = $type === 'error' ? 'error' : 'success';
            return "<div class='alert alert-{$class}'>{$message}</div>";
        }
        return '';
    }
    
    /**
     * Get product image path
     */
    public static function getProductImagePath($imagePath) {
        if (empty($imagePath)) {
            return 'assets/img/hilariography_logo.png';
        }
        
        // Use absolute path from project root
        $fullPath = __DIR__ . '/../../uploads/' . $imagePath;
        return file_exists($fullPath) ? '../uploads/' . $imagePath : 'assets/img/hilariography_logo.png';
    }
    
    /**
     * Check if product image exists
     */
    public static function productImageExists($imagePath) {
        if (empty($imagePath)) {
            return false;
        }
        
        // Use absolute path from project root
        $fullPath = __DIR__ . '/../../uploads/' . $imagePath;
        return file_exists($fullPath);
    }
} 
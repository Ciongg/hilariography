<?php
// Application configuration
define('APP_NAME', 'Supplier Dashboard');
define('APP_VERSION', '1.0.0');
define('UPLOAD_PATH', '../../uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('REAL_TIME_UPDATE_INTERVAL', 2000); // 2 seconds

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'supplier_session');

// Pagination settings
define('ITEMS_PER_PAGE', 20);

// Security settings
define('PASSWORD_MIN_LENGTH', 6);
define('CSRF_TOKEN_NAME', 'csrf_token');

// Notification settings
define('NOTIFICATION_DURATION', 3000); // 3 seconds
define('CONFLICT_NOTIFICATION_DURATION', 5000); // 5 seconds

// Color options for products
define('PRODUCT_COLORS', [
    'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Pink', 
    'Brown', 'Black', 'White', 'Gray', 'Navy', 'Maroon', 'Beige', 
    'Turquoise', 'Gold', 'Silver'
]);

// Size options for products
define('PRODUCT_SIZES', [
    'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'
]);

// Price validation
define('MIN_PRODUCT_PRICE', 5.00);
define('MAX_PRODUCT_PRICE', 999999.99); 
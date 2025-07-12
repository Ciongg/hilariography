<?php
// Application configuration for retailer
define('APP_NAME', 'Hilariography');
define('APP_TAGLINE', 'The Sample Royals of Print!');
define('APP_VERSION', '1.0.0');
define('SUPPLIER_DB_NAME', 'dbfabricsupplier');
define('UPLOAD_PATH', '../uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('REAL_TIME_UPDATE_INTERVAL', 2000); // 2 seconds

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'retailer_session');

// User roles
define('USER_ROLE_ADMIN', 'admin');
define('USER_ROLE_USER', 'user');

// Pagination settings
define('ITEMS_PER_PAGE', 20);

// Security settings
define('PASSWORD_MIN_LENGTH', 6);
define('CSRF_TOKEN_NAME', 'csrf_token');

// Notification settings
define('NOTIFICATION_DURATION', 3000); // 3 seconds
define('CONFLICT_NOTIFICATION_DURATION', 5000); // 5 seconds

// Price validation
define('MIN_PRODUCT_PRICE', 5.00);
define('MAX_PRODUCT_PRICE', 999999.99);

// Order settings
define('MIN_ORDER_QUANTITY', 1);
define('MAX_ORDER_QUANTITY', 100); 
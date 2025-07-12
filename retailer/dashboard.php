<?php
session_start();

// Check if user is logged in as retailer
if (!isset($_SESSION["retailer_loggedin"]) || $_SESSION["retailer_loggedin"] !== true) {
    header("Location: auth/login.php");
    exit();
}

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'utils/helpers.php';

// Get user role
$userRole = $_SESSION["retailer_role"] ?? 'user';
$isAdmin = $userRole === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo APP_NAME; ?> - <?php echo $isAdmin ? 'Admin Dashboard' : 'Product Catalog'; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css" />
</head>
<body>
    <div class="container">
        <?php include 'views/components/header.php'; ?>
        
        <div class="content">
            <div class="content-header">
                <h2 class="content-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="content-title-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <?php echo $isAdmin ? 'Product Management (Admin View)' : 'Product Catalog'; ?>
                </h2>
                <div class="filters-container">
                    <div class="search-container">
                        <div class="search-input-wrapper">
                            <div class="search-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <input type="text" class="search-input" placeholder="Search products..." id="searchInput">
                        </div>
                    </div>
                    <div class="price-filter-container">
                        <div class="price-inputs">
                            <input type="number" class="price-input" placeholder="Min ₱" id="minPrice" min="0" step="0.01">
                            <span class="price-separator">-</span>
                            <input type="number" class="price-input" placeholder="Max ₱" id="maxPrice" min="0" step="0.01">
                        </div>
                        <button class="filter-btn" onclick="applyFilters()">Filter</button>
                        <button class="clear-filter-btn" onclick="clearFilters()">Clear</button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="products-grid" id="productsGrid">
                    <?php
                    require_once 'models/Product.php';
                    $productModel = new Product();
                    $products = $productModel->getAll();
                    
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            include 'views/components/product_card.php';
                        }
                    } else {
                        echo '<div class="no-products-found">
                            <div class="no-products-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <div class="no-products-title">No Products Available</div>
                            <div class="no-products-message">There are currently no products in the catalog. Please check back later.</div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'views/components/product_modal.php'; ?>

    <script>
        // Pass user role to JavaScript
        window.userRole = '<?php echo $userRole; ?>';
        window.isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    </script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html> 
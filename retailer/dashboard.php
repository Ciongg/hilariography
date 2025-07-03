<?php
session_start();

// Check if user is logged in as retailer
if (!isset($_SESSION["retailer_loggedin"]) || $_SESSION["retailer_loggedin"] !== true) {
    header("Location: auth/login.php");
    exit();
}

include("../includes/db.php"); // Updated to include db.php

// Verify database connection
if (!$conn) {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hilariography - Product List</title>
    <link rel="stylesheet" href="assets/css/dashboard.css" />
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <img src="assets/img/hilariography_logo.png" alt="Hilariography Logo" class="logo-image">
                    <div>
                        <h1>Hilariography</h1>
                        <p>The Sample Royals of Print!</p>
                    </div>
                </div>
                <div class="user-section">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr(htmlspecialchars($_SESSION["retailer_username"])[0], 0, 1)); ?>
                    </div>
                    <span class="user-name">
                        <?php echo htmlspecialchars($_SESSION["retailer_username"]); ?>
                    </span>
                    <a href="auth/logout.php" class="logout-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="logout-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="content-header">
                <h2 class="content-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="content-title-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    All Product List
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
                    $result = $conn->query("SELECT * FROM dbproduct");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row === null) {
                                continue;
                            }
                            
                            $imagePath = !empty($row['image_path']) ? "../uploads/" . htmlspecialchars($row['image_path']) : 'assets/img/no-image.png';
                            $productName = htmlspecialchars($row['prodName']);
                            $productDesc = htmlspecialchars($row['prodDescription']);
                            $price = number_format($row['price'], 2);
                            $quantity = $row['quantity'];
                            $color = htmlspecialchars($row['color']);
                            $size = htmlspecialchars($row['size']);
                            $prodID = $row['prodID'];
                    ?>
                    <div class="product-card" data-search="<?php echo strtolower($productName); ?>">
                        <div class="product-image-container">
                            <?php if (!empty($row['image_path']) && file_exists("../uploads/" . $row['image_path'])): ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $productName; ?>" class="product-card-image">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <div class="no-image-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </div>
                                    <div class="no-image-text">No Image</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-content">
                                <h3 class="product-name"><?php echo $productName; ?></h3>
                                <p class="product-description"><?php echo $productDesc; ?></p>
                                <div class="product-details">
                                    <span class="product-price">₱<?php echo $price; ?></span>
                                    <span class="product-quantity">Qty: <?php echo $quantity; ?></span>
                                </div>
                            </div>
                            <div class="product-attributes-row">
                                <div class="product-attributes">
                                    <?php if (!empty($color)): ?>
                                        <span class="attribute-tag"><?php echo $color; ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($size)): ?>
                                        <span class="attribute-tag"><?php echo $size; ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="view-details-btn" onclick="openProductModal('<?php echo $prodID; ?>', '<?php echo addslashes($productName); ?>', '<?php echo addslashes($productDesc); ?>', '<?php echo $price; ?>', '<?php echo $quantity; ?>', '<?php echo addslashes($color); ?>', '<?php echo addslashes($size); ?>', '<?php echo addslashes($imagePath); ?>')">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<div class="no-products">No products found.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalProductName">Product Details</h2>
                <div class="modal-actions">
                
                    <span class="close" onclick="closeProductModal()">&times;</span>
                </div>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div id="modalLoadingState" class="modal-loading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Loading product details...</div>
                </div>
                
                <!-- Error State -->
                <div id="modalErrorState" class="modal-error" style="display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 20px; min-height: 300px; width: 100%; opacity: 1; visibility: visible;">
                    <div class="error-icon" style="width: 64px; height: 64px; color: #ef4444; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 100%; height: 100%;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="error-title" style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 12px;">Product Unavailable</div>
                    <div class="error-message" id="modalErrorMessage" style="color: #6b7280; margin-bottom: 24px; max-width: 400px; line-height: 1.5;">Sorry, this product is currently unavailable. It may have been removed or is temporarily out of stock.</div>
                    <div class="error-actions" style="display: flex; gap: 12px; align-items: center;">
                       
                        <button class="close-btn" onclick="closeProductModal()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s;">Close</button>
                    </div>
                </div>
                
                <!-- Product Content -->
                <div id="modalProductContent" class="modal-product-content">
                    <div class="modal-product-image-container">
                        <img id="modalProductImage" src="" alt="Product Image" class="modal-product-image" style="display: none;">
                        <div id="modalNoImagePlaceholder" class="modal-no-image-placeholder" style="display: none;">
                            <div class="modal-no-image-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <div class="modal-no-image-text">No Image</div>
                        </div>
                    </div>
                    <div class="modal-product-info">
                        <!-- Full width description -->
                        <div class="info-row full-width">
                            <span class="info-label">Description:</span>
                            <span class="info-value" id="modalProductDescription"></span>
                        </div>
                        
                        <!-- 2-column grid for other details -->
                        <div class="info-grid">
                            <div class="info-row">
                                <span class="info-label">Price:</span>
                                <span class="info-value price" id="modalProductPrice"></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Quantity:</span>
                                <span class="info-value" id="modalProductQuantity"></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Color:</span>
                                <span class="info-value" id="modalProductColor"></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Size:</span>
                                <span class="info-value" id="modalProductSize"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        let productCards = document.querySelectorAll('.product-card');

        // Real-time updates - improved detection
        let lastProductData = null;
        let updateInterval;
        let isManualRefresh = false;

        // Modal real-time updates
        let currentProductId = null;
        let modalUpdateInterval = null;
        let lastModalUpdate = null;

        function startRealTimeUpdates() {
            // Initial data fetch
            checkForUpdates();
            updateInterval = setInterval(checkForUpdates, 2000); // Check every 2 seconds
        }

        function stopRealTimeUpdates() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        }

        function startModalUpdates(productId) {
            currentProductId = productId;
            // Start checking for updates every 2 seconds
            modalUpdateInterval = setInterval(() => {
                updateModalProduct(productId, false);
            }, 2000);
        }

        function stopModalUpdates() {
            if (modalUpdateInterval) {
                clearInterval(modalUpdateInterval);
                modalUpdateInterval = null;
            }
            currentProductId = null;
            lastModalUpdate = null;
        }

        // Manual refresh function for user interactions
        async function refreshData(showNotification = false) {
            isManualRefresh = true;
            try {
                const response = await fetch('api/get_products.php');
                const data = await response.json();
                
                if (data.error) {
                    console.error('Error fetching products:', data.error);
                    return null;
                }

                const currentDataHash = createDataHash(data.products);
                
                // Always update on manual refresh regardless of hash
                lastProductData = currentDataHash;
                updateProductsGridSmooth(data.products, showNotification);
                
                isManualRefresh = false;
                return data.products;
            } catch (error) {
                console.error('Error refreshing data:', error);
                isManualRefresh = false;
                return null;
            }
        }

        async function checkForUpdates() {
            // Skip automatic updates if manual refresh is in progress
            if (isManualRefresh) return;
            
            try {
                const response = await fetch('api/get_products.php');
                const data = await response.json();
                
                if (data.error) {
                    console.error('Error fetching products:', data.error);
                    return;
                }

                // Create a hash of current product data for comparison
                const currentDataHash = createDataHash(data.products);
                
                // Check if this is the first load or if data has changed
                if (lastProductData === null || lastProductData !== currentDataHash) {
                    const isInitialLoad = lastProductData === null;
                    lastProductData = currentDataHash;
                    
                    if (isInitialLoad) {
                        updateProductsGrid(data.products, false);
                    } else {
                        updateProductsGridSmooth(data.products, true);
                    }
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
            }
        }

        function createDataHash(products) {
            // Create a simple hash of product data to detect changes
            return products.map(p => 
                `${p.prodID}-${p.prodName}-${p.quantity}-${p.price}-${p.prodDescription}-${p.color}-${p.size}-${p.updated_at}`
            ).join('|');
        }

        // Smooth update that preserves existing DOM structure and animations
        function updateProductsGridSmooth(products, showNotification = false) {
            const productsGrid = document.getElementById('productsGrid');
            const existingCards = productsGrid.querySelectorAll('.product-card');
            
            if (products.length === 0) {
                productsGrid.innerHTML = '<div class="no-products">No products found.</div>';
                productCards = [];
                return;
            }

            // Update existing cards or add new ones
            products.forEach(product => {
                let existingCard = Array.from(existingCards).find(card => 
                    card.getAttribute('data-product-id') === product.prodID.toString()
                );

                if (existingCard) {
                    // Update existing card content without rebuilding
                    updateExistingCard(existingCard, product);
                } else {
                    // Add new card if it doesn't exist
                    const newCardHTML = createProductCardHTML(product);
                    productsGrid.insertAdjacentHTML('beforeend', newCardHTML);
                }
            });

            // Remove cards for products that no longer exist
            const currentProductIds = products.map(p => p.prodID.toString());
            existingCards.forEach(card => {
                const cardProductId = card.getAttribute('data-product-id');
                if (!currentProductIds.includes(cardProductId)) {
                    card.remove();
                }
            });

            // Update productCards reference
            productCards = document.querySelectorAll('.product-card');
            
            // Reapply current filters
            applyFilters();
            
            // Show notification only if requested
            if (showNotification) {
                showUpdateNotification();
            }
        }

        // Update existing card without rebuilding the entire structure
        function updateExistingCard(cardElement, product) {
            // Update text content only
            const nameElement = cardElement.querySelector('.product-name');
            const descElement = cardElement.querySelector('.product-description');
            const priceElement = cardElement.querySelector('.product-price');
            const quantityElement = cardElement.querySelector('.product-quantity');
            const attributesContainer = cardElement.querySelector('.product-attributes');
            const viewButton = cardElement.querySelector('.view-details-btn');

            if (nameElement) nameElement.textContent = product.prodName;
            if (descElement) descElement.textContent = product.prodDescription;
            if (priceElement) priceElement.textContent = '₱' + product.price;
            if (quantityElement) quantityElement.textContent = 'Qty: ' + product.quantity;

            // Update attributes
            if (attributesContainer) {
                attributesContainer.innerHTML = '';
                if (product.color) {
                    attributesContainer.innerHTML += `<span class="attribute-tag">${product.color}</span>`;
                }
                if (product.size) {
                    attributesContainer.innerHTML += `<span class="attribute-tag">${product.size}</span>`;
                }
            }

            // Update button onclick
            if (viewButton) {
                viewButton.setAttribute('onclick', `openProductModalWithRefresh('${product.prodID}')`);
            }

            // Update search data attribute
            cardElement.setAttribute('data-search', product.prodName.toLowerCase());

            // Update image if needed
            const imageContainer = cardElement.querySelector('.product-image-container');
            if (imageContainer) {
                const existingImg = imageContainer.querySelector('.product-card-image');
                const existingPlaceholder = imageContainer.querySelector('.no-image-placeholder');

                if (product.imageExists) {
                    if (existingImg) {
                        existingImg.src = product.imagePath;
                        existingImg.alt = product.prodName;
                    } else {
                        // Replace placeholder with image
                        imageContainer.innerHTML = `<img src="${product.imagePath}" alt="${product.prodName}" class="product-card-image">`;
                    }
                } else {
                    if (existingPlaceholder) {
                        // Keep existing placeholder
                    } else {
                        // Replace image with placeholder
                        imageContainer.innerHTML = `
                            <div class="no-image-placeholder">
                                <div class="no-image-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                                <div class="no-image-text">No Image</div>
                            </div>`;
                    }
                }
            }
        }

        // Create HTML for a new product card
        function createProductCardHTML(product) {
            const imageHTML = product.imageExists ? 
                `<img src="${product.imagePath}" alt="${product.prodName}" class="product-card-image">` :
                `<div class="no-image-placeholder">
                    <div class="no-image-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                    <div class="no-image-text">No Image</div>
                </div>`;

            return `
                <div class="product-card" data-search="${product.prodName.toLowerCase()}" data-product-id="${product.prodID}">
                    <div class="product-image-container">
                        ${imageHTML}
                    </div>
                    <div class="product-info">
                        <div class="product-content">
                            <h3 class="product-name">${product.prodName}</h3>
                            <p class="product-description">${product.prodDescription}</p>
                            <div class="product-details">
                                <span class="product-price">₱${product.price}</span>
                                <span class="product-quantity">Qty: ${product.quantity}</span>
                            </div>
                        </div>
                        <div class="product-attributes-row">
                            <div class="product-attributes">
                                ${product.color ? `<span class="attribute-tag">${product.color}</span>` : ''}
                                ${product.size ? `<span class="attribute-tag">${product.size}</span>` : ''}
                            </div>
                            <button class="view-details-btn" onclick="openProductModalWithRefresh('${product.prodID}')">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Original grid update for initial load
        function updateProductsGrid(products, showNotification = false) {
            const productsGrid = document.getElementById('productsGrid');
            
            if (products.length === 0) {
                productsGrid.innerHTML = '<div class="no-products">No products found.</div>';
                productCards = [];
                return;
            }

            let gridHTML = '';
            products.forEach(product => {
                gridHTML += createProductCardHTML(product);
            });

            productsGrid.innerHTML = gridHTML;
            productCards = document.querySelectorAll('.product-card');
            
            // Reapply current filters
            applyFilters();
            
            // Show notification only if requested
            if (showNotification) {
                showUpdateNotification();
            }
        }

        function showUpdateNotification() {
            // Remove any existing notifications first
            const existingNotifications = document.querySelectorAll('.update-notification');
            existingNotifications.forEach(notif => notif.remove());
            
            // Create and show a subtle notification
            const notification = document.createElement('div');
            notification.className = 'update-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            notification.textContent = 'Data refreshed!';
            document.body.appendChild(notification);
            
            // Fade in
            setTimeout(() => notification.style.opacity = '1', 100);
            
            // Remove after 2 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 2000);
        }

        // Enhanced modal function that refreshes data first
        async function openProductModalWithRefresh(productId) {
            const modal = document.getElementById('productModal');
            
            // Show modal immediately
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            
            // Show loading state
            showModalState('loading');
            
            // Load product data
            await updateModalProduct(productId, true);
            
            // Start real-time updates for this product
            startModalUpdates(productId);
        }

        function showModalState(state) {
            const loadingState = document.getElementById('modalLoadingState');
            const errorState = document.getElementById('modalErrorState');
            const contentState = document.getElementById('modalProductContent');
            
            // Hide all states first with aggressive styling
            loadingState.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important;';
            errorState.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important;';
            contentState.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important;';
            
            // Show the requested state with aggressive inline styling
            switch (state) {
                case 'loading':
                    loadingState.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 20px; min-height: 300px; width: 100%;';
                    break;
                case 'error':
                    errorState.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 20px; min-height: 300px; width: 100%;';
                    break;
                case 'content':
                    contentState.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; width: 100%;';
                    break;
            }
        }

        async function updateModalProduct(productId, isInitialLoad = false) {
            try {
                const response = await fetch(`api/get_single_product.php?id=${productId}`);
                const data = await response.json();
                
                // Simple if-else: if data is valid and has product, show content, else show error
                if (data.success && data.product && data.product.prodName && data.product.prodID) {
                    const product = data.product;
                    
                    // Check if this is a significant update (for non-initial loads)
                    const currentDataHash = JSON.stringify(product);
                    const hasChanged = lastModalUpdate !== currentDataHash;
                    
                    if (isInitialLoad || hasChanged) {
                        lastModalUpdate = currentDataHash;
                        
                        // Update modal content
                        document.getElementById('modalProductName').textContent = product.prodName;
                        document.getElementById('modalProductDescription').textContent = product.prodDescription;
                        document.getElementById('modalProductPrice').textContent = '₱' + product.price;
                        document.getElementById('modalProductQuantity').textContent = product.quantity;
                        document.getElementById('modalProductColor').textContent = product.color || 'N/A';
                        document.getElementById('modalProductSize').textContent = product.size || 'N/A';
                        
                        // Update image
                        const modalImage = document.getElementById('modalProductImage');
                        const modalPlaceholder = document.getElementById('modalNoImagePlaceholder');
                        
                        if (product.imagePath && product.imagePath !== 'assets/img/no-image.png' && product.imageExists) {
                            modalImage.src = product.imagePath;
                            modalImage.style.display = 'block';
                            modalPlaceholder.style.display = 'none';
                        } else {
                            modalImage.style.display = 'none';
                            modalPlaceholder.style.display = 'flex';
                        }
                        
                        // Show content state
                        showModalState('content');
                        
                        // Show update notification for non-initial loads
                        if (!isInitialLoad && hasChanged) {
                            showModalUpdateNotification();
                        }
                    }
                } else {
                    // Show error state for any invalid/empty data
                    const errorMessageElement = document.getElementById('modalErrorMessage');
                    if (data.message) {
                        errorMessageElement.textContent = data.message;
                    } else {
                        errorMessageElement.textContent = 'Sorry, this product is currently unavailable. It may have been removed or is temporarily out of stock.';
                    }
                    showModalState('error');
                }
            } catch (error) {
                console.error('Error fetching product:', error);
                
                // Show error state for network/fetch errors
                const errorMessageElement = document.getElementById('modalErrorMessage');
                errorMessageElement.textContent = 'Unable to load product details. Please check your connection and try again.';
                showModalState('error');
            }
        }

        function showModalUpdateNotification() {
            // Create a subtle pulse effect on the modal content
            const modalContent = document.querySelector('.modal-content');
            modalContent.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
            
            setTimeout(() => {
                modalContent.style.boxShadow = '';
            }, 1000);
        }

      

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const minPrice = parseFloat(minPriceInput.value) || 0;
            const maxPrice = parseFloat(maxPriceInput.value) || Infinity;

            productCards.forEach(card => {
                const searchData = card.getAttribute('data-search');
                const priceElement = card.querySelector('.product-price');
                const priceText = priceElement ? priceElement.textContent.replace('₱', '').replace(',', '') : '0';
                const price = parseFloat(priceText);

                const matchesSearch = searchData.includes(searchTerm);
                const matchesPrice = price >= minPrice && price <= maxPrice;

                if (matchesSearch && matchesPrice) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Refresh data when filter button is clicked
            setTimeout(() => refreshData(false), 200);
        }

        function clearFilters() {
            searchInput.value = '';
            minPriceInput.value = '';
            maxPriceInput.value = '';
            applyFilters();
            
            // Refresh data when clear button is clicked
            setTimeout(() => refreshData(false), 200);
        }

        // Simplified event listeners - only for essential interactions
        document.addEventListener('DOMContentLoaded', function() {
            startRealTimeUpdates();
            
            // Only refresh when window regains focus (user returns to tab)
            window.addEventListener('focus', function() {
                refreshData(false);
            });
        });

        // Stop updates when page is not visible (performance optimization)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopRealTimeUpdates();
            } else {
                startRealTimeUpdates();
                // Refresh when user returns to the page
                refreshData(false);
            }
        });

        // Stop updates when page unloads
        window.addEventListener('beforeunload', function() {
            stopRealTimeUpdates();
        });

        // Keep the original modal function for backward compatibility
        function openProductModal(prodID, name, description, price, quantity, color, size, imagePath) {
            // Just call the new refresh version
            openProductModalWithRefresh(prodID);
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.remove('show');
            
            // Stop modal updates
            stopModalUpdates();
            
            setTimeout(() => modal.style.display = 'none', 150);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeProductModal();
            }
        }

        // Add keyboard support for modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && currentProductId) {
                closeProductModal();
            }
        });
    </script>
</body>

</html>

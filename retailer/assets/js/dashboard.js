// Simple Dashboard JavaScript functionality
class Dashboard {
    constructor() {
        this.updateInterval = null;
        this.currentProductId = null;
        this.currentProduct = null;
        this.isAdmin = window.isAdmin || false;
        this.userRole = window.userRole || 'user';
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.startAutoUpdates();
    }
    
    setupEventListeners() {
        // Search and filter inputs
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        if (searchInput) {
            searchInput.addEventListener('input', () => this.applyFilters());
        }
        if (minPriceInput) {
            minPriceInput.addEventListener('input', () => this.applyFilters());
        }
        if (maxPriceInput) {
            maxPriceInput.addEventListener('input', () => this.applyFilters());
        }
        
        // Modal click-outside functionality
        const modal = document.getElementById('productModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeProductModal();
                }
            });
        }
        
        // Keyboard support
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.currentProductId) {
                this.closeProductModal();
            }
        });
        
        // Stop updates when page is hidden, resume when visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoUpdates();
            } else {
                this.startAutoUpdates();
            }
        });
        
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            this.stopAutoUpdates();
        });
    }
    
    startAutoUpdates() {
        // Clear any existing interval
        this.stopAutoUpdates();
        
        // Start new 2-second interval
        this.updateInterval = setInterval(() => {
            this.refreshProducts();
        }, 2000);
    }
    
    stopAutoUpdates() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }
    
    async refreshProducts() {
        try {
            const response = await fetch('api/get_products.php');
            const data = await response.json();
            
            if (data.error) {
                console.error('Error fetching products:', data.error);
                return;
            }

            this.updateProductsGrid(data.products);
            
            // Check if current modal product still exists
            this.checkModalProductExists(data.products);
        } catch (error) {
            console.error('Error refreshing products:', error);
        }
    }
    
    checkModalProductExists(products) {
        // Only check if modal is open and we have a current product
        if (!this.currentProductId || !this.currentProduct) {
            return;
        }
        
        // Check if the current product still exists in the product list
        const productExists = products.some(product => 
            product.prodID.toString() === this.currentProductId.toString()
        );
        
        if (!productExists) {
            // Product was deleted, show deleted state
            this.showModalState('deleted');
        }
    }
    
    updateProductsGrid(products) {
        const productsGrid = document.getElementById('productsGrid');
        
        if (products.length === 0) {
            productsGrid.innerHTML = this.createNoProductsHTML();
            return;
        }

        let gridHTML = '';
        products.forEach(product => {
            gridHTML += this.createProductCardHTML(product);
        });

        productsGrid.innerHTML = gridHTML;
        
        // Reapply current filters
        this.applyFilters();
    }
    
    createNoProductsHTML() {
        return `
            <div class="no-products-found">
                <div class="no-products-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <div class="no-products-title">No Products Found</div>
                <div class="no-products-message">Try adjusting your search terms or filters to find what you're looking for.</div>
                <div class="no-products-actions">
                    <button class="clear-filters-btn" onclick="clearFilters()">Clear All Filters</button>
                </div>
            </div>
        `;
    }
    
    createProductCardHTML(product) {
        const imageHTML = product.imageExists && product.imagePath && product.imagePath !== 'assets/img/hilariography_logo.png' ? 
            `<img src="${product.imagePath}" alt="${product.prodName}" class="product-card-image">` :
            `<div class="no-image-placeholder">
                <div class="no-image-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <div class="no-image-text">No Image</div>
            </div>`;

        const buttonHTML = this.isAdmin ? 
            `<button class="view-details-btn" onclick="dashboard.openProductModal('${product.prodID}')">View Details</button>` :
            `<button class="buy-btn" onclick="dashboard.openProductModal('${product.prodID}')">Buy Now</button>`;

        const attributesHTML = `
            ${product.color && product.color !== 'N/A' ? `<span class="attribute-tag">${product.color}</span>` : ''}
            ${product.size && product.size !== 'N/A' ? `<span class="attribute-tag">${product.size}</span>` : ''}
        `;

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
                            <span class="product-price">${product.price}</span>
                            <span class="product-quantity">Qty: ${product.quantity}</span>
                        </div>
                    </div>
                    <div class="product-attributes-row">
                        <div class="product-attributes">
                            ${attributesHTML}
                        </div>
                        ${buttonHTML}
                    </div>
                </div>
            </div>
        `;
    }
    
    async openProductModal(productId) {
        const modal = document.getElementById('productModal');
        
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
        
        this.showModalState('loading');
        
        await this.updateModalProduct(productId);
    }
    
    showModalState(state) {
        const loadingState = document.getElementById('modalLoadingState');
        const errorState = document.getElementById('modalErrorState');
        const deletedState = document.getElementById('modalDeletedState');
        const contentState = document.getElementById('modalProductContent');
        
        // Hide all states first
        loadingState.style.display = 'none';
        errorState.style.display = 'none';
        deletedState.style.display = 'none';
        contentState.style.display = 'none';
        contentState.style.opacity = '0';
        contentState.style.visibility = 'hidden';
        
        switch (state) {
            case 'loading':
                loadingState.style.display = 'flex';
                break;
            case 'error':
                errorState.style.display = 'flex';
                break;
            case 'deleted':
                deletedState.style.display = 'flex';
                break;
            case 'content':
                contentState.style.display = 'flex';
                contentState.style.opacity = '1';
                contentState.style.visibility = 'visible';
                break;
        }
    }
    
    async updateModalProduct(productId) {
        try {
            const response = await fetch(`api/get_single_product.php?id=${productId}`);
            const data = await response.json();
            
            if (data.success && data.product && data.product.prodName && data.product.prodID) {
                const product = data.product;
                this.currentProduct = product;
                this.currentProductId = productId;
                
                // Update modal content
                document.getElementById('modalProductName').textContent = this.isAdmin ? 'Product Details' : 'Purchase Product';
                document.getElementById('modalProductDescription').textContent = product.prodDescription;
                document.getElementById('modalProductPrice').textContent = product.price;
                document.getElementById('modalProductQuantity').textContent = product.quantity;
                document.getElementById('modalProductColor').textContent = product.color || 'N/A';
                document.getElementById('modalProductSize').textContent = product.size || 'N/A';
                
                // Update image
                const modalImage = document.getElementById('modalProductImage');
                const modalPlaceholder = document.getElementById('modalNoImagePlaceholder');
                
                if (product.imagePath && product.imagePath !== 'assets/img/hilariography_logo.png' && product.imageExists) {
                    modalImage.src = product.imagePath;
                    modalImage.style.display = 'block';
                    modalPlaceholder.style.display = 'none';
                } else {
                    modalImage.style.display = 'none';
                    modalPlaceholder.style.display = 'flex';
                }
                
                // Reset quantity and update total (only for users)
                if (!this.isAdmin) {
                    const quantityInput = document.getElementById('purchaseQuantity');
                    if (quantityInput) {
                        quantityInput.value = 1;
                        quantityInput.max = product.quantity;
                        this.updateTotal();
                    }
                }
                
                this.showModalState('content');
            } else {
                // Check if this is a 404 or product not found error
                if (data.error && (data.error.includes('not found') || data.error.includes('404'))) {
                    this.showModalState('deleted');
                } else {
                    const errorMessageElement = document.getElementById('modalErrorMessage');
                    if (data.message) {
                        errorMessageElement.textContent = data.message;
                    } else {
                        errorMessageElement.textContent = 'Sorry, this product is currently unavailable. It may have been removed or is temporarily out of stock.';
                    }
                    this.showModalState('error');
                }
            }
        } catch (error) {
            console.error('Error fetching product:', error);
            
            // Check if it's a 404 error
            if (error.message && error.message.includes('404')) {
                this.showModalState('deleted');
            } else {
                const errorMessageElement = document.getElementById('modalErrorMessage');
                errorMessageElement.textContent = 'Unable to load product details. Please check your connection and try again.';
                this.showModalState('error');
            }
        }
    }
    
    // User-specific functions (only for non-admin users)
    updateTotal() {
        if (!this.currentProduct || this.isAdmin) return;
        
        const quantityInput = document.getElementById('purchaseQuantity');
        const totalElement = document.getElementById('modalTotalAmount');
        
        if (quantityInput && totalElement) {
            const quantity = parseInt(quantityInput.value) || 1;
            const price = parseFloat(this.currentProduct.price.replace(/[₱,]/g, ''));
            const total = price * quantity;
            totalElement.textContent = '₱' + total.toFixed(2);
        }
    }
    
    increaseQuantity() {
        if (this.isAdmin) return;
        
        const quantityInput = document.getElementById('purchaseQuantity');
        if (quantityInput && this.currentProduct) {
            const currentValue = parseInt(quantityInput.value) || 1;
            const maxQuantity = this.currentProduct.quantity;
            const newValue = Math.min(currentValue + 1, maxQuantity);
            quantityInput.value = newValue;
            this.updateTotal();
        }
    }
    
    decreaseQuantity() {
        if (this.isAdmin) return;
        
        const quantityInput = document.getElementById('purchaseQuantity');
        if (quantityInput) {
            const currentValue = parseInt(quantityInput.value) || 1;
            const newValue = Math.max(currentValue - 1, 1);
            quantityInput.value = newValue;
            this.updateTotal();
        }
    }
    
    async confirmPurchase() {
        if (this.isAdmin || !this.currentProduct) {
            alert('No product selected for purchase.');
            return;
        }
        
        const quantityInput = document.getElementById('purchaseQuantity');
        const quantity = parseInt(quantityInput.value) || 1;
        
        // Validate quantity
        if (quantity > this.currentProduct.quantity) {
            alert('Order quantity exceeds available stock.');
            return;
        }
        
        if (quantity < 1) {
            alert('Please select a valid quantity.');
            return;
        }
        
        const price = parseFloat(this.currentProduct.price.replace(/[₱,]/g, ''));
        const total = price * quantity;
        
        // Show confirmation dialog
        const confirmed = confirm(
            `Confirm Purchase:\n\n` +
            `Product: ${this.currentProduct.prodName}\n` +
            `Quantity: ${quantity}\n` +
            `Total: ₱${total.toFixed(2)}\n\n` +
            `Do you want to proceed with this purchase?`
        );
        
        if (confirmed) {
            // Here you would typically send the order to your backend
            // For now, we'll just show a success message
            alert('Purchase confirmed! Your order has been placed successfully.');
            this.closeProductModal();
        }
    }
    
    applyFilters() {
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        const searchTerm = searchInput.value.toLowerCase();
        const minPrice = parseFloat(minPriceInput.value) || 0;
        const maxPrice = parseFloat(maxPriceInput.value) || Infinity;

        const productCards = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const searchData = card.getAttribute('data-search');
            const priceElement = card.querySelector('.product-price');
            const priceText = priceElement ? priceElement.textContent.replace(/[₱,]/g, '') : '0';
            const price = parseFloat(priceText);

            const matchesSearch = searchData.includes(searchTerm);
            const matchesPrice = price >= minPrice && price <= maxPrice;

            if (matchesSearch && matchesPrice) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show no products found interface if no products match filters
        const productsGrid = document.getElementById('productsGrid');
        const hasActiveFilters = searchTerm || minPrice > 0 || maxPrice !== Infinity;
        
        if (visibleCount === 0 && hasActiveFilters) {
            productsGrid.innerHTML = this.createNoProductsHTML();
        }
    }
    
    async clearFilters() {
        // Show loading state on both clear buttons
        this.showClearButtonsLoading(true);
        
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        // Clear the inputs
        searchInput.value = '';
        minPriceInput.value = '';
        maxPriceInput.value = '';
        
        // Small delay to show loading state
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Refresh products to show all products
        await this.refreshProducts();
        
        // Hide loading state
        this.showClearButtonsLoading(false);
    }
    
    showClearButtonsLoading(show) {
        const navbarClearBtn = document.querySelector('.clear-filter-btn');
        const noProductsClearBtn = document.querySelector('.clear-filters-btn');
        
        if (navbarClearBtn) {
            if (show) {
                navbarClearBtn.innerHTML = `
                    <div class="loading-spinner-small"></div>
                    <span>Clearing...</span>
                `;
                navbarClearBtn.disabled = true;
            } else {
                navbarClearBtn.innerHTML = 'Clear';
                navbarClearBtn.disabled = false;
            }
        }
        
        if (noProductsClearBtn) {
            if (show) {
                noProductsClearBtn.innerHTML = `
                    <div class="loading-spinner-small"></div>
                    <span>Clearing...</span>
                `;
                noProductsClearBtn.disabled = true;
            } else {
                noProductsClearBtn.innerHTML = 'Clear All Filters';
                noProductsClearBtn.disabled = false;
            }
        }
    }
    
    closeProductModal() {
        const modal = document.getElementById('productModal');
        modal.classList.remove('show');
        
        this.currentProduct = null;
        this.currentProductId = null;
        
        setTimeout(() => modal.style.display = 'none', 150);
    }
}

// Global functions for backward compatibility
function openProductModal(productId) {
    dashboard.openProductModal(productId);
}

function closeProductModal() {
    dashboard.closeProductModal();
}

function applyFilters() {
    dashboard.applyFilters();
}

async function clearFilters() {
    await dashboard.clearFilters();
}

// User-specific global functions (only for non-admin users)
function updateTotal() {
    if (window.dashboard && !window.dashboard.isAdmin) {
        dashboard.updateTotal();
    }
}

function increaseQuantity() {
    if (window.dashboard && !window.dashboard.isAdmin) {
        dashboard.increaseQuantity();
    }
}

function decreaseQuantity() {
    if (window.dashboard && !window.dashboard.isAdmin) {
        dashboard.decreaseQuantity();
    }
}

function confirmPurchase() {
    if (window.dashboard && !window.dashboard.isAdmin) {
        dashboard.confirmPurchase();
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
});
<!-- Product Modal for Both Admin and User -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalProductName">
                <?php echo $isAdmin ? 'Product Details' : 'Purchase Product'; ?>
            </h2>
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
            <div id="modalErrorState" class="modal-error" style="display: none;">
                <div class="error-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="error-title">Product Unavailable</div>
                <div class="error-message" id="modalErrorMessage">Sorry, this product is currently unavailable.</div>
                <div class="error-actions">
                    <button class="close-btn" onclick="closeProductModal()">Close</button>
                </div>
            </div>
            
            <!-- Product Deleted State -->
            <div id="modalDeletedState" class="modal-deleted" style="display: none;">
                <div class="deleted-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="deleted-title">Product No Longer Available</div>
                <div class="deleted-message">This product has been removed from the catalog and is no longer available for purchase.</div>
                <div class="deleted-actions">
                    <button class="close-btn" onclick="closeProductModal()">Close</button>
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
                            <span class="info-label"><?php echo $isAdmin ? 'Quantity:' : 'Available:'; ?></span>
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
                    
                    <!-- Purchase Section (Only for Users) -->
                    <?php if (!$isAdmin): ?>
                    <div class="purchase-section">
                        <div class="quantity-section">
                            <label for="purchaseQuantity">Quantity:</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                                <input type="number" id="purchaseQuantity" value="1" min="1" max="100" onchange="updateTotal()">
                                <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                        
                        <div class="total-section">
                            <span class="total-label">Total:</span>
                            <span class="total-amount" id="modalTotalAmount"></span>
                        </div>
                        
                        <div class="purchase-actions">
                            <button class="confirm-btn" onclick="confirmPurchase()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                Confirm Purchase
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 
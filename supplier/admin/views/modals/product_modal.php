<?php
require_once __DIR__ . '/../../utils/helpers.php';
?>
<!-- ADD PRODUCT MODAL -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <h3>Add Product</h3>
        <form action="products/add_product.php" method="post" enctype="multipart/form-data" id="addProductForm">
            <div class="form-group">
                <label>Product Name <span style="color: red;">*</span></label>
                <input type="text" name="productName" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="productImage" id="addProductImage" accept="image/*">
                <div id="addImagePreview" style="margin-top: 10px;"></div>
            </div>
            <div class="form-group">
                <label>Description <span style="color: red;">*</span></label>
                <textarea name="productDescription" required></textarea>
            </div>
            <div class="form-group">
                <label>Quantity <span style="color: red;">*</span></label>
                <input type="number" name="productQty" min="1" required>
            </div>
            <div class="form-group">
                <label>Price <span style="color: red;">*</span></label>
                <input type="number" step="0.01" name="productPrice" min="<?php echo MIN_PRODUCT_PRICE; ?>" required>
            </div>
            <div class="form-group">
                <label>Color (optional)</label>
                <select name="productColor" id="addProductColor" onchange="toggleCustomColorInput('add')">
                    <?php echo Helpers::getColorOptions(); ?>
                </select>
                <input type="text" name="productColorCustom" id="addProductColorCustom" placeholder="Enter custom color" style="display: none; margin-top: 8px;">
            </div>
            <div class="form-group">
                <label>Size (optional)</label>
                <select name="productSize" id="addProductSize" onchange="toggleCustomSizeInput('add')">
                    <?php echo Helpers::getSizeOptions(); ?>
                </select>
                <input type="text" name="productSizeCustom" id="addProductSizeCustom" placeholder="Enter custom size" style="display: none; margin-top: 8px;">
            </div>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="closeModal('productModal')">Cancel</button>
                <button type="submit" class="save-btn">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <div class="modal-actions">
                <span class="close" onclick="closeModal('editProductModal')">&times;</span>
            </div>
        </div>
        <div class="modal-body">
            <!-- Loading State -->
            <div id="editModalLoadingState" class="modal-loading" style="display: none;">
                <div class="loading-spinner"></div>
                <div class="loading-text">Loading product details...</div>
            </div>
            
            <!-- Error State -->
            <div id="editModalErrorState" class="modal-error" style="display: none;">
                <div class="error-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="error-title">Product Unavailable</div>
                <div class="error-message" id="editModalErrorMessage">Sorry, this product is currently unavailable.</div>
                <div class="error-actions">
                    <button class="close-btn" onclick="closeModal('editProductModal')">Close</button>
                </div>
            </div>
            
            <!-- Product Deleted State -->
            <div id="editModalDeletedState" class="modal-deleted" style="display: none;">
                <div class="deleted-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="deleted-title">Product No Longer Available</div>
                <div class="deleted-message">This product has been removed from the catalog and is no longer available for editing.</div>
                <div class="deleted-actions">
                    <button class="close-btn" onclick="closeModal('editProductModal')">Close</button>
                </div>
            </div>
            
            <!-- Edit Product Content -->
            <div id="editProductContent" class="modal-product-content">
                <form id="editProductForm" method="post" action="products/edit_product.php" enctype="multipart/form-data">
                    <input type="hidden" name="prodID" id="editProductID">
                    <input type="hidden" name="removeImage" id="removeImageFlag" value="0">
                    <div class="form-group">
                        <label for="editProductName">Product Name <span style="color: red;">*</span></label>
                        <input type="text" name="productName" id="editProductName" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductImage">Product Image</label>
                        <input type="file" name="productImage" id="editProductImage" accept="image/*">
                        <div id="editCurrentImagePreview" style="margin-top: 10px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="editProductDescription">Description <span style="color: red;">*</span></label>
                        <textarea name="productDescription" id="editProductDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editProductQty">Quantity <span style="color: red;">*</span></label>
                        <input type="number" name="productQty" id="editProductQty" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductPrice">Price (₱) <span style="color: red;">*</span></label>
                        <input type="number" step="0.01" min="<?php echo MIN_PRODUCT_PRICE; ?>" name="productPrice" id="editProductPrice" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductColor">Color (optional)</label>
                        <select name="productColor" id="editProductColor" onchange="toggleCustomColorInput('edit')">
                            <?php echo Helpers::getColorOptions(); ?>
                        </select>
                        <input type="text" name="productColorCustom" id="editProductColorCustom" placeholder="Enter custom color" style="display: none; margin-top: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="editProductSize">Size (optional)</label>
                        <select name="productSize" id="editProductSize" onchange="toggleCustomSizeInput('edit')">
                            <?php echo Helpers::getSizeOptions(); ?>
                        </select>
                        <input type="text" name="productSizeCustom" id="editProductSizeCustom" placeholder="Enter custom size" style="display: none; margin-top: 8px;">
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="cancel-btn" onclick="closeModal('editProductModal')">Cancel</button>
                        <button type="submit" class="save-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
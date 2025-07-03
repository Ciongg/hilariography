<?php
header("Location: auth/login.php");
exit;
?>


<!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <h3>Edit Product</h3>
            <form id="editProductForm" method="post" action="products/edit_product.php" enctype="multipart/form-data">
                <input type="hidden" name="prodID" id="editProductID">
                <input type="hidden" name="removeImage" id="removeImageFlag" value="0">
                <div class="form-group">
                    <label for="editProductName">Name</label>
                    <input type="text" name="productName" id="editProductName" required>
                </div>
                <div class="form-group">
                    <label for="editProductImage">Product Image</label>
                    <input type="file" name="productImage" id="editProductImage" accept="image/*">
                    <div id="editCurrentImagePreview" style="margin-top: 10px;"></div>
                </div>
                <div class="form-group">
                    <label for="editProductDescription">Description</label>
                    <textarea name="productDescription" id="editProductDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editProductQty">Quantity</label>
                    <input type="number" name="productQty" id="editProductQty" min="0" required>
                </div>
                <div class="form-group">
                    <label for="editProductPrice">Price (₱)</label>
                    <input type="number" step="0.01" min="5" name="productPrice" id="editProductPrice" min="0" required>
                </div>
                <div class="form-group">
                    <label for="editProductColor">Color (optional)</label>
                    <select name="productColor" id="editProductColor" onchange="toggleCustomColorInput('edit')">
                        <option value="">Select Color</option>
                        <option value="Red">Red</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Orange">Orange</option>
                        <option value="Purple">Purple</option>
                        <option value="Pink">Pink</option>
                        <option value="Brown">Brown</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                        <option value="Gray">Gray</option>
                        <option value="Navy">Navy</option>
                        <option value="Maroon">Maroon</option>
                        <option value="Beige">Beige</option>
                        <option value="Turquoise">Turquoise</option>
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="text" name="productColorCustom" id="editProductColorCustom" placeholder="Enter custom color" style="display: none; margin-top: 8px;">
                </div>
                <div class="form-group">
                    <label for="editProductSize">Size (optional)</label>
                    <select name="productSize" id="editProductSize" onchange="toggleCustomSizeInput('edit')">
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="2XL">2XL</option>
                        <option value="3XL">3XL</option>
                        <option value="other">Other</option>
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
                    <input type="number" step="0.01" name="productPrice" min="5" required>
                </div>
                <div class="form-group">
                    <label>Color (optional)</label>
                    <select name="productColor" id="addProductColor" onchange="toggleCustomColorInput('add')">
                        <option value="">Select Color</option>
                        <option value="Red">Red</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Orange">Orange</option>
                        <option value="Purple">Purple</option>
                        <option value="Pink">Pink</option>
                        <option value="Brown">Brown</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                        <option value="Gray">Gray</option>
                        <option value="Navy">Navy</option>
                        <option value="Maroon">Maroon</option>
                        <option value="Beige">Beige</option>
                        <option value="Turquoise">Turquoise</option>
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="text" name="productColorCustom" id="addProductColorCustom" placeholder="Enter custom color" style="display: none; margin-top: 8px;">
                </div>
                <div class="form-group">
                    <label>Size (optional)</label>
                    <select name="productSize" id="addProductSize" onchange="toggleCustomSizeInput('add')">
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="2XL">2XL</option>
                        <option value="3XL">3XL</option>
                        <option value="other">Other</option>
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

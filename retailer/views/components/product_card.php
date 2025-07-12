<div class="product-card" data-search="<?php echo strtolower($product['prodName']); ?>" data-product-id="<?php echo $product['prodID']; ?>">
    <div class="product-image-container">
        <?php if ($product['imageExists']): ?>
            <img src="<?php echo $product['imagePath']; ?>" alt="<?php echo $product['prodName']; ?>" class="product-card-image">
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
            <h3 class="product-name"><?php echo htmlspecialchars($product['prodName']); ?></h3>
            <p class="product-description"><?php echo htmlspecialchars($product['prodDescription']); ?></p>
            <div class="product-details">
                <span class="product-price"><?php echo $product['price']; ?></span>
                <span class="product-quantity">Qty: <?php echo $product['quantity']; ?></span>
            </div>
        </div>
        <div class="product-attributes-row">
            <div class="product-attributes">
                <?php if ($product['color'] !== 'N/A'): ?>
                    <span class="attribute-tag"><?php echo htmlspecialchars($product['color']); ?></span>
                <?php endif; ?>
                <?php if ($product['size'] !== 'N/A'): ?>
                    <span class="attribute-tag"><?php echo htmlspecialchars($product['size']); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($isAdmin): ?>
                <button class="view-details-btn" onclick="openProductModal('<?php echo $product['prodID']; ?>')">
                    View Details
                </button>
            <?php else: ?>
                <button class="buy-btn" onclick="openProductModal('<?php echo $product['prodID']; ?>')">
                    Buy Now
                </button>
            <?php endif; ?>
        </div>
    </div>
</div> 
<?php
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../utils/helpers.php';

$productModel = new Product();
$products = $productModel->getAll();
?>
<div id="productsTable" class="table-container hidden">
    <div class="table-header">
        <h3>Product Management</h3>
        <div class="filters-container">
            <div class="search-container">
                <div class="search-input-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" class="search-input" placeholder="Search products..." id="productSearchInput">
                </div>
            </div>
            <div class="price-filter-container">
                <div class="price-inputs">
                    <input type="number" class="price-input" placeholder="Min ₱" id="minPrice" min="0" step="0.01">
                    <span class="price-separator">-</span>
                    <input type="number" class="price-input" placeholder="Max ₱" id="maxPrice" min="0" step="0.01">
                </div>
                <button class="filter-btn" onclick="applyProductFilters()">Filter</button>
                <button class="clear-filter-btn" onclick="clearProductFilters()">Clear</button>
            </div>
            <button class="modern-add-btn" onclick="openModal('productModal')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                Add Product
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th class="actions-column">Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><span class='table-id'><?php echo $product['prodID']; ?></span></td>
                    <td><span class='table-name'><?php echo htmlspecialchars($product['prodName']); ?></span></td>
                    <td>
                        <?php if (!empty($product['image_path'])): ?>
                        <div class='image-container'>
                            <img src='../../uploads/<?php echo htmlspecialchars($product['image_path']); ?>' alt='Product Image' class='product-image'>
                        </div>
                        <?php else: ?>
                        <div class='no-image'>
                            <svg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                                <rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect>
                                <circle cx='8.5' cy='8.5' r='1.5'></circle>
                                <polyline points='21,15 16,10 5,21'></polyline>
                            </svg>
                            <span>No image</span>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><span class='table-description'><?php echo htmlspecialchars(Helpers::truncateText($product['prodDescription'])); ?></span></td>
                    <td><span class='table-qty'><?php echo $product['quantity']; ?></span></td>
                    <td><span class='table-price'><?php echo Helpers::formatPrice($product['price']); ?></span></td>
                    <td><span class='table-color'><?php echo htmlspecialchars($product['color']); ?></span></td>
                    <td><span class='table-size'><?php echo htmlspecialchars($product['size']); ?></span></td>
                    <td><span class='table-created'><?php echo $product['created_at']; ?></span></td>
                    <td><span class='table-created'><?php echo $product['updated_at']; ?></span></td>
                    <td class='actions-cell'>
                        <div class='action-buttons'>
                            <button class='btn btn-edit' 
                                data-product-id='<?php echo $product['prodID']; ?>'
                                data-product-name='<?php echo htmlspecialchars($product['prodName'], ENT_QUOTES); ?>'
                                data-product-description='<?php echo htmlspecialchars($product['prodDescription'], ENT_QUOTES); ?>'
                                data-product-quantity='<?php echo $product['quantity']; ?>'
                                data-product-price='<?php echo $product['price']; ?>'
                                data-product-color='<?php echo htmlspecialchars($product['color'] === 'N/A' ? '' : $product['color'], ENT_QUOTES); ?>'
                                data-product-size='<?php echo htmlspecialchars($product['size'] === 'N/A' ? '' : $product['size'], ENT_QUOTES); ?>'
                                data-product-image='<?php echo htmlspecialchars($product['image_path'] ?? '', ENT_QUOTES); ?>'
                                onclick='dashboard.openEditProductModalFromData(this)'>
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                </svg>
                                Edit
                            </button>
                            <a href='products/delete_product.php?id=<?php echo $product['prodID']; ?>' class='btn btn-delete' onclick="return confirm('Delete this product?')">
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                </svg>
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 
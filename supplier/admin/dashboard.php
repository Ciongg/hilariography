<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    header("Location: ../auth/login.php");
    exit;
}
include("../../includes/db.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Supplier Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>

<body>
    <div id="dashboard" class="dashboard">
        <div class="container">
            <div class="header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="logo">
                        <img src="../assets/img/fabsuplogo.png" alt="FabricSup Logo">
                    </div>
                    <h1 style="margin: 0;">Supply Dashboard</h1>
                </div>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                    </div>
                    <span id="currentUser"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="../auth/logout.php" class="logout-btn" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" onclick="showTab('users')">Users</button>
                <button class="tab" onclick="showTab('products')">Products</button>
            </div>

            <!-- USERS TABLE -->
            <div id="usersTable" class="table-container">
                <div class="table-header">
                    <h3>User Management</h3>
                    <div class="filters-container">
                        <div class="search-container">
                            <div class="search-input-wrapper">
                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <input type="text" class="search-input" placeholder="Search users..." id="userSearchInput">
                            </div>
                        </div>
                        <div class="role-filter-container">
                            <select class="role-filter" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="employee">Employee</option>
                            </select>
                            <button class="filter-btn" onclick="applyUserFilters()">Filter</button>
                            <button class="clear-filter-btn" onclick="clearUserFilters()">Clear</button>
                        </div>
                        <button class="modern-add-btn" onclick="openModal('userModal')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                            Add User
                        </button>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php
                            $result = $conn->query("SELECT * FROM dbusers ORDER BY userID ASC");
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                 
                                    $createdDate = '';
                                    if (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00') {
                                   
                                        $cleanDate = preg_replace('/\.\d+/', '', $row['created_at']);
                                        $timestamp = strtotime($cleanDate);
                                        if ($timestamp !== false) {
                                            $date = date('F j, Y', $timestamp);
                                            $time = date('g:ia', $timestamp);
                                            $createdDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                                        } else {
                                            $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
                                        }
                                    } else {
                                        $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
                                    }
                                    
                       
                                    $updatedDate = '';
                                    if (!empty($row['updated_at']) && $row['updated_at'] !== '0000-00-00 00:00:00') {
                                    
                                        $cleanDate = preg_replace('/\.\d+/', '', $row['updated_at']);
                                        $timestamp = strtotime($cleanDate);
                                        if ($timestamp !== false) {
                                            $date = date('F j, Y', $timestamp);
                                            $time = date('g:ia', $timestamp);
                                            $updatedDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                                        } else {
                                            $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
                                        }
                                    } else {
                                        $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
                                    }

                                    echo "<tr>
                            <td><span class='table-id'>{$row['userID']}</span></td>
                            <td><span class='table-username'>" . htmlspecialchars($row['userName']) . "</span></td>
                            <td><span class='table-role role-" . strtolower($row['userRole']) . "'>" . htmlspecialchars($row['userRole']) . "</span></td>
                            <td><span class='table-created'>" . $createdDate . "</span></td>
                            <td><span class='table-created'>" . $updatedDate . "</span></td>
                            <td class='actions-cell'>
                                <div class='action-buttons'>
                                    <button class='btn btn-edit' onclick='openEditUserModal({$row['userID']}, " . json_encode($row['userName']) . ", " . json_encode($row['userRole']) . ")'>
                                        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                            <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                        </svg>
                                        Edit
                                    </button>";
                                    
                                    // Show delete button only if logged-in user is Admin and target user is Employee
                                    if (isset($_SESSION["userRole"]) && $_SESSION["userRole"] === "Admin" && $row['userRole'] === "Employee") {
                                        echo "<a href='users/delete_user.php?id={$row['userID']}' class='btn btn-delete' onclick=\"return confirm('Delete this employee?')\">
                                            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                                <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                            </svg>
                                            Delete
                                        </a>";
                                    } else {
                                        // Add invisible placeholder to maintain grid layout
                                        echo "<span class='btn-placeholder'></span>";
                                    }
                                    
                                    echo "</div>
                            </td>
                        </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PRODUCTS TABLE -->
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
                            <?php
                            $result = $conn->query("SELECT * FROM dbproduct ORDER BY prodID ASC");
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                    <td><span class='table-id'>{$row['prodID']}</span></td>
                    <td><span class='table-name'>" . htmlspecialchars($row['prodName']) . "</span></td>";
                    
                    // Display image from shared uploads folder using image_path
                    if (!empty($row['image_path'])) {
                        echo "<td><div class='image-container'><img src='../../uploads/" . htmlspecialchars($row['image_path']) . "' alt='Product Image' class='product-image'></div></td>";
                    } else {
                        echo "<td><div class='no-image'><svg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21,15 16,10 5,21'></polyline></svg><span>No image</span></div></td>";
                    }
                    
          
                    $createdDate = '';
                    if (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00') {
                
                        $cleanDate = preg_replace('/\.\d+/', '', $row['created_at']);
                        $timestamp = strtotime($cleanDate);
                        if ($timestamp !== false) {
                            $date = date('F j, Y', $timestamp);
                            $time = date('g:ia', $timestamp);
                            $createdDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                        } else {
                            $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
                        }
                    } else {
                        $createdDate = "<span style='color: #9ca3af; font-style: italic;'>Not available</span>";
                    }
      
                    $updatedDate = '';
                    if (!empty($row['updated_at']) && $row['updated_at'] !== '0000-00-00 00:00:00') {
           
                        $cleanDate = preg_replace('/\.\d+/', '', $row['updated_at']);
                        $timestamp = strtotime($cleanDate);
                        if ($timestamp !== false) {
                            $date = date('F j, Y', $timestamp);
                            $time = date('g:ia', $timestamp);
                            $updatedDate = $date . " <span style='color: #6b7280; font-style: italic;'>- " . $time . "</span>";
                        } else {
                            $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
                        }
                    } else {
                        $updatedDate = "<span style='color: #9ca3af; font-style: italic;'>Not updated</span>";
                    }
                    
                    echo "<td><span class='table-description'>" . htmlspecialchars(strlen($row['prodDescription']) > 50 ? substr($row['prodDescription'], 0, 50) . '...' : $row['prodDescription']) . "</span></td>
                    <td><span class='table-qty'>{$row['quantity']}</span></td>
                    <td><span class='table-price'>₱" . number_format($row['price'], 2) . "</span></td>
                    <td><span class='table-color'>" . htmlspecialchars($row['color'] ?: 'N/A') . "</span></td>
                    <td><span class='table-size'>" . htmlspecialchars($row['size'] ?: 'N/A') . "</span></td>
                    <td><span class='table-created'>" . $createdDate . "</span></td>
                    <td><span class='table-created'>" . $updatedDate . "</span></td>
                    <td class='actions-cell'>
                        <div class='action-buttons'>
                            <button class='btn btn-edit' 
                                onclick='openEditProductModal(
                                {$row['prodID']},
                                " . htmlspecialchars(json_encode($row['prodName']), ENT_QUOTES, 'UTF-8') . ",
                                " . htmlspecialchars(json_encode($row['prodDescription']), ENT_QUOTES, 'UTF-8') . ",
                                {$row['quantity']},
                                {$row['price']},
                                " . htmlspecialchars(json_encode($row['color']), ENT_QUOTES, 'UTF-8') . ",
                                " . htmlspecialchars(json_encode($row['size']), ENT_QUOTES, 'UTF-8') . ",
                                " . htmlspecialchars(json_encode($row['image_path']), ENT_QUOTES, 'UTF-8') . "
                                )'>
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                </svg>
                                Edit
                            </button>
                            <a href='products/delete_product.php?id={$row['prodID']}' class='btn btn-delete' onclick=\"return confirm('Delete this product?')\">
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                </svg>
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD USER MODAL -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h3>Add User</h3>
            <form action="users/add_user.php" method="post" id="addUserForm">
                <div class="form-group">
                    <label>Username <span style="color: red;">*</span></label>
                    <input type="text" name="userName" id="addUserName" required>
                </div>
                <div class="form-group">
                    <label>Password <span style="color: red;">*</span></label>
                    <input type="password" name="userPassword" id="addUserPassword" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password <span style="color: red;">*</span></label>
                    <input type="password" name="confirmPassword" id="addConfirmPassword" required>
                    <div id="passwordError" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Passwords do not match</div>
                </div>
                <div class="form-group">
                    <label>Role <span style="color: red;">*</span></label>
                    <select name="userRole" required>
                        <option value="">Select Role</option>
                        <option>Admin</option>
                        <option>Employee</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeModal('userModal')">Cancel</button>
                    <button type="submit" class="save-btn" id="addUserSubmitBtn">Save</button>
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

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <h3 id="editUserModalTitle">Edit User</h3>
            <form id="editUserForm" method="post" action="users/edit_user.php">
                <input type="hidden" name="userID" id="editUserID">
                <div class="form-group">
                    <label for="editUserName">Name</label>
                    <input type="text" id="editUserName" name="userName" required>
                </div>
                <div class="form-group">
                    <label for="editUserRole">Role</label>
                    <select id="editUserRole" name="userRole" required>
                        <option value="Admin">Admin</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeModal('editUserModal')">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <h3>Edit Product</h3>
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

    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase() === tab);
            });
            document.getElementById('usersTable').classList.toggle('hidden', tab !== 'users');
            document.getElementById('productsTable').classList.toggle('hidden', tab !== 'products');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
      
            modal.offsetHeight;
            modal.classList.add('show');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            
            // Stop modal updates when closing
            if (modalId === 'editUserModal') {
                stopModalUserUpdates();
            } else if (modalId === 'editProductModal') {
                stopModalProductUpdates();
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                // Reset custom inputs
                if (modalId === 'productModal') {
                    document.getElementById('addProductColor').value = '';
                    document.getElementById('addProductColorCustom').style.display = 'none';
                    document.getElementById('addProductColorCustom').value = '';
                    document.getElementById('addProductSize').value = '';
                    document.getElementById('addProductSizeCustom').style.display = 'none';
                    document.getElementById('addProductSizeCustom').value = '';
                }
            }, 150); 
        }

        // Override existing functions to include update triggers
        const originalShowTab = showTab;
        showTab = function(tab) {
            originalShowTab(tab);
            if (tab === 'products') {
                setTimeout(() => {
                    triggerRetailerUpdate();
                }, 300);
            }
        };

        // Filter functionality
        function applyUserFilters() {
            const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
            const rows = document.getElementById('usersTableBody').getElementsByTagName('tr');

            for (let row of rows) {
                const username = row.cells[1]?.textContent.toLowerCase() || "";
                const role = row.cells[2]?.textContent.toLowerCase() || "";
                
                const matchesSearch = username.includes(searchTerm);
                const matchesRole = !roleFilter || role.includes(roleFilter);

                row.style.display = matchesSearch && matchesRole ? "" : "none";
            }
        }

        function clearUserFilters() {
            document.getElementById('userSearchInput').value = '';
            document.getElementById('roleFilter').value = '';
            applyUserFilters();
        }

        function applyProductFilters() {
            const searchTerm = document.getElementById('productSearchInput').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
            const rows = document.getElementById('productsTableBody').getElementsByTagName('tr');

            for (let row of rows) {
                const productName = row.cells[1]?.textContent.toLowerCase() || "";
                const priceText = row.cells[5]?.textContent.replace('₱', '').replace(',', '') || "0";
                const price = parseFloat(priceText);
                
                const matchesSearch = productName.includes(searchTerm);
                const matchesPrice = price >= minPrice && price <= maxPrice;

                row.style.display = matchesSearch && matchesPrice ? "" : "none";
            }
        }

        function clearProductFilters() {
            document.getElementById('productSearchInput').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            applyProductFilters();
        }

        document.addEventListener("DOMContentLoaded", () => {

             // User search instant functionality
            const userSearchInput = document.getElementById('userSearchInput');
            const roleFilter = document.getElementById('roleFilter');
            
            if (userSearchInput) {
                userSearchInput.addEventListener('input', applyUserFilters);
            }
            if (roleFilter) {
                roleFilter.addEventListener('change', applyUserFilters);
            }

            // Product search instant functionality  
            const productSearchInput = document.getElementById('productSearchInput');
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            
            if (productSearchInput) {
                productSearchInput.addEventListener('input', applyProductFilters);
            }
            if (minPriceInput) {
                minPriceInput.addEventListener('input', applyProductFilters);
            }
            if (maxPriceInput) {
                maxPriceInput.addEventListener('input', applyProductFilters);
            }
            
            const params = new URLSearchParams(window.location.search);
            const tab = params.get("tab");
            if (tab === "products") showTab("products");
            else showTab("users");
        });

        // Enhanced click-outside functionality to also stop modal updates
        document.querySelectorAll(".modal").forEach(modal => {
            modal.addEventListener("click", e => {
                if (e.target === modal) {
                    // Stop modal updates when closing via click-outside
                    if (modal.id === 'editUserModal') {
                        stopModalUserUpdates();
                    } else if (modal.id === 'editProductModal') {
                        stopModalProductUpdates();
                    }
                    closeModal(modal.id);
                }
            });
        });

        function openEditUserModal(id, name, role) {
            document.getElementById("editUserID").value = id;
            document.getElementById("editUserName").value = name;
            document.getElementById("editUserRole").value = role;
            openModal("editUserModal");

            // Start real-time updates for this user
            startModalUserUpdates(id);
        }

        function openEditProductModal(id, name, desc, qty, price, color, size, imagePath) {
            document.getElementById("editProductID").value = id;
            document.getElementById("editProductName").value = name;
            document.getElementById("editProductDescription").value = desc;
            document.getElementById("editProductQty").value = qty;
            document.getElementById("editProductPrice").value = price;
            
            // Handle color selection
            const colorSelect = document.getElementById("editProductColor");
            const colorCustomInput = document.getElementById("editProductColorCustom");
            const colorOptions = Array.from(colorSelect.options).map(opt => opt.value);
            
            if (color && colorOptions.includes(color)) {
                colorSelect.value = color;
                colorCustomInput.style.display = "none";
                colorCustomInput.value = "";
            } else if (color) {
                colorSelect.value = "other";
                colorCustomInput.style.display = "block";
                colorCustomInput.value = color;
            } else {
                colorSelect.value = "";
                colorCustomInput.style.display = "none";
                colorCustomInput.value = "";
            }
            
            // Handle size selection
            const sizeSelect = document.getElementById("editProductSize");
            const sizeCustomInput = document.getElementById("editProductSizeCustom");
            const sizeOptions = Array.from(sizeSelect.options).map(opt => opt.value);
            
            if (size && sizeOptions.includes(size)) {
                sizeSelect.value = size;
                sizeCustomInput.style.display = "none";
                sizeCustomInput.value = "";
            } else if (size) {
                sizeSelect.value = "other";
                sizeCustomInput.style.display = "block";
                sizeCustomInput.value = size;
            } else {
                sizeSelect.value = "";
                sizeCustomInput.style.display = "none";
                sizeCustomInput.value = "";
            }
            
            document.getElementById("removeImageFlag").value = "0";
            
            // Show current image preview with remove button
            const previewDiv = document.getElementById("editCurrentImagePreview");
            if (imagePath) {
                previewDiv.innerHTML = `
                    <p style="margin: 5px 0; color: #666; font-size: 14px;">Current image:</p>
                    <img src="../../uploads/${imagePath}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd; display: block; margin-bottom: 10px;">
                    <button type="button" onclick="removeCurrentImage()" style="background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; cursor: pointer;">Remove Image</button>
                `;
            } else {
                previewDiv.innerHTML = "<p style='margin: 5px 0; color: #999; font-size: 14px;'>No current image</p>";
            }
            
            openModal("editProductModal");

            // Start real-time updates for this product
            startModalProductUpdates(id);
        }

        function removeCurrentImage() {
            document.getElementById("removeImageFlag").value = "1";
            document.getElementById("editCurrentImagePreview").innerHTML = "<p style='margin: 5px 0; color: #999; font-size: 14px;'>Image will be removed when saved</p>";
        }

        // Add toggle functions for custom inputs
        function toggleCustomColorInput(type) {
            const select = document.getElementById(type + "ProductColor");
            const customInput = document.getElementById(type + "ProductColorCustom");
            
            if (select.value === "other") {
                customInput.style.display = "block";
                customInput.focus();
            } else {
                customInput.style.display = "none";
                customInput.value = "";
            }
        }

        function toggleCustomSizeInput(type) {
            const select = document.getElementById(type + "ProductSize");
            const customInput = document.getElementById(type + "ProductSizeCustom");
            
            if (select.value === "other") {
                customInput.style.display = "block";
                customInput.focus();
            } else {
                customInput.style.display = "none";
                customInput.value = "";
            }
        }

        // Add image preview functionality for file input
        document.getElementById("editProductImage").addEventListener("change", function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById("editCurrentImagePreview");
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<p style="margin: 5px 0; color: #666; font-size: 14px;">New image preview:</p><img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd;">`;
                };
                reader.readAsDataURL(file);
            }   // Reset remove flag when new image is selected
        });         document.getElementById("removeImageFlag").value = "0";

        // Add image preview functionality for add product modal
        document.getElementById("addProductImage").addEventListener("change", function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById("addImagePreview");
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `
                        <p style="margin: 5px 0; color: #666; font-size: 14px;">Image preview:</p>
                        <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd; display: block; margin-bottom: 10px;">
                        <button type="button" onclick="removeAddImage()" style="background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; cursor: pointer;">Remove Image</button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = "";
            }
        });

        function removeAddImage() {
            document.getElementById("addProductImage").value = "";
            document.getElementById("addImagePreview").innerHTML = "";
        }

        // Password validation for Add User modal
        function validatePasswords() {
            const password = document.getElementById("addUserPassword").value;
            const confirmPassword = document.getElementById("addConfirmPassword").value;
            const errorDiv = document.getElementById("passwordError");
            const submitBtn = document.getElementById("addUserSubmitBtn");

            if (password !== confirmPassword) {
                errorDiv.style.display = "block";
                submitBtn.disabled = true;
                submitBtn.style.opacity = "0.5";
                submitBtn.style.cursor = "not-allowed";
                return false;
            } else {
                errorDiv.style.display = "none";
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                submitBtn.style.cursor = "pointer";
                return true;
            }
        }

        // Add event listeners for password validation
        document.addEventListener("DOMContentLoaded", function() {
            const passwordField = document.getElementById("addUserPassword");
            const confirmPasswordField = document.getElementById("addConfirmPassword");
            const addUserForm = document.getElementById("addUserForm");

            if (passwordField && confirmPasswordField) {
                passwordField.addEventListener("input", validatePasswords);
                confirmPasswordField.addEventListener("input", validatePasswords);
            }

            // Prevent form submission if passwords don't match
            if (addUserForm) {
                addUserForm.addEventListener("submit", function(e) {
                    if (!validatePasswords()) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });

        // Add immediate update functionality after form submissions
        document.addEventListener("DOMContentLoaded", function() {
            // Start real-time update polling
            startRealTimeUpdates();
            
            // Handle add product form success
            const addProductForm = document.getElementById("addProductForm");
            if (addProductForm) {
                addProductForm.addEventListener("submit", function(e) {
                    // Let the form submit normally, then trigger update
                    setTimeout(() => {
                        triggerRetailerUpdate();
                        // Also reload the current page's product table
                        location.reload();
                    }, 1000);
                });
            }

            // Handle edit product form success
            const editProductForm = document.getElementById("editProductForm");
            if (editProductForm) {
                editProductForm.addEventListener("submit", function(e) {
                    // Mark that a product form was just saved
                    productFormLastSaved = Date.now();
                    
                    // Let the form submit normally, then trigger update
                    setTimeout(() => {
                        triggerRetailerUpdate();
                        // Also reload the current page's product table
                        location.reload();
                    }, 1000);
                });
            }
            
            // Handle edit user form success
            const editUserForm = document.getElementById("editUserForm");
            if (editUserForm) {
                editUserForm.addEventListener("submit", function(e) {
                    // Mark that a user form was just saved
                    userFormLastSaved = Date.now();
                    
                    // Let the form submit normally, then trigger update
                    setTimeout(() => {
                        // Also reload the current page's user table
                        location.reload();
                    }, 1000);
                });
            }
            
            // Add user form submission tracking
            const addUserForm = document.getElementById("addUserForm");
            if (addUserForm) {
                addUserForm.addEventListener("submit", function(e) {
                    // Mark that a user form was just saved
                    userFormLastSaved = Date.now();
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                });
            }
        });

        // Real-time update system
        let lastUserUpdate = 0;
        let lastProductUpdate = 0;
        
        // Modal real-time updates
        let currentEditUserId = null;
        let currentEditProductId = null;
        let modalUserUpdateInterval = null;
        let modalProductUpdateInterval = null;
        let userFormLastSaved = 0;
        let productFormLastSaved = 0;
        let allowModalUpdates = true;
        
        function startModalUserUpdates(userId) {
            currentEditUserId = userId;
            allowModalUpdates = true;
            modalUserUpdateInterval = setInterval(() => {
                updateModalUser(userId);
            }, 2000);
        }
        
        function stopModalUserUpdates() {
            if (modalUserUpdateInterval) {
                clearInterval(modalUserUpdateInterval);
                modalUserUpdateInterval = null;
            }
            currentEditUserId = null;
            allowModalUpdates = true;
        }
        
        function startModalProductUpdates(productId) {
            currentEditProductId = productId;
            allowModalUpdates = true;
            modalProductUpdateInterval = setInterval(() => {
                updateModalProduct(productId);
            }, 2000);
        }
        
        function stopModalProductUpdates() {
            if (modalProductUpdateInterval) {
                clearInterval(modalProductUpdateInterval);
                modalProductUpdateInterval = null;
            }
            currentEditProductId = null;
            allowModalUpdates = true;
        }
        
        async function updateModalUser(userId) {
            // Only update if we recently detected a form submission or modal just opened
            if (!allowModalUpdates) return;
            
            try {
                const response = await fetch(`ajax/get_single_user.php?id=${userId}&t=${Date.now()}`);
                const data = await response.json();
                
                if (data.success && data.user) {
                    const user = data.user;
                    
                    // Update form fields if they exist and modal is open
                    const modal = document.getElementById('editUserModal');
                    if (modal && modal.style.display === 'flex') {
                        const nameField = document.getElementById('editUserName');
                        const roleField = document.getElementById('editUserRole');
                        
                        // Check if user recently saved (within last 5 seconds)
                        const recentlySaved = (Date.now() - userFormLastSaved) < 5000;
                        
                        // Only update if there was a recent save or the user isn't actively typing
                        if (recentlySaved || (!document.activeElement || 
                            (document.activeElement !== nameField && document.activeElement !== roleField))) {
                            
                            let hasUpdates = false;
                            
                            if (nameField && nameField.value !== user.userName) {
                                nameField.value = user.userName;
                                hasUpdates = true;
                            }
                            
                            if (roleField && roleField.value !== user.userRole) {
                                roleField.value = user.userRole;
                                hasUpdates = true;
                            }
                            
                            if (hasUpdates && recentlySaved) {
                                showModalUpdateNotification('User data updated by another admin');
                            }
                        }
                    }
                }
            } catch (error) {
                console.log('Modal user update check failed:', error);
            }
        }
        
        async function updateModalProduct(productId) {
            // Only update if we recently detected a form submission or modal just opened
            if (!allowModalUpdates) return;
            
            try {
                const response = await fetch(`ajax/get_single_product.php?id=${productId}&t=${Date.now()}`);
                const data = await response.json();
                
                if (data.success && data.product) {
                    const product = data.product;
                    
                    // Update form fields if they exist and modal is open
                    const modal = document.getElementById('editProductModal');
                    if (modal && modal.style.display === 'flex') {
                        const nameField = document.getElementById('editProductName');
                        const descField = document.getElementById('editProductDescription');
                        const qtyField = document.getElementById('editProductQty');
                        const priceField = document.getElementById('editProductPrice');
                        const colorSelect = document.getElementById('editProductColor');
                        const colorCustomInput = document.getElementById('editProductColorCustom');
                        const sizeSelect = document.getElementById('editProductSize');
                        const sizeCustomInput = document.getElementById('editProductSizeCustom');
                        
                        // Check if user recently saved (within last 5 seconds)
                        const recentlySaved = (Date.now() - productFormLastSaved) < 5000;
                        
                        // Only update if there was a recent save or the user isn't actively typing
                        const activeInputs = [nameField, descField, qtyField, priceField, colorCustomInput, sizeCustomInput];
                        const userIsTyping = activeInputs.includes(document.activeElement);
                        
                        if (recentlySaved || !userIsTyping) {
                            let hasUpdates = false;
                            
                            if (nameField && nameField.value !== product.prodName) {
                                nameField.value = product.prodName;
                                hasUpdates = true;
                            }
                            
                            if (descField && descField.value !== product.prodDescription) {
                                descField.value = product.prodDescription;
                                hasUpdates = true;
                            }
                            
                            if (qtyField && qtyField.value != product.quantity) {
                                qtyField.value = product.quantity;
                                hasUpdates = true;
                            }
                            
                            if (priceField && parseFloat(priceField.value) !== parseFloat(product.price)) {
                                priceField.value = product.price;
                                hasUpdates = true;
                            }
                            
                            // Handle color updates only if user isn't focused on color inputs
                            if (document.activeElement !== colorSelect && document.activeElement !== colorCustomInput) {
                                const colorOptions = Array.from(colorSelect.options).map(opt => opt.value);
                                if (product.color && colorOptions.includes(product.color)) {
                                    if (colorSelect.value !== product.color) {
                                        colorSelect.value = product.color;
                                        colorCustomInput.style.display = "none";
                                        colorCustomInput.value = "";
                                        hasUpdates = true;
                                    }
                                } else if (product.color) {
                                    if (colorSelect.value !== "other" || colorCustomInput.value !== product.color) {
                                        colorSelect.value = "other";
                                        colorCustomInput.style.display = "block";
                                        colorCustomInput.value = product.color;
                                        hasUpdates = true;
                                    }
                                }
                            }
                            
                            // Handle size updates only if user isn't focused on size inputs
                            if (document.activeElement !== sizeSelect && document.activeElement !== sizeCustomInput) {
                                const sizeOptions = Array.from(sizeSelect.options).map(opt => opt.value);
                                if (product.size && sizeOptions.includes(product.size)) {
                                    if (sizeSelect.value !== product.size) {
                                        sizeSelect.value = product.size;
                                        sizeCustomInput.style.display = "none";
                                        sizeCustomInput.value = "";
                                        hasUpdates = true;
                                    }
                                } else if (product.size) {
                                    if (sizeSelect.value !== "other" || sizeCustomInput.value !== product.size) {
                                        sizeSelect.value = "other";
                                        sizeCustomInput.style.display = "block";
                                        sizeCustomInput.value = product.size;
                                        hasUpdates = true;
                                    }
                                }
                            }
                            
                            if (hasUpdates && recentlySaved) {
                                showModalUpdateNotification('Product data updated by another admin');
                            }
                        }
                    }
                }
            } catch (error) {
                console.log('Modal product update check failed:', error);
            }
        }
        
        function showModalUpdateNotification(message) {
            // Create a subtle pulse effect on the modal content
            const modalContent = document.querySelector('.modal.show .modal-content');
            if (modalContent) {
                modalContent.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                
                setTimeout(() => {
                    modalContent.style.boxShadow = '';
                }, 1000);
            }
        }
        
        // Enhanced modal data validation - detect external changes
        async function validateModalData(type, id) {
            try {
                let response, data;
                
                if (type === 'user') {
                    response = await fetch(`ajax/get_single_user.php?id=${id}`);
                    data = await response.json();
                    
                    if (data.success && data.user) {
                        const modal = document.getElementById('editUserModal');
                        if (modal && modal.style.display === 'flex') {
                            const currentName = document.getElementById('editUserName').value;
                            const currentRole = document.getElementById('editUserRole').value;
                            
                            if (currentName !== data.user.userName || currentRole !== data.user.userRole) {
                                showModalConflictNotification('This user has been modified by another user. Form data will be updated.');
                                return false; // Data conflict detected
                            }
                        }
                    }
                } else if (type === 'product') {
                    response = await fetch(`ajax/get_single_product.php?id=${id}`);
                    data = await response.json();
                    
                    if (data.success && data.product) {
                        const modal = document.getElementById('editProductModal');
                        if (modal && modal.style.display === 'flex') {
                            const currentName = document.getElementById('editProductName').value;
                            const currentDesc = document.getElementById('editProductDescription').value;
                            const currentQty = document.getElementById('editProductQty').value;
                            const currentPrice = document.getElementById('editProductPrice').value;
                            
                            if (currentName !== data.product.prodName || 
                                currentDesc !== data.product.prodDescription ||
                                currentQty != data.product.quantity ||
                                parseFloat(currentPrice) !== parseFloat(data.product.price.replace(',', ''))) {
                                showModalConflictNotification('This product has been modified by another user. Form data will be updated.');
                                return false; // Data conflict detected
                            }
                        }
                    }
                }
                
                return true; // No conflicts
            } catch (error) {
                console.log('Modal validation check failed:', error);
                return true; // Assume no conflicts on error
            }
        }
        
        function showModalConflictNotification(message) {
            // Create a more prominent notification for conflicts
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #f59e0b;
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                z-index: 10001;
                font-size: 14px;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                max-width: 350px;
                border-left: 4px solid #d97706;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Trigger animation
            requestAnimationFrame(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            });
            
            // Remove after 5 seconds (longer for conflict notifications)
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // Add keyboard support for modals
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Close any open edit modals and stop their updates
                if (currentEditUserId && document.getElementById('editUserModal').style.display === 'flex') {
                    closeModal('editUserModal');
                }
                if (currentEditProductId && document.getElementById('editProductModal').style.display === 'flex') {
                    closeModal('editProductModal');
                }
                // Also close add modals
                if (document.getElementById('userModal').style.display === 'flex') {
                    closeModal('userModal');
                }
                if (document.getElementById('productModal').style.display === 'flex') {
                    closeModal('productModal');
                }
            }
        });

        function startRealTimeUpdates() {
            // Check for updates every 2 seconds
            setInterval(checkForUpdates, 2000);
        }
        
        function checkForUpdates() {
            // Check for user updates
            fetch('ajax/get_users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.users) {
                        const currentHash = JSON.stringify(data.users);
                        if (lastUserUpdate !== 0 && lastUserUpdate !== currentHash) {
                            updateUsersTable(data.users);
                            showUpdateNotification('Users table updated');
                            
                            // Signal modal updates are allowed after external changes
                            if (currentEditUserId) {
                                userFormLastSaved = Date.now();
                            }
                        }
                        lastUserUpdate = currentHash;
                    }
                })
                .catch(error => console.log('User update check failed:', error));
                
            // Check for product updates
            fetch('ajax/get_products.php')
                .then(response => response.json())
                .then(data => {
                    if (data.products) {
                        const currentHash = JSON.stringify(data.products);
                        if (lastProductUpdate !== 0 && lastProductUpdate !== currentHash) {
                            updateProductsTable(data.products);
                            showUpdateNotification('Products table updated');
                            triggerRetailerUpdate();
                            
                            // Signal modal updates are allowed after external changes
                            if (currentEditProductId) {
                                productFormLastSaved = Date.now();
                            }
                        }
                        lastProductUpdate = currentHash;
                    }
                })
                .catch(error => console.log('Product update check failed:', error));
        }
        
        function updateUsersTable(users) {
            const tbody = document.getElementById('usersTableBody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><span class='table-id'>${user.userID}</span></td>
                    <td><span class='table-username'>${escapeHtml(user.userName)}</span></td>
                    <td><span class='table-role role-${user.userRole.toLowerCase()}'>${escapeHtml(user.userRole)}</span></td>
                    <td><span class='table-created'>${user.created_at}</span></td>
                    <td><span class='table-created'>${user.updated_at}</span></td>
                    <td class='actions-cell'>
                        <div class='action-buttons'>
                            <button class='btn btn-edit' onclick='openEditUserModal(${user.userID}, ${JSON.stringify(user.userName)}, ${JSON.stringify(user.userRole)})'>
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                </svg>
                                Edit
                            </button>
                            ${user.canDelete ? `
                                <a href='users/delete_user.php?id=${user.userID}' class='btn btn-delete' onclick="return confirm('Delete this employee?')">
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                        <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                    </svg>
                                    Delete
                                </a>
                            ` : `<span class='btn-placeholder'></span>`}
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Reapply current filters after update
            applyUserFilters();
        }
        
        function updateProductsTable(products) {
            const tbody = document.getElementById('productsTableBody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            products.forEach(product => {
                const row = document.createElement('tr');
                const truncatedDesc = product.prodDescription.length > 50 ? 
                    product.prodDescription.substring(0, 50) + '...' : product.prodDescription;
                
                const imageHtml = product.image_path ? 
                    `<div class='image-container'><img src='../../uploads/${escapeHtml(product.image_path)}' alt='Product Image' class='product-image'></div>` :
                    `<div class='no-image'><svg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21,15 16,10 5,21'></polyline></svg><span>No image</span></div>`;
                
                row.innerHTML = `
                    <td><span class='table-id'>${product.prodID}</span></td>
                    <td><span class='table-name'>${escapeHtml(product.prodName)}</span></td>
                    <td>${imageHtml}</td>
                    <td><span class='table-description'>${escapeHtml(truncatedDesc)}</span></td>
                    <td><span class='table-qty'>${product.quantity}</span></td>
                    <td><span class='table-price'>₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></td>
                    <td><span class='table-color'>${escapeHtml(product.color || 'N/A')}</span></td>
                    <td><span class='table-size'>${escapeHtml(product.size || 'N/A')}</span></td>
                    <td><span class='table-created'>${product.created_at}</span></td>
                    <td><span class='table-created'>${product.updated_at}</span></td>
                    <td class='actions-cell'>
                        <div class='action-buttons'>
                            <button class='btn btn-edit' 
                                onclick='openEditProductModal(
                                ${product.prodID},
                                ${JSON.stringify(product.prodName)},
                                ${JSON.stringify(product.prodDescription)},
                                ${product.quantity},
                                ${product.price},
                                ${JSON.stringify(product.color === 'N/A' ? '' : product.color)},
                                ${JSON.stringify(product.size === 'N/A' ? '' : product.size)},
                                ${JSON.stringify(product.image_path || '')}
                                )'>
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                </svg>
                                Edit
                            </button>
                            <a href='products/delete_product.php?id=${product.prodID}' class='btn btn-delete' onclick="return confirm('Delete this product?')">
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                </svg>
                                Delete
                            </a>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Reapply current filters after update
            applyProductFilters();
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function showUpdateNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 12px 20px;
                border-radius: 6px;
                z-index: 10000;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Trigger animation
            requestAnimationFrame(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            });
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function triggerRetailerUpdate() {
            // Force immediate update by pinging the retailer API
            fetch('../../retailer/api/get_products.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Triggered retailer update check - products found:', data.products ? data.products.length : 0);
                    
                    // Make multiple calls to ensure update is detected
                    setTimeout(() => {
                        fetch('../../retailer/api/get_products.php');
                    }, 500);
                })
                .catch(error => {
                    console.log('Update trigger sent, response:', error);
                });
        }

        // ...existing code...
    </script>           
</body> 


</html>
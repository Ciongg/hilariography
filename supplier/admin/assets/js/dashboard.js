// Supplier Dashboard JavaScript functionality
class Dashboard {
    constructor() {
        this.updateInterval = null;
        this.currentEditUserId = null;
        this.currentEditProductId = null;
        this.lastUserUpdate = 0;
        this.lastProductUpdate = 0;
        this.userFormLastSaved = 0;
        this.productFormLastSaved = 0;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupTabNavigation();
        this.startAutoUpdates();
    }
    
    setupEventListeners() {
        // Search and filter inputs
        this.setupSearchFilters();
        
        // Modal functionality
        this.setupModalHandlers();
        
        // Form submissions
        this.setupFormSubmissions();
        
        // Image preview functionality
        this.setupImagePreviews();
        
        // Password validation
        this.setupPasswordValidation();
        
        // Keyboard support
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.closeAllModals();
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
    
    setupSearchFilters() {
        // User filters
        const userSearchInput = document.getElementById('userSearchInput');
        const roleFilter = document.getElementById('roleFilter');
        
        if (userSearchInput) {
            userSearchInput.addEventListener('input', () => this.applyUserFilters());
        }
        if (roleFilter) {
            roleFilter.addEventListener('change', () => this.applyUserFilters());
        }

        // Product filters
        const productSearchInput = document.getElementById('productSearchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        if (productSearchInput) {
            productSearchInput.addEventListener('input', () => this.applyProductFilters());
        }
        if (minPriceInput) {
            minPriceInput.addEventListener('input', () => this.applyProductFilters());
        }
        if (maxPriceInput) {
            maxPriceInput.addEventListener('input', () => this.applyProductFilters());
        }
    }
    
    setupModalHandlers() {
        // Modal click-outside functionality
        document.querySelectorAll(".modal").forEach(modal => {
            modal.addEventListener("click", e => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });
    }
    
    setupFormSubmissions() {
        // Add product form
        const addProductForm = document.getElementById("addProductForm");
        if (addProductForm) {
            addProductForm.addEventListener("submit", (e) => {
                setTimeout(() => {
                    this.triggerRetailerUpdate();
                    location.reload();
                }, 1000);
            });
        }

        // Edit product form
        const editProductForm = document.getElementById("editProductForm");
        if (editProductForm) {
            editProductForm.addEventListener("submit", (e) => {
                this.productFormLastSaved = Date.now();
                setTimeout(() => {
                    this.triggerRetailerUpdate();
                    location.reload();
                }, 1000);
            });
        }
        
        // Edit user form
        const editUserForm = document.getElementById("editUserForm");
        if (editUserForm) {
            editUserForm.addEventListener("submit", (e) => {
                this.userFormLastSaved = Date.now();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        }
        
        // Add user form
        const addUserForm = document.getElementById("addUserForm");
        if (addUserForm) {
            addUserForm.addEventListener("submit", (e) => {
                this.userFormLastSaved = Date.now();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        }
    }
    
    setupImagePreviews() {
        // Edit product image preview
        document.getElementById("editProductImage")?.addEventListener("change", (e) => {
            const file = e.target.files[0];
            const previewDiv = document.getElementById("editCurrentImagePreview");
            
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewDiv.innerHTML = `
                        <p style="margin: 5px 0; color: #666; font-size: 14px;">New image preview:</p>
                        <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd;">
                    `;
                };
                reader.readAsDataURL(file);
            }
            document.getElementById("removeImageFlag").value = "0";
        });

        // Add product image preview
        document.getElementById("addProductImage")?.addEventListener("change", (e) => {
            const file = e.target.files[0];
            const previewDiv = document.getElementById("addImagePreview");
            
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewDiv.innerHTML = `
                        <p style="margin: 5px 0; color: #666; font-size: 14px;">Image preview:</p>
                        <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd; display: block; margin-bottom: 10px;">
                        <button type="button" onclick="dashboard.removeAddImage()" style="background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; cursor: pointer;">Remove Image</button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = "";
            }
        });
    }
    
    setupPasswordValidation() {
        const passwordField = document.getElementById("addUserPassword");
        const confirmPasswordField = document.getElementById("addConfirmPassword");
        const addUserForm = document.getElementById("addUserForm");

        if (passwordField && confirmPasswordField) {
            passwordField.addEventListener("input", () => this.validatePasswords());
            confirmPasswordField.addEventListener("input", () => this.validatePasswords());
        }

        if (addUserForm) {
            addUserForm.addEventListener("submit", (e) => {
                if (!this.validatePasswords()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    }
    
    setupTabNavigation() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get("tab");
        if (tab === "products") {
            this.showTab("products");
        } else {
            this.showTab("users");
        }
    }
    
    // Tab functionality
    showTab(tab) {
        document.querySelectorAll('.tab').forEach(btn => {
            btn.classList.toggle('active', btn.textContent.toLowerCase() === tab);
        });
        document.getElementById('usersTable').classList.toggle('hidden', tab !== 'users');
        document.getElementById('productsTable').classList.toggle('hidden', tab !== 'products');
        
        if (tab === 'products') {
            setTimeout(() => {
                this.triggerRetailerUpdate();
            }, 300);
        }
    }
    
    // Modal functionality
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'flex';
        modal.offsetHeight;
        modal.classList.add('show');
    }
    
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            this.resetModalFields(modalId);
            
            // Reset edit modal state if it's the edit product modal
            if (modalId === 'editProductModal') {
                this.showEditModalState('content');
            }
        }, 150);
    }
    
    closeAllModals() {
        const modals = ['editUserModal', 'editProductModal', 'userModal', 'productModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'flex') {
                this.closeModal(modalId);
            }
        });
    }
    
    resetModalFields(modalId) {
        if (modalId === 'productModal') {
            document.getElementById('addProductColor').value = '';
            document.getElementById('addProductColorCustom').style.display = 'none';
            document.getElementById('addProductColorCustom').value = '';
            document.getElementById('addProductSize').value = '';
            document.getElementById('addProductSizeCustom').style.display = 'none';
            document.getElementById('addProductSizeCustom').value = '';
        }
    }
    
    // Edit modal functions
    openEditUserModal(id, name, role) {
        document.getElementById("editUserID").value = id;
        document.getElementById("editUserName").value = name;
        document.getElementById("editUserRole").value = role;
        this.currentEditUserId = id;
        this.openModal("editUserModal");
    }
    
    openEditUserModalFromData(button) {
        const id = button.getAttribute('data-user-id');
        const name = button.getAttribute('data-user-name');
        const role = button.getAttribute('data-user-role');
        
        this.openEditUserModal(id, name, role);
    }
    
    openEditProductModal(id, name, desc, qty, price, color, size, imagePath) {
        this.currentEditProductId = id;
        this.openModal("editProductModal");
        
        // Show loading state first
        this.showEditModalState('loading');
        
        // Verify product still exists before showing form
        this.verifyProductExists(id).then(exists => {
            if (exists) {
                this.showEditModalState('content');
                document.getElementById("editProductID").value = id;
                document.getElementById("editProductName").value = name;
                document.getElementById("editProductDescription").value = desc;
                document.getElementById("editProductQty").value = qty;
                document.getElementById("editProductPrice").value = price;
                this.setupProductModalFields(color, size, imagePath);
            } else {
                this.showEditModalState('deleted');
            }
        }).catch(error => {
            console.error('Error verifying product:', error);
            this.showEditModalState('error');
        });
    }
    
    openEditProductModalFromData(button) {
        const id = button.getAttribute('data-product-id');
        const name = button.getAttribute('data-product-name');
        const desc = button.getAttribute('data-product-description');
        const qty = button.getAttribute('data-product-quantity');
        const price = button.getAttribute('data-product-price');
        const color = button.getAttribute('data-product-color');
        const size = button.getAttribute('data-product-size');
        const imagePath = button.getAttribute('data-product-image');
        
        this.openEditProductModal(id, name, desc, qty, price, color, size, imagePath);
    }
    
    setupProductModalFields(color, size, imagePath) {
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
        
        // Show current image preview
        const previewDiv = document.getElementById("editCurrentImagePreview");
        if (imagePath) {
            previewDiv.innerHTML = `
                <p style="margin: 5px 0; color: #666; font-size: 14px;">Current image:</p>
                <img src="../../uploads/${imagePath}" style="width:100px;height:100px;object-fit:cover;border-radius:4px; border: 1px solid #ddd; display: block; margin-bottom: 10px;">
                <button type="button" onclick="dashboard.removeCurrentImage()" style="background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; cursor: pointer;">Remove Image</button>
            `;
        } else {
            previewDiv.innerHTML = "<p style='margin: 5px 0; color: #999; font-size: 14px;'>No current image</p>";
        }
    }
    
    // Image functions
    removeCurrentImage() {
        document.getElementById("removeImageFlag").value = "1";
        document.getElementById("editCurrentImagePreview").innerHTML = "<p style='margin: 5px 0; color: #999; font-size: 14px;'>Image will be removed when saved</p>";
    }
    
    removeAddImage() {
        document.getElementById("addProductImage").value = "";
        document.getElementById("addImagePreview").innerHTML = "";
    }
    
    // Custom input toggles
    toggleCustomColorInput(type) {
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
    
    toggleCustomSizeInput(type) {
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
    
    // Password validation
    validatePasswords() {
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
    
    // Filter functionality
    applyUserFilters() {
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
    
    clearUserFilters() {
        document.getElementById('userSearchInput').value = '';
        document.getElementById('roleFilter').value = '';
        this.applyUserFilters();
    }
    
    applyProductFilters() {
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
    
    clearProductFilters() {
        document.getElementById('productSearchInput').value = '';
        document.getElementById('minPrice').value = '';
        document.getElementById('maxPrice').value = '';
        this.applyProductFilters();
    }
    
    // Auto-update functionality
    startAutoUpdates() {
        this.stopAutoUpdates();
        this.updateInterval = setInterval(() => this.checkForUpdates(), 2000);
    }
    
    stopAutoUpdates() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }
    
    async checkForUpdates() {
        try {
            // Check for user updates
            const userResponse = await fetch('ajax/get_users.php');
            const userData = await userResponse.json();
            
            if (userData.users) {
                const currentHash = JSON.stringify(userData.users);
                if (this.lastUserUpdate !== 0 && this.lastUserUpdate !== currentHash) {
                    this.updateUsersTable(userData.users);
                    this.showUpdateNotification('Users table updated');
                }
                this.lastUserUpdate = currentHash;
            }
            
            // Check for product updates
            const productResponse = await fetch('ajax/get_products.php');
            const productData = await productResponse.json();
            
            if (productData.products) {
                const currentHash = JSON.stringify(productData.products);
                if (this.lastProductUpdate !== 0 && this.lastProductUpdate !== currentHash) {
                    this.updateProductsTable(productData.products);
                    this.showUpdateNotification('Products table updated');
                    this.triggerRetailerUpdate();
                    
                    // Check if current edit modal product still exists
                    if (this.currentEditProductId) {
                        const editModal = document.getElementById('editProductModal');
                        if (editModal && editModal.style.display === 'flex') {
                            const productExists = productData.products.some(p => p.prodID == this.currentEditProductId);
                            if (!productExists) {
                                this.showEditModalState('deleted');
                            }
                        }
                    }
                }
                this.lastProductUpdate = currentHash;
            }
        } catch (error) {
            console.error('Update check failed:', error);
        }
    }
    
    // Table updates
    updateUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><span class='table-id'>${user.userID}</span></td>
                <td><span class='table-username'>${this.escapeHtml(user.userName)}</span></td>
                <td><span class='table-role role-${user.userRole.toLowerCase()}'>${this.escapeHtml(user.userRole)}</span></td>
                <td><span class='table-created'>${user.created_at}</span></td>
                <td><span class='table-created'>${user.updated_at}</span></td>
                <td class='actions-cell'>
                    <div class='action-buttons'>
                        <button class='btn btn-edit' 
                            data-user-id='${user.userID}'
                            data-user-name='${this.escapeHtml(user.userName)}'
                            data-user-role='${this.escapeHtml(user.userRole)}'
                            onclick='dashboard.openEditUserModalFromData(this)'>
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
        
        this.applyUserFilters();
    }
    
    updateProductsTable(products) {
        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        products.forEach(product => {
            const row = document.createElement('tr');
            const truncatedDesc = product.prodDescription.length > 50 ? 
                product.prodDescription.substring(0, 50) + '...' : product.prodDescription;
            
            const imageHtml = product.image_path ? 
                `<div class='image-container'><img src='../../uploads/${this.escapeHtml(product.image_path)}' alt='Product Image' class='product-image'></div>` :
                `<div class='no-image'><svg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21,15 16,10 5,21'></polyline></svg><span>No image</span></div>`;
            
            row.innerHTML = `
                <td><span class='table-id'>${product.prodID}</span></td>
                <td><span class='table-name'>${this.escapeHtml(product.prodName)}</span></td>
                <td>${imageHtml}</td>
                <td><span class='table-description'>${this.escapeHtml(truncatedDesc)}</span></td>
                <td><span class='table-qty'>${product.quantity}</span></td>
                <td><span class='table-price'>₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></td>
                <td><span class='table-color'>${this.escapeHtml(product.color || 'N/A')}</span></td>
                <td><span class='table-size'>${this.escapeHtml(product.size || 'N/A')}</span></td>
                <td><span class='table-created'>${product.created_at}</span></td>
                <td><span class='table-created'>${product.updated_at}</span></td>
                <td class='actions-cell'>
                    <div class='action-buttons'>
                        <button class='btn btn-edit' 
                            data-product-id='${product.prodID}'
                            data-product-name='${this.escapeHtmlAttribute(product.prodName)}'
                            data-product-description='${this.escapeHtmlAttribute(product.prodDescription)}'
                            data-product-quantity='${product.quantity}'
                            data-product-price='${product.price}'
                            data-product-color='${this.escapeHtmlAttribute(product.color === 'N/A' ? '' : product.color)}'
                            data-product-size='${this.escapeHtmlAttribute(product.size === 'N/A' ? '' : product.size)}'
                            data-product-image='${this.escapeHtmlAttribute(product.image_path || '')}'
                            onclick='dashboard.openEditProductModalFromData(this)'>
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
        
        this.applyProductFilters();
    }
    
        // Utility functions
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    escapeHtmlAttribute(text) {
        if (!text) return '';
        // Escape special characters for HTML attributes
        return text
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
    
    // Modal state management
    showEditModalState(state) {
        const states = ['loading', 'error', 'deleted', 'content'];
        states.forEach(s => {
            const element = document.getElementById(`editModal${s.charAt(0).toUpperCase() + s.slice(1)}State`);
            if (element) {
                element.style.display = s === state ? 'block' : 'none';
            }
        });
        
        const contentElement = document.getElementById('editProductContent');
        if (contentElement) {
            contentElement.style.display = state === 'content' ? 'block' : 'none';
        }
    }
    
    async verifyProductExists(productId) {
        try {
            const response = await fetch(`ajax/get_single_product.php?id=${productId}`);
            const data = await response.json();
            return data.success && data.product;
        } catch (error) {
            console.error('Error verifying product:', error);
            return false;
        }
    }
    

    
    showUpdateNotification(message) {
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
        
        requestAnimationFrame(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        });
        
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
    
    triggerRetailerUpdate() {
        fetch('../../retailer/api/get_products.php')
            .then(response => response.json())
            .then(data => {
                console.log('Triggered retailer update check - products found:', data.products ? data.products.length : 0);
                
                setTimeout(() => {
                    fetch('../../retailer/api/get_products.php');
                }, 500);
            })
            .catch(error => {
                console.log('Update trigger sent, response:', error);
            });
    }
}

// Global functions for backward compatibility
function showTab(tab) {
    dashboard.showTab(tab);
}

function openModal(modalId) {
    dashboard.openModal(modalId);
}

function closeModal(modalId) {
    dashboard.closeModal(modalId);
}

function openEditUserModal(id, name, role) {
    dashboard.openEditUserModal(id, name, role);
}

function openEditUserModalFromData(button) {
    dashboard.openEditUserModalFromData(button);
}

function openEditProductModal(id, name, desc, qty, price, color, size, imagePath) {
    dashboard.openEditProductModal(id, name, desc, qty, price, color, size, imagePath);
}

function openEditProductModalFromData(button) {
    dashboard.openEditProductModalFromData(button);
}

function removeCurrentImage() {
    dashboard.removeCurrentImage();
}

function removeAddImage() {
    dashboard.removeAddImage();
}

function toggleCustomColorInput(type) {
    dashboard.toggleCustomColorInput(type);
}

function toggleCustomSizeInput(type) {
    dashboard.toggleCustomSizeInput(type);
}

function applyUserFilters() {
    dashboard.applyUserFilters();
}

function clearUserFilters() {
    dashboard.clearUserFilters();
}

function applyProductFilters() {
    dashboard.applyProductFilters();
}

function clearProductFilters() {
    dashboard.clearProductFilters();
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
}); 
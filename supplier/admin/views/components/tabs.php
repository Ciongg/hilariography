<?php
$currentTab = $_GET['tab'] ?? 'users';
?>
<div class="tabs">
    <button class="tab <?php echo $currentTab === 'users' ? 'active' : ''; ?>" onclick="showTab('users')">Users</button>
    <button class="tab <?php echo $currentTab === 'products' ? 'active' : ''; ?>" onclick="showTab('products')">Products</button>
</div> 
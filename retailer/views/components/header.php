<?php
require_once __DIR__ . '/../../config/config.php';
?>
<div class="header">
    <div class="header-content">
        <div class="logo-section">
            <img src="assets/img/hilariography_logo.png" alt="Hilariography Logo" class="logo-image">
            <div>
                <h1><?php echo APP_NAME; ?></h1>
                <p><?php echo APP_TAGLINE; ?></p>
            </div>
        </div>
        <div class="user-section">
            <div class="user-avatar">
                <?php echo strtoupper(substr(htmlspecialchars($_SESSION["retailer_username"])[0], 0, 1)); ?>
            </div>
            <span class="user-name">
                <?php echo htmlspecialchars($_SESSION["retailer_username"]); ?>
                <span class="user-role">(<?php echo Helpers::getUserRoleDisplay($_SESSION["retailer_role"] ?? 'user'); ?>)</span>
            </span>
            <a href="../auth/logout.php" class="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="logout-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Logout
            </a>
        </div>
    </div>
</div> 
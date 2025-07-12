<?php
require_once __DIR__ . '/../../config/config.php';
?>
<div class="header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div class="logo">
            <img src="../assets/img/fabsuplogo.png" alt="FabricSup Logo">
        </div>
        <h1 style="margin: 0;"><?php echo APP_NAME; ?></h1>
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
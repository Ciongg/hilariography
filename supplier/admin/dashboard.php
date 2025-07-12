<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'utils/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/dashboard.css" />
</head>
<body>
    <div id="dashboard" class="dashboard">
        <div class="container">
            <?php include 'views/components/header.php'; ?>
            <?php include 'views/components/tabs.php'; ?>
            
            <?php include 'views/users/users_table.php'; ?>
            <?php include 'views/products/products_table.php'; ?>
        </div>
    </div>

    <?php 
    include 'views/modals/user_modal.php';
    include 'views/modals/product_modal.php';
    ?>

    <script src="assets/js/dashboard.js"></script>
</body>
</html> 
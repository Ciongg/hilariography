<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["retailer_loggedin"]) || $_SESSION["retailer_loggedin"] !== true) {
    header("Location: auth/login.php");
    exit();
}

// Redirect to consolidated dashboard
header("Location: dashboard.php");
exit();
?>

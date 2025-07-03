<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../../includes/db.php");

if (isset($_GET["id"])) {
  $id = intval($_GET["id"]);
  $stmt = $conn->prepare("DELETE FROM dbproduct WHERE prodID = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  
  // Create notification file for real-time updates
  file_put_contents("../../../temp/product_update_" . time() . ".txt", "delete_product");
}

// redirect back, *with tab=products*
header("Location: ../dashboard.php?tab=products");
exit;
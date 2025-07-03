<?php
session_start();
include("../../../includes/db.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["productName"]);
  $desc = trim($_POST["productDescription"]);
  $qty = intval($_POST["productQty"]);
  $price = floatval($_POST["productPrice"]);
  
  // Handle color - use custom input if "other" is selected
  $color = "";
  if (!empty($_POST["productColor"])) {
    if ($_POST["productColor"] === "other" && !empty($_POST["productColorCustom"])) {
      $color = trim($_POST["productColorCustom"]);
    } elseif ($_POST["productColor"] !== "other") {
      $color = trim($_POST["productColor"]);
    }
  }
  
  // Handle size - use custom input if "other" is selected
  $size = "";
  if (!empty($_POST["productSize"])) {
    if ($_POST["productSize"] === "other" && !empty($_POST["productSizeCustom"])) {
      $size = trim($_POST["productSizeCustom"]);
    } elseif ($_POST["productSize"] !== "other") {
      $size = trim($_POST["productSize"]);
    }
  }
  
  $imagePath = null;
  
  if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == UPLOAD_ERR_OK) {
    $targetDir = '../../../uploads/';
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }
    $fileName = uniqid() . '_' . basename($_FILES['productImage']['name']);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
      $imagePath = $fileName;
    }
  }
  
  $stmt = $conn->prepare("INSERT INTO dbproduct (prodName, image_path, prodDescription, quantity, price, color, size, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
  if ($stmt) {
    $stmt->bind_param("sssidss", $name, $imagePath, $desc, $qty, $price, $color, $size);
    $stmt->execute();
    $stmt->close();
    
    // Create notification file for real-time updates
    file_put_contents("../../../temp/product_update_" . time() . ".txt", "add_product");
  } else {
    die("Database error: " . $conn->error);
  }
}

header("Location: ../dashboard.php?tab=products");
exit;
?>
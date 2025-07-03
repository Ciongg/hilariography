<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prodID = intval($_POST["prodID"]);
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
    $removeImage = isset($_POST["removeImage"]) && $_POST["removeImage"] === "1";
    
    // Get current image path
    $stmt = $conn->prepare("SELECT image_path FROM dbproduct WHERE prodID = ?");
    $stmt->bind_param("i", $prodID);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentProduct = $result->fetch_assoc();
    $currentImagePath = $currentProduct ? $currentProduct['image_path'] : null;
    $stmt->close();
    
    // Handle image upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == UPLOAD_ERR_OK) {
        // Delete old image if exists
        if ($currentImagePath && file_exists('../../../uploads/' . $currentImagePath)) {
            unlink('../../../uploads/' . $currentImagePath);
        }
        
        $targetDir = '../../../uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['productImage']['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
            $imagePath = $fileName;
        }
    } elseif ($removeImage) {
        // Remove current image
        if ($currentImagePath && file_exists('../../../uploads/' . $currentImagePath)) {
            unlink('../../../uploads/' . $currentImagePath);
        }
        $imagePath = null;
    } else {
        // Keep current image
        $imagePath = $currentImagePath;
    }
    
    $stmt = $conn->prepare("UPDATE dbproduct SET prodName = ?, image_path = ?, prodDescription = ?, quantity = ?, price = ?, color = ?, size = ?, updated_at = CURRENT_TIMESTAMP WHERE prodID = ?");
    if ($stmt) {
        $stmt->bind_param("sssidssi", $name, $imagePath, $desc, $qty, $price, $color, $size, $prodID);
        $stmt->execute();
        $stmt->close();
        
        // Create notification file for real-time updates
        file_put_contents("../../../temp/product_update_" . time() . ".txt", "edit_product");
    } else {
        die("Database error: " . $conn->error);
    }

    header("Location: ../dashboard.php?tab=products");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Edit Product</title>
</head>

<body>
  <h2>Edit Product</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="prodID" value="<?= $product['prodID'] ?>">
    <label>Name</label>
    <input type="text" name="productName" value="<?= htmlspecialchars($product['prodName']) ?>" required>
    <label>Description</label>
    <textarea name="productDescription" required><?= htmlspecialchars($product['prodDescription']) ?></textarea>
    <label>Quantity</label>
    <input type="number" name="productQty" value="<?= $product['quantity'] ?>" required>
    <label>Price</label>
    <input type="number" step="0.01" name="productPrice" value="<?= $product['price'] ?>" required>
    <label>Color</label>
    <select name="productColor">
      <option value="">Select Color</option>
      <option value="red" <?= $product['color'] == 'red' ? 'selected' : '' ?>>Red</option>
      <option value="blue" <?= $product['color'] == 'blue' ? 'selected' : '' ?>>Blue</option>
      <option value="green" <?= $product['color'] == 'green' ? 'selected' : '' ?>>Green</option>
      <option value="other" <?= $product['color'] == 'other' ? 'selected' : '' ?>>Other</option>
    </select>
    <input type="text" name="productColorCustom" placeholder="Enter custom color" value="<?= $product['color'] == 'other' ? htmlspecialchars($product['color']) : '' ?>" style="display:none;">
    <label>Size</label>
    <select name="productSize">
      <option value="">Select Size</option>
      <option value="small" <?= $product['size'] == 'small' ? 'selected' : '' ?>>Small</option>
      <option value="medium" <?= $product['size'] == 'medium' ? 'selected' : '' ?>>Medium</option>
      <option value="large" <?= $product['size'] == 'large' ? 'selected' : '' ?>>Large</option>
      <option value="other" <?= $product['size'] == 'other' ? 'selected' : '' ?>>Other</option>
    </select>
    <input type="text" name="productSizeCustom" placeholder="Enter custom size" value="<?= $product['size'] == 'other' ? htmlspecialchars($product['size']) : '' ?>" style="display:none;">
    <label>Image</label>
    <input type="file" name="productImage" accept="image/*">
    <label>Remove Image</label>
    <input type="checkbox" name="removeImage" value="1">
    <button type="submit">Save</button>
  </form>

  <script>
    // Show/hide custom color input
    document.querySelector('select[name="productColor"]').addEventListener('change', function() {
      var customColorInput = document.querySelector('input[name="productColorCustom"]');
      if (this.value === 'other') {
        customColorInput.style.display = 'block';
        customColorInput.required = true;
      } else {
        customColorInput.style.display = 'none';
        customColorInput.required = false;
      }
    });

    // Show/hide custom size input
    document.querySelector('select[name="productSize"]').addEventListener('change', function() {
      var customSizeInput = document.querySelector('input[name="productSizeCustom"]');
      if (this.value === 'other') {
        customSizeInput.style.display = 'block';
        customSizeInput.required = true;
      } else {
        customSizeInput.style.display = 'none';
        customSizeInput.required = false;
      }
    });
  </script>
</body>

</html>
<?php
session_start();
include("../../../includes/db.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["userName"]);
  $password = trim($_POST["userPassword"]);
  $role = trim($_POST["userRole"]);


  if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long";
  }


  if (empty($errors)) {

    $stmt = $conn->prepare("INSERT INTO dbusers (userName, password, userRole, created_at, updated_at) VALUES (?,?,?, NOW(), NOW())");
    $stmt->bind_param("sss", $name, $password, $role);
    $stmt->execute();
    $stmt->close();
    
    // Create notification file for real-time updates
    file_put_contents("../../../temp/user_update_" . time() . ".txt", "add_user");
    
    header("Location: ../dashboard.php?tab=users&success=true&action=add_user");
    exit;
  } else {
    // Redirect back with errors
    $errorString = implode("|", $errors);
    header("Location: ../dashboard.php?tab=users&error=" . urlencode($errorString) . "&username=" . urlencode($name) . "&role=" . urlencode($role));
    exit;
  }
}

header("Location: ../dashboard.php?tab=users");
exit;
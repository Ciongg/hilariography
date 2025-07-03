<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = intval($_POST["userID"]);
  $name = trim($_POST["userName"]);
  $role = trim($_POST["userRole"]);
  
  // Update without changing password
  $stmt = $conn->prepare("UPDATE dbusers SET userName=?, userRole=?, updated_at=NOW() WHERE userID=?");
  $stmt->bind_param("ssi", $name, $role, $id);
  
  $stmt->execute();
  $stmt->close();
  
  // Create notification file for real-time updates
  file_put_contents("../../../temp/user_update_" . time() . ".txt", "edit_user");
  
  header("Location: ../dashboard.php");
  exit;
}

if (isset($_GET["id"])) {
  $id = intval($_GET["id"]);
  $stmt = $conn->prepare("SELECT * FROM dbusers WHERE userID=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Edit User</title>
</head>

<body>
  <h2>Edit User</h2>
  <form method="post">
    <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
    <label>Name</label>
    <input type="text" name="userName" value="<?= htmlspecialchars($user['userName']) ?>" required>
    <label>Role</label>
    <select name="userRole" required>
      <option value="Admin" <?= $user['userRole'] == "Admin" ? "selected" : "" ?>>Admin</option>
      <option value="Employee" <?= $user['userRole'] == "Employee" ? "selected" : "" ?>>Employee</option>
    </select>
    <button type="submit">Save</button>
  </form>
</body>

</html>
<?php
session_start();
include("../../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);

  $stmt = $conn->prepare("SELECT * FROM dbusers WHERE userName = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $username;
    $_SESSION["userRole"] = $user['userRole'];
    header("Location: ../admin/dashboard.php");
    exit;
  } else {
    $error = "Invalid username or password.";
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Admin Login</title>
  <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
  <div class="login-container">
    
    <div class="login-card">
      <div class="login-logo">
         <img src="../assets/img/fabsuplogo.png" alt="FabricSup Logo">
       </div>
      <h2>Admin Login</h2>
      <form method="post">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required>
          <?php if (!empty($error)) echo "<p style='color:red; margin-top: 5px; margin-bottom: 0; text-align: left;'>$error</p>"; ?>
        </div>
        <button class="login-btn" type="submit">Login</button>
      </form>
    </div>
  </div>
</body>

</html>
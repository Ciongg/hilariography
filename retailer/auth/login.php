<?php
session_start();
include("../../includes/db_retailer.php");

$errorMessages = [
    "email" => "",
    "password" => "",
    "general" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if (empty($email)) {
        $errorMessages["email"] = "Email is required.";
    }
    if (empty($password)) {
        $errorMessages["password"] = "Password is required.";
    }

    if (empty($errorMessages["email"]) && empty($errorMessages["password"])) {
        $stmt = $conn_retailer->prepare("SELECT * FROM dbusers WHERE email = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn_retailer->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password']) {
                $_SESSION["retailer_loggedin"] = true;
                $_SESSION["retailer_username"] = $user['username'];
                $_SESSION["retailer_id"] = $user['userID'];
                $_SESSION["retailer_role"] = $user['userRole'];
                header("Location: ../dashboard.php");
                exit;
            } else {
                $errorMessages["password"] = "Invalid email or password.";
            }
        } else {
            $errorMessages["password"] = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilariography - Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === "password") {
                field.type = "text";
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';
            } else {
                field.type = "password";
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <div class="brand-header">
                <div class="login-logo">
                    <img src="../assets/img/hilariography_logo.png" alt="Hilariography Logo" class="login-logo-img">
                </div>
                <div class="brand-name">Hilariography</div>
                <div class="welcome-content" style="margin-top: 60px;">
                    <h1 class="welcome-title">Welcome Back!</h1>
                    <p class="welcome-subtitle">Continue your creative journey with us. Sign in to access your account and explore the prints of the Sample Royals.</p>
                </div>
            </div>
        </div>
        <div class="auth-section">
            <div class="auth-header">
                <h2 class="auth-title">Log in</h2>
                <p class="auth-subtitle">Please enter your credentials to continue</p>
            </div>
            <form method="post" class="form active">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-input" name="email" id="email" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (!empty($errorMessages["email"])) echo "<p style='color:red;'>{$errorMessages["email"]}</p>"; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input type="password" class="form-input" name="password" id="password" placeholder="Password" required autocomplete="current-password" data-toggle="false">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errorMessages["password"])) echo "<p style='color:red;'>{$errorMessages["password"]}</p>"; ?>
                </div>
                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" class="checkbox" id="remember">
                        <label for="remember" class="checkbox-label">Remember Me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="submit-btn">Log in</button>
            </form>
            <div class="divider">
                <span>Or</span>
            </div>
            <button class="secondary-btn" onclick="window.location.href='register.php'">Sign up</button>
        </div>
    </div>
</body>
</html>
             

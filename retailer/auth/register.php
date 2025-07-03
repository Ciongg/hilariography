<?php
session_start();
include("../../includes/db_retailer.php");

$errorMessages = [
    "username" => "",
    "password" => "",
    "confirmPassword" => "",
    "email" => "",
    "phoneNumber" => "",
    "general" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmPassword"]);
    $email = trim($_POST["email"]);
    $phoneNumber = trim($_POST["phoneNumber"]);

    // Basic validation
    if (empty($email)) {
        $errorMessages["email"] = "Email is required.";
    }
    if (empty($username)) {
        $errorMessages["username"] = "Username is required.";
    }
    if (empty($phoneNumber)) {
        $errorMessages["phoneNumber"] = "Phone number is required.";
    }
    if (empty($password)) {
        $errorMessages["password"] = "Password is required.";
    }
    if (empty($confirmPassword)) {
        $errorMessages["confirmPassword"] = "Confirm password is required.";
    }

    // Validate phone number length
    if (!empty($phoneNumber) && (strlen($phoneNumber) !== 11 || !ctype_digit($phoneNumber))) {
        $errorMessages["phoneNumber"] = "Phone number must be exactly 11 digits.";
    }

    // Validate password match
    if (!empty($password) && !empty($confirmPassword) && $password !== $confirmPassword) {
        $errorMessages["confirmPassword"] = "Passwords do not match.";
    }

    // Check if all basic validations pass
    $hasErrors = false;
    foreach ($errorMessages as $error) {
        if (!empty($error)) {
            $hasErrors = true;
            break;
        }
    }

    if (!$hasErrors) {
        // Check email uniqueness
        $email_stmt = $conn_retailer->prepare("SELECT * FROM dbusers WHERE email = ?");
        $email_stmt->bind_param("s", $email);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();

        if ($email_result->num_rows > 0) {
            $errorMessages["email"] = "Email already exists.";
        }
        $email_stmt->close();

        // Check username uniqueness
        $check_stmt = $conn_retailer->prepare("SELECT * FROM dbusers WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errorMessages["username"] = "Username already exists.";
        }
        $check_stmt->close();

        // Check phone number uniqueness
        $phone_stmt = $conn_retailer->prepare("SELECT * FROM dbusers WHERE phoneNumber = ?");
        $phone_stmt->bind_param("s", $phoneNumber);
        $phone_stmt->execute();
        $phone_result = $phone_stmt->get_result();

        if ($phone_result->num_rows > 0) {
            $errorMessages["phoneNumber"] = "Phone number already exists.";
        }
        $phone_stmt->close();


        if (empty($errorMessages["email"]) && empty($errorMessages["username"]) && empty($errorMessages["phoneNumber"])) {

            $stmt = $conn_retailer->prepare("INSERT INTO dbusers (username, password, email, phoneNumber, userRole, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            
            if ($stmt === false) {
                $errorMessages["general"] = "Database error: " . $conn_retailer->error;
            } else {
                $defaultRole = "user";
                $stmt->bind_param("sssss", $username, $password, $email, $phoneNumber, $defaultRole);

                if ($stmt->execute()) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $errorMessages["general"] = "Registration failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilariography - Register</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSubmitButton() {
            const termsCheckbox = document.getElementById("terms");
            const submitButton = document.getElementById("submitBtn");
            submitButton.disabled = !termsCheckbox.checked;
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.parentElement.querySelector('.password-toggle');
            
            if (field.type === "password") {
                field.type = "text";
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';
            } else {
                field.type = "password";
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>';
            }
        }

        function limitPhoneNumber(input) {
            // Remove any non-digit characters
            input.value = input.value.replace(/\D/g, '');
       
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
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
                    <h1 class="welcome-title">Join Now!</h1>
                    <p class="welcome-subtitle">Continue your creative journey with us. Sign up to create your account and explore the prints of the Sample Royals.</p>
                </div>
            </div>
        </div>
        <div class="auth-section">
            <div class="auth-header">
                <h2 class="auth-title">Sign up</h2>
                <p class="auth-subtitle">Create your account to get started</p>
            </div>
            <?php if (!empty($success)) : ?>
            <script>
                Swal.fire({
                    title: 'Registration Successful!',
                    text: 'You can now log in to your account.',
                    icon: 'success',
                    confirmButtonText: 'Go to Login',
                    confirmButtonColor: '#228B22',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            </script>
            <?php endif; ?>
            
            <?php if (!empty($errorMessages["general"])) : ?>
            <div style="background: #ffebee; border: 1px solid #f44336; color: #d32f2f; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($errorMessages["general"]); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" class="form active">
                <div class="form-group">
                    <label for="email">Email Address <span style="color: red;">*</span></label>
                    <input type="email" class="form-input" name="email" id="email" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (!empty($errorMessages["email"])) echo "<p style='color:red;'>{$errorMessages["email"]}</p>"; ?>
                </div>
                <div class="form-group">
                    <label for="username">Username <span style="color: red;">*</span></label>
                    <input type="text" class="form-input" name="username" id="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <?php if (!empty($errorMessages["username"])) echo "<p style='color:red;'>{$errorMessages["username"]}</p>"; ?>
                </div>
                <div class="form-group">
                    <label for="phoneNumber">Phone Number <span style="color: red;">*</span></label>
                    <input type="tel" class="form-input" name="phoneNumber" id="phoneNumber" placeholder="Phone Number" required value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>" oninput="limitPhoneNumber(this)" maxlength="11">
                    <?php if (!empty($errorMessages["phoneNumber"])) echo "<p style='color:red;'>{$errorMessages["phoneNumber"]}</p>"; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password <span style="color: red;">*</span></label>
                    <div class="password-input-group">
                        <input type="password" class="form-input" name="password" id="password" placeholder="Password" required autocomplete="new-password" data-toggle="false">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errorMessages["password"])) echo "<p style='color:red;'>{$errorMessages["password"]}</p>"; ?>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password <span style="color: red;">*</span></label>
                    <div class="password-input-group">
                        <input type="password" class="form-input" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required autocomplete="new-password" data-toggle="false">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($errorMessages["confirmPassword"])) echo "<p style='color:red;'>{$errorMessages["confirmPassword"]}</p>"; ?>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" class="checkbox" id="terms" onchange="toggleSubmitButton()" required <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                    <label for="terms" class="checkbox-label">I agree to the Terms of Service and Privacy Policy</label>
                </div>
                <button type="submit" class="submit-btn" id="submitBtn" disabled>Sign up</button>
            </form>
            <div class="divider">
                <span>Or</span>
            </div>
            <button class="secondary-btn" onclick="window.location.href='login.php'">Back to Login</button>
        </div>
    </div>
</body>
</html>
    </div>
</body>
</html>

<?php
session_start();
include("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // ✅ Hash the new password before saving
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password for the given admin username
        $sql = "UPDATE admin SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $username);
        $stmt->execute();

        // Check if a row was actually updated
        if ($stmt->affected_rows > 0) {
            $success = "✅ Password updated successfully! <a href='admin_login.php'>Login now</a>";
        } else {
            $error = "❌ Username not found or error updating password!";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password - Admin</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_login.css">
    </head>
    <body>
        <div class="login-container">
            <div class="login-box">
                <div class="brand">
                    <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
                    <h2>Reset Admin Password</h2>
                </div>

                <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
                <?php if(isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>

                <form action="admin_forgot_pwd.php" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username">Admin Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>

                    <button type="submit" class="login-btn">Reset Password</button>
                </form>

                <div class="extras">
                    <a href="admin_login.php">Back to Login</a>
                </div>
            </div>
        </div>
    </body>
</html>

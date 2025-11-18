<?php
require '../_base.php';

$_title = 'KAWAII.Reset Password';
include '../_head.php';

$error = "";
$success = "";

// Clean up expired tokens first
$stmt = $_db->prepare("DELETE FROM token WHERE expire < NOW()");
$stmt->execute();

// Check if token is valid
$token = req('token');
$user_id = null;

if ($token) {
    $stmt = $_db->prepare("
        SELECT memberID FROM token 
        WHERE id = ? AND type = 'reset_password'
    ");
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tokenData) {
        $error = "Invalid or expired password reset link. Please request a new one.";
    } else {
        $user_id = $tokenData['memberID'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (!$newPassword || !$confirmPassword) {
        $error = "All fields are required.";
    } elseif (strlen($newPassword) < 8) {
        $error = "New password must be at least 8 characters long.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else {
        // Hash the new password
        $hashedPassword = sha1($newPassword);

        // Update password in database
        $stmt = $_db->prepare("UPDATE member SET memberPassword = ? WHERE memberID = ?");
        if ($stmt->execute([$hashedPassword, $user_id])) {
            // Delete the used token
            $stmt = $_db->prepare("DELETE FROM token WHERE id = ?");
            $stmt->execute([$token]);

            $success = "Password reset successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <div class="reset-password-container ">
        <h1>Reset Your Password</h1>

        <?php if ($error): ?>
            <div class="error-message">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <p><?= $success ?></p>
            </div>
        <?php elseif ($user_id): ?>
            <form action="resetPassword.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" name="newPassword" id="newPassword" placeholder="Enter new password" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm new password" required minlength="8">
                </div>

                <button type="submit" class="form-button">Reset Password</button>
            </form>
        <?php else: ?>
            <p>Please use the password reset link sent to your email.</p>
        <?php endif; ?>
    </div>

    <script>
        // Front-end password validation (optional enhancement)
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword.length < 8) {
                alert('New password must be at least 8 characters long.');
                e.preventDefault();
                return false;
            }

            if (newPassword !== confirmPassword) {
                alert('New passwords do not match.');
                e.preventDefault();
                return false;
            }

            return true;
        });
    </script>
</body>

</html>
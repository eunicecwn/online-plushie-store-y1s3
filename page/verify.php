<?php
require '../_base.php';

$_title = 'KAWAII.Verify Account';
include '../_head.php';

// Check if user came from signup process or email link
if (!isset($_SESSION['verify_member_id'])) {
    if (isset($_GET['member_id'])) {
        $_SESSION['verify_member_id'] = $_GET['member_id'];
    } else {
        header('Location: signup.php');
        exit;
    }
}


$memberID = $_SESSION['verify_member_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp)) {
        $error = "Please enter the verification code";
    } else {
        // Clean up expired tokens first
        $stmt = $_db->prepare("DELETE FROM token WHERE expire < NOW()");
        $stmt->execute();
        
        // Check if OTP is valid
        $stmt = $_db->prepare("
            SELECT * FROM token 
            WHERE id = ? AND memberID = ? AND type = 'account_activation'
        ");

        $stmt->execute([$otp, $memberID]);
        $token = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($token) {
            // Activate account
            $stmt = $_db->prepare("UPDATE member SET status = 'active' WHERE memberID = ?");
            $stmt->execute([$memberID]);
            
            // Delete the used token
            $stmt = $_db->prepare("DELETE FROM token WHERE id = ?");
            $stmt->execute([$otp]);
            
            // Clear session
            unset($_SESSION['verify_member_id']);
            
            // Show success message and redirect to login
            echo "<script>
                alert('Account verified successfully! You can now log in.');
                window.location.href = 'login.php';
            </script>";
            exit;
        } else {
            $error = "Invalid or expired verification code";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify Account</title>
    <link rel="stylesheet" href="/css/signup.css">
    <style>
        .verify-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .verify-container h2 {
            color: #e58f8f;
            margin-bottom: 20px;
        }
        .verify-container p {
            margin-bottom: 20px;
            color: #666;
        }
        .otp-input {
            width: 100%;
            max-width: 430px;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            letter-spacing: 5px;
            font-family: "Playpen Sans", serif;
        }
        .resend-link {
            display: block;
            margin-top: 15px;
            color: #e58f8f;
            text-decoration: none;
        }
        .resend-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <i class="fa-solid fa-envelope-circle-check" style="font-size: 50px; color: #e58f8f; margin-bottom: 20px;"></i>
        <h2>Verify Your Account</h2>
        <p>We've sent a 6-digit verification code to your email address. Please enter it below:</p>
        
        <?php if ($error): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="otp" class="otp-input" placeholder="Enter 6-digit code" maxlength="6" required>
            <button type="submit" style="width: 100%; max-width:450px; padding: 10px; background-color: #e58f8f; color: white; border: none; border-radius: 5px; cursor: pointer;">Verify Account</button>
        </form>
        
        <a href="#" class="resend-link" onclick="resendOTP()">Didn't receive code? Resend</a>
    </div>

    <script>
        function resendOTP() {
            fetch('resendOTP.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'member_id=<?= $memberID ?>'
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('New verification code has been sent to your email!');
                } else {
                    alert('Failed to resend code. Please try again.');
                }

            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
        }
    </script>
</body>
</html>
<?php
require '../_base.php';

$_title = 'KAWAII.User';
include '../_head.php';

$error = "";
$_err = [];

if (is_post()) {
    $email = req('memberEmail');
    $password = req('memberPassword');

    if (empty($email)) {
        $_err['memberEmail'] = 'Please enter a email.';
    } else if (!is_email($email)) {
        $_err['memberEmail'] = 'Invalid email format! Please try again.';
    }

    if (empty($password)) {
        $_err['memberPassword'] = 'Please enter a password.';
    } else if (strlen($password) < 8) {
        $_err['memberPassword'] = 'Password must be at least 8 characters long.';
    }

    if (!$_err) {
        $hashedPassword = sha1($password);

        // Check admin table
        $stmt = $_db->prepare("
        SELECT adminID, adminEmail, adminName, profilePhoto, 'Admin' AS role 
        FROM admin 
        WHERE adminEmail = ? AND adminPassword = ?
    ");

        $stmt->execute([$email, $hashedPassword]);
        $user = $stmt->fetch();

        // If not admin, check staff table
        if (!$user) {
            $stmt = $_db->prepare("
                SELECT *, 'Staff' AS role 
                FROM staff 
                WHERE staffEmail = ? AND staffPassword = ? AND status = 'active'
            ");
            $stmt->execute([$email, $hashedPassword]);
            $user = $stmt->fetch();
        }

        // If not admin or staff, check member table
        if (!$user) {
            $stmt = $_db->prepare("
                SELECT *, 'Member' AS role 
                FROM member 
                WHERE memberEmail = ? AND memberPassword = ? AND status = 'active'
            ");
            $stmt->execute([$email, $hashedPassword]);
            $user = $stmt->fetch();
        }

        if ($user) {
            // Create standardized user object
            $userObj = (object)[
                'id' => $user->adminID ?? $user->staffID ?? $user->memberID,
                'email' => $user->adminEmail ?? $user->staffEmail ?? $user->memberEmail,
                'name' => $user->adminName ?? $user->staffName ?? $user->memberName ?? 'User',
                'role' => $user->role,
                'profilePhoto' => $user->profilePhoto ?? null // Fetch profile photo
            ];

            // Login the user and redirect based on role
            if ($userObj->role === ROLE_ADMIN || $userObj->role === ROLE_STAFF) {
                login($userObj, '../adminpage/dashboard.php');
            } elseif ($userObj->role === ROLE_MEMBER) {
                login($userObj, '../index.php');
            } else {
                login($userObj, '../index.php');
            }
        } else {
            $error = "Oops! Your email or password didn't match our records. Please try again.";
        }
    }
}
?>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="form-container-login">
        <i class="fa-regular fa-circle-user user-icon"></i>
        <h1>Log In</h1>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>


        <form method="POST">
            <div class="form-group">
                <label for="memberEmail">Email</label>
                <input type="email" name="memberEmail" id="memberEmail" placeholder="Enter your email" class="form-input" required
                    value="<?= htmlspecialchars(req('memberEmail')) ?>">
                <?= err('memberEmail') ?>
            </div>


            <div class="form-group">
                <label for="memberPassword">Password</label>
                <input type="password" name="memberPassword" id="memberPassword"
                    placeholder="Enter your password (min 8 characters)" class="form-input" required minlength="8">
                <?= err('memberPassword') ?>
            </div>

            <p class="forgot-password-link">
                <a href="#" onclick="forgetPassword()" style="text-align:left">Forgot your password?</a>
            </p>
            <button type="submit" class="form-button">Login</button>
        </form>

        <p>Don't have an account? <a href="signup.php" class="link-signup-login">Sign up here</a></p>

    </div>


    <script>
        function forgetPassword() {
            const email = document.getElementById('memberEmail').value.trim();
            if (!email) {
                alert('Please enter your email first.');
                return;
            }

            fetch('forgetPassword.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'email=' + encodeURIComponent(email)
                })
                .then(response => response.text())
                .then(data => {
                    data = data.trim();

                    switch (data) {
                        case 'email_sent':
                            alert('✅ Password reset link has been sent to your email.');
                            break;
                        case 'email_not_found':
                            alert('❌ Email not found. Please check and try again.');
                            break;
                        case 'email_invalid':
                            alert('❌ Invalid email format.');
                            break;
                        default:
                            alert('❌ Something went wrong. Please try again.');
                            break;
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
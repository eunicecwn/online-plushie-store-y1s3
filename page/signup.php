<?php
require '../_base.php';

$_title = 'KAWAII.User';
include '../_head.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberName = trim($_POST['memberName']);
    $memberEmail = trim($_POST['memberEmail']);
    $memberPassword = $_POST['memberPassword'];
    $memberAddress = trim($_POST['memberAddress']);
    $confirmPassword = $_POST['confirmPassword'];
    $phoneNumber = trim($_POST['phoneNumber']);

    // Initialize error array
    $errors = [];

    // Password validation
    if (strlen($memberPassword) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    }

    if ($memberPassword !== $confirmPassword) {
        $errors['confirm'] = "Passwords do not match";
    }

    // Handle profile photo upload
    $profilePhoto = 'default.png'; // Default profile image
    if (!empty($_FILES['profilePhoto']['name'])) {
        $uploadDir = '../profile/';
        $fileName = time() . '_' . basename($_FILES['profilePhoto']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadFile)) {
            $profilePhoto = $fileName;
        }
    }

    // Check if email already exists
    $stmt = $_db->prepare("SELECT COUNT(*) FROM member WHERE memberEmail = ?");
    $stmt->execute([$memberEmail]);
    if ($stmt->fetchColumn() > 0) {
        $errors['email'] = "Email already registered. Please log in.";
    }

    // Only proceed if no errors
    if (empty($errors)) {

        // Hash the password
        $hashedPassword = sha1($memberPassword);

        // Insert into database
        $stmt = $_db->prepare("INSERT INTO member (memberName, memberEmail, memberPassword, phoneNumber, memberAddress, profilePhoto, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");

        if ($stmt->execute([$memberName, $memberEmail, $hashedPassword, $phoneNumber, $memberAddress, $profilePhoto])) {
            // Get the auto-generated memberID
            $newMemberID = $_db->lastInsertId();

            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            $expire = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP valid for 10 minutes

            // Store OTP in token table
            $stmt = $_db->prepare("INSERT INTO token (id, expire, memberID, type) VALUES (?, ?, ?, 'account_activation')");
            $stmt->execute([$otp, $expire, $newMemberID]);

            // Send OTP email
            $m = get_mail();
            try {
                $m->addAddress($memberEmail, $memberName);
                $m->Subject = "KAWAII Account Verification Code 🎀";
                $m->Body = "
                <h2>Welcome to KAWAII!</h2>
                <p>Thank you for signing up, <strong>$memberName</strong>!</p>
                <p>Your verification code is:</p>
                <div style='
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #e58f8f;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    font-size: 24px;
                    letter-spacing: 3px;
                '>$otp</div>
                <p>This code will expire in 10 minutes.</p>
                <p>If you didn't request this code, please ignore this email.</p>
                <p>To verify your account, please <a href='http://localhost:8000/page/verify.php?member_id=$memberID'>click here</a> and enter your code.</p>
            ";
                $m->isHTML(true);
                $m->send();

                // Store memberID in session for verification
                $_SESSION['verify_member_id'] = $newMemberID;

                // Show success popup and redirect to verification page
                echo "<script>
                alert('Sign up successful! Please check your email for the verification code to activate your account.');
                window.location.href = 'verify.php';
            </script>";
                exit;
            } catch (Exception $e) {
                $errors['email'] = "Failed to send verification email. Please contact admin.";
            }
        } else {
            $errors['database'] = "Error in sign up. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <div class="form-container-signup">
        <!-- Profile Photo Section -->
        <div class="profile-photo-section">
            <label for="profilePhoto">
                <img id="profilePreview" src="/profile/default.png">
            </label>
            <button type="button" class="choose-photo-btn" onclick="document.getElementById('profilePhoto').click();">
                Choose Profile Picture
            </button>
        </div>

        <h1>Sign Up As a New Member!</h1>

        <?php if (!empty($errors)) : ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="form-content-scroll">
            <form action="signup.php" method="POST" enctype="multipart/form-data">

                <input type="file" name="profilePhoto" id="profilePhoto" accept="image/*" onchange="previewImage(event)">

                <div class="form-group">
                    <label for="memberName">Full Name</label>
                    <input type="text" name="memberName" id="memberName" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="memberEmail">Email</label>
                    <input type="email" name="memberEmail" id="memberEmail" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="memberAddress">Address</label>
                    <textarea name="memberAddress" id="memberAddress" placeholder="Enter your address" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="memberPassword">Password</label>
                    <input type="password" name="memberPassword" id="memberPassword" placeholder="Enter your password (min 8 characters)"
                        minlength="8" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password"
                        minlength="8" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" name="phoneNumber" id="phoneNumber" placeholder="Enter your phone number" required>
                </div>

                <button type="submit" class="form-button">Sign Up</button>
            </form>

            <p>Already have an account? <a href="login.php" class="link-signup-login">Login here</a></p>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('profilePreview').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        //password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('memberPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
                return false;
            }

            if (password !== confirm) {
                alert('Passwords do not match');
                e.preventDefault();
                return false;
            }

            return true;
        });
    </script>
</body>

</html>
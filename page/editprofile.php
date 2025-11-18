<?php
ob_start(); // Start output buffering
require '../_base.php';

// Ensure user is logged in
auth();

// Set title and include appropriate header file
$_title = 'Edit Profile';
if ($_user->role === 'Admin' || $_user->role === 'Staff') {
    include '../_headadmin.php';
} else {
    include '../_head.php';
}

// Determine which table to query based on user role
if ($_user->role === 'Admin') {
    $table = 'admin';
    $idField = 'adminID';
    $nameField = 'adminName';
    $emailField = 'adminEmail';
    $passwordField = 'adminPassword';
    $hasPhone = false;
} elseif ($_user->role === 'Staff') {
    $table = 'staff';
    $idField = 'staffID';
    $nameField = 'staffName';
    $emailField = 'staffEmail';
    $passwordField = 'staffPassword';
    $hasPhone = true;
    $phoneField = 'phoneNumber';
} else {
    $table = 'member';
    $idField = 'memberID';
    $nameField = 'memberName';
    $emailField = 'memberEmail';
    $passwordField = 'memberPassword';
    $hasPhone = true;
    $phoneField = 'phoneNumber';
}

// Get current user's details
$stmt = $_db->prepare("SELECT * FROM $table WHERE $idField = ?");
$stmt->execute([$_user->id]);
$user = $stmt->fetch();

if (!$user) {
    die('<div class="error-message">User profile not found in ' . $table . ' table</div>');
}

$errors = [];
$success = false;
$passwordUpdated = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get submitted form data
    $name = trim($_POST['memberName']);
    $email = trim($_POST['memberEmail']);
    $phone = $hasPhone ? trim($_POST['phoneNumber']) : null;
    $profilePhoto = $user->profilePhoto ?? null;
    $address = ($_user->role === 'Member') ? trim($_POST['memberAddress']) : null;

    // Check for changes
    $hasChanges = false;
    if ($name !== $user->$nameField) $hasChanges = true;
    if ($email !== $user->$emailField) $hasChanges = true;
    if ($hasPhone && $phone !== $user->$phoneField) $hasChanges = true;
    if ($_user->role === 'Member' && $address !== $user->memberAddress) $hasChanges = true;

    // Check if the profile photo is updated
    if (!empty($_FILES['profilePhoto']['name'])) {
        $uploadDir = '../profile/';
        $fileName = time() . '_' . basename($_FILES['profilePhoto']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadFile)) {
            if ($profilePhoto && $profilePhoto !== 'default.png') {
                @unlink($uploadDir . $profilePhoto);
            }
            $profilePhoto = $fileName;
            $hasChanges = true;
        }
    }

    // If there are no changes, show a message and return early
    if (!$hasChanges && empty($_POST['currentPassword']) && empty($_POST['newPassword']) && empty($_POST['confirmPassword'])) {
        echo "<script>alert('No changes were made.'); window.location.href='myprofile.php';</script>";
        exit;
    }

    // Validate basic inputs
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Check if email already used
    $stmt = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $emailField = ? AND $idField != ?");
    $stmt->execute([$email, $_user->id]);
    if ($stmt->fetchColumn() > 0) {
        $errors['email'] = "Email already in use by another account";
    }

    // Handle password change if filled
    if (!empty($_POST['currentPassword']) || !empty($_POST['newPassword']) || !empty($_POST['confirmPassword'])) {
        $currentPassword = trim($_POST['currentPassword']);
        $newPassword = trim($_POST['newPassword']);
        $confirmPassword = trim($_POST['confirmPassword']);

        // Verify current password
        $stmt = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $idField = ? AND $passwordField = SHA1(?)");
        $stmt->execute([$_user->id, $currentPassword]);
        if ($stmt->fetchColumn() === 0) {
            $errors['currentPassword'] = "Current password is incorrect";
        }

        if (strlen($newPassword) < 8) {
            $errors['newPassword'] = "New password must be at least 8 characters long";
        }

        if ($newPassword !== $confirmPassword) {
            $errors['confirmPassword'] = "Passwords do not match";
        }

        if (empty($errors['currentPassword']) && empty($errors['newPassword']) && empty($errors['confirmPassword'])) {
            $passwordUpdated = true;
        }
    }

    if (empty($errors)) {
        // Build update query
        $updateFields = [
            "$nameField = ?",
            "$emailField = ?",
            "profilePhoto = ?"
        ];
        $params = [$name, $email, $profilePhoto];

        if ($hasPhone) {
            $updateFields[] = "$phoneField = ?";
            $params[] = $phone;
        }

        if ($passwordUpdated) {
            $updateFields[] = "$passwordField = SHA1(?)";
            $params[] = $newPassword;
        }

        if ($_user->role === 'Member') {
            $updateFields[] = "memberAddress = ?";
            $params[] = $address;
        }

        $params[] = $_user->id;

        $query = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE $idField = ?";
        $stmt = $_db->prepare($query);

        if ($stmt->execute($params)) {
            // Update session with all new data
            $_user->name = $name;
            $_user->email = $email;
            $_user->memberAddress = $address;
            $_user->profilePhoto = $profilePhoto; // Add this line to update the photo in session
            $_SESSION['_user'] = $_user;

            temp('success', 'Profile updated successfully' . ($passwordUpdated ? ' and password changed' : ''));
            redirect('myprofile.php');
        } else {
            $errors['database'] = "Error updating profile. Please try again.";
        }
    }
}

// Set CSS file
$cssFile = ($_user->role === ROLE_ADMIN || $_user->role === ROLE_STAFF)
    ? '/css/profileadmin.css'
    : '/css/profileMember.css';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="<?= $cssFile ?>">
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <h2>Edit Profile</h2>
            <a href="myprofile.php" class="back-btn">Back to Profile</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="editprofile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group photo-section">
                <div class="form-group">
                    <label for="profilePhoto">Profile Photo</label>
                    <div class="photo-preview">
                        <img id="profilePreview" src="/profile/<?= encode($user->profilePhoto ?? 'default.png') ?>">
                        <input type="file" name="profilePhoto" id="profilePhoto" accept="image/*" onchange="previewImage(event)">
                        <label for="profilePhoto" class="upload-btn">Choose Photo</label>
                    </div>
                </div>
            </div>

            <div class="form-scroll">
                <div class="form-group">
                    <label for="memberName">Full Name</label>
                    <input type="text" name="memberName" id="memberName"
                        value="<?= encode($user->$nameField) ?>" required>
                </div>

                <div class="form-group">
                    <label for="memberEmail">Email</label>
                    <input type="email" name="memberEmail" id="memberEmail"
                        value="<?= encode($user->$emailField) ?>" required>
                </div>

                <?php if ($hasPhone): ?>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" name="phoneNumber" id="phoneNumber"
                            value="<?= encode($user->$phoneField) ?>" required>
                    </div>
                <?php endif; ?>

                <?php if ($_user->role === 'Member'): ?>
                    <div class="form-group">
                        <label for="memberAddress">Address</label>
                        <textarea name="memberAddress" id="memberAddress" rows="4" required><?= encode($user->memberAddress ?? '') ?></textarea>
                    </div>
                <?php endif; ?>

                <div class="password-change-section">
                    <h3>Change Password</h3>

                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" name="currentPassword" id="currentPassword"
                            placeholder="Enter your current password" autocomplete="new-password">

                    </div>

                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" name="newPassword" id="newPassword"
                            placeholder="Enter new password (min 8 characters)" minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" name="confirmPassword" id="confirmPassword"
                            placeholder="Confirm your new password" minlength="8">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="myprofile.php" class="cancel-btn">Cancel</a>
                </div>
        </form>
    </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('profilePreview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const currentPass = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;

            if (currentPass || newPass || confirmPass) {
                if (!currentPass) {
                    alert('Please enter your current password');
                    e.preventDefault();
                    return false;
                }
                if (newPass.length < 8) {
                    alert('New password must be at least 8 characters long');
                    e.preventDefault();
                    return false;
                }
                if (newPass !== confirmPass) {
                    alert('New passwords do not match');
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        });
    </script>
</body>

</html>
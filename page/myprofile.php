<?php
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

// Get current user's details from the correct table (admin/staff/member)
// First determine which table to query based on user role
if ($_user->role === 'Admin') {
    $table = 'admin';
    $idField = 'adminID';
    $nameField = 'adminName';
    $emailField = 'adminEmail';
} elseif ($_user->role === 'Staff') {
    $table = 'staff';
    $idField = 'staffID';
    $nameField = 'staffName';
    $emailField = 'staffEmail';
} else {
    $table = 'member';
    $idField = 'memberID';
    $nameField = 'memberName';
    $emailField = 'memberEmail';
}

$stmt = $_db->prepare("SELECT * FROM $table WHERE $idField = ?");
$stmt->execute([$_user->id]);
$user = $stmt->fetch();

// If user not found, show error and exit
if (!$user) {
    die('<div class="error-message">User profile not found in '.$table.' table</div>');
}

// Determine which CSS file to load based on user role
$cssFile = ($_user->role === ROLE_ADMIN || $_user->role === ROLE_STAFF) 
    ? '/css/profileadmin.css' 
    : '/css/profileMember.css';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= $cssFile ?>">
</head>
<body>

<?php 
$msg = temp('success'); 
if ($msg):
?>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        alert('<?= addslashes($msg) ?>');
    });
</script>
<?php endif; ?>
    
    <div class="myprofile-container">
        <div class="profile-header">
            <h2>My Profile</h2>
        </div>
        <div class="profile-photo">
            <?php if (empty($user->profilePhoto)): ?>
                <div class="profile-icon">
                    <i class="fa-regular fa-circle-user"></i>
                </div>
            <?php else: ?>
                <img src="../profile/<?= encode($user->profilePhoto) ?>">
            <?php endif; ?>
        </div>

            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?= encode($user->$nameField) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ID:</span>
                    <span class="detail-value"><?= encode($user->$idField) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= encode($user->$emailField) ?></span>
                </div>
                <?php if ($table === 'member'): ?>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value"><?= encode($user->phoneNumber) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><?= ucfirst($user->status) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value"><?= nl2br(encode($user->memberAddress)) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($_user->role !== 'Staff'): ?>
                <div class="profile-actions">
                    <a href="editprofile.php" class="edit-profile-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>
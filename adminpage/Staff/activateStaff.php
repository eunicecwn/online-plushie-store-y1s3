<?php
require '../../_base.php'; // Database connection

if (is_get()) {
    $staffID = req('id');

    $stm = $_db->prepare('SELECT * FROM staff WHERE staffID = ? AND status = "inactive"');
    $stm->execute([$staffID]);
    $staff = $stm->fetch();

    if (!$staff) {
        temp('error', 'Staff not found or already active');
        redirect('staffTable.php');
    }

    extract((array)$staff);
}

// Handle activation request
if (is_post()) {
    $staffID = req('id');

    $stmt = $_db->prepare("UPDATE staff SET status = 'active' WHERE staffID = ?");
    $stmt->execute([$staffID]);

    temp('info', 'Staff activated successfully');
    redirect('staffTable.php');
}

$_title = 'KAWAII.SellerCentre | Activate Staff';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Activate Staff</h1>

    <p>Are you sure you want to activate the staff <strong><?= $staffName ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $staffID ?>">
        
        <button type="submit" class="form-button" data-confirm="Are you sure you want to activate this staff?">Activate</button>
        <a href="staffTable.php" class="form-button">Cancel</a>
    </form>
</div>
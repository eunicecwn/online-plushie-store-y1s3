<?php
require '../../_base.php'; // Database connection

if (is_get()) {
    $memberID = req('id');

    $stm = $_db->prepare('SELECT * FROM member WHERE memberID = ? AND status = "inactive"');
    $stm->execute([$memberID]);
    $member = $stm->fetch();

    if (!$member) {
        temp('error', 'Member not found or already active');
        redirect('memberTable.php');
    }

    extract((array)$member);
}

// Handle unblock request
if (is_post()) {
    $memberID = req('id');

    $stmt = $_db->prepare("UPDATE member SET status = 'active' WHERE memberID = ?");
    $stmt->execute([$memberID]);

    temp('info', 'Member unblocked successfully');
    redirect('memberTable.php');
}

$_title = 'KAWAII.SellerCentre | Unblock Member';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Unblock Member</h1>

    <p>Are you sure you want to unblock the member <strong><?= $memberName ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $memberID ?>">
        
        <button type="submit" class="form-button" data-confirm="Are you sure you want to unblock this member?">Unblock</button>
        <a href="memberTable.php" class="form-button">Cancel</a>
    </form>
</div>
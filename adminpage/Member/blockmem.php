<?php
require '../../_base.php'; // Database connection

if (is_get()) {
    $memberID = req('id');

    $stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
    $stm->execute([$memberID]);
    $member = $stm->fetch();

    if (!$member) {
        temp('error', 'Member not found');
        redirect('memberTable.php');
    }

    extract((array)$member);
}

// Handle block request
if (is_post()) {
    $memberID = req('id');

    $stmt = $_db->prepare("UPDATE member SET status = 'inactive' WHERE memberID = ?");
    $stmt->execute([$memberID]);

    temp('info', 'Member blocked successfully');
    redirect('memberTable.php');
}

$_title = 'KAWAII.SellerCentre | Block Member';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Block Member</h1>

    <p>Are you sure you want to block the member <strong><?= $memberName ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $memberID ?>">
        
        <button type="submit" class="form-button" data-confirm="Are you sure you want to block this member?">Block</button>
        <a href="memberTable.php" class="form-button">Cancel</a>
    </form>
</div>


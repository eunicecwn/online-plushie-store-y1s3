<?php
require '../../_base.php';

// GET request — show confirmation form
if (is_get()) {
    $voucherID = req('voucherID'); // GET param is 'id'

    $stm = $_db->prepare('SELECT * FROM voucher WHERE voucherID = ?');
    $stm->execute([$voucherID]);
    $voucher = $stm->fetch();

    if (!$voucher) {
        temp('error', 'Voucher not found');
        redirect('/adminpage/Voucher/myVoucher.php');
    }

    extract((array)$voucher);
}

// POST request — deactivate voucher
if (is_post()) {
    $voucherID = req('voucherID');

    $stmt = $_db->prepare("UPDATE voucher SET status = 'Disabled' WHERE voucherID = ?");
    $stmt->execute([$voucherID]);

    temp('info', 'Voucher disabled successfully');
    redirect('/adminpage/Voucher/myVoucher.php');
}

$_title = 'KAWAII.SellerCentre | Disable Voucher';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Disable Voucher</h1>

    <p>Are you sure you want to disable the voucher <strong><?= htmlspecialchars($voucherCode) ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="voucherID" value="<?= $voucherID ?>">

        <button type="submit" class="form-button" data-confirm="Are you sure you want to disable this voucher?">Disable</button>
        <a href="/adminpage/Voucher/myVoucher.php" class="form-button">Cancel</a>
    </form>
</div>

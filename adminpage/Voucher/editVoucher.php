<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

if (is_get()) {  // Display existing data
    $voucherID = req('voucherID'); // Get ID from URL (updateVoucher.php?voucherID=1)

    $stm = $_db->prepare('SELECT * FROM voucher WHERE voucherID = ? LIMIT 1');
    $stm->execute([$voucherID]);
    $voucher = $stm->fetch();

    if (!$voucher) {
        temp('error', 'Voucher not found');
        redirect('../../_headadmin.php');
    }

    extract((array)$voucher);
}

if (is_post()) {
    $voucherID = req('voucherID');
    $voucherCode = strtoupper(trim(req('voucherCode')));
    $discountAmount = req('discountAmount');
    $usageLimit = req('usageLimit');
    $expiryDate = req('expiryDate');

    // Validation: Voucher Code
    if (empty($voucherCode)) {
        $_err['voucherCode'] = 'Please enter a voucher code.';
    } else if (strlen($voucherCode) != 5) {
        $_err['voucherCode'] = 'Voucher code must be exactly 5 characters.';
    } else if (!preg_match('/^[A-Z0-9]+$/', $voucherCode)) {
        $_err['voucherCode'] = 'Voucher code can only contain uppercase letters and numbers.';
    } else {
        // Check uniqueness for voucherCode excluding the current voucher
        $isUniqueQuery = $_db->prepare("SELECT COUNT(*) FROM voucher WHERE voucherCode = ? AND voucherID != ?");
        $isUniqueQuery->execute([$voucherCode, $voucherID]);
        $isUnique = $isUniqueQuery->fetchColumn();
        if ($isUnique > 0) {
            $_err['voucherCode'] = 'This voucher code already exists.';
        }
    }

    // Validation: Discount Amount
    if (empty($discountAmount)) {
        $_err['discountAmount'] = 'Please enter a discount amount.';
    } else if (!is_numeric($discountAmount) || $discountAmount <= 0) {
        $_err['discountAmount'] = 'Discount amount must be a positive number.';
    } else if ($discountAmount > 1000) {
        $_err['discountAmount'] = 'Maximum discount amount is RM1000.';
    }

    // Validation: Usage Limit
    if (!empty($usageLimit) && (!is_numeric($usageLimit) || $usageLimit < 1)) {
        $_err['usageLimit'] = 'Usage limit must be at least 1.';
    }

    // Validation: Expiry Date
    if (!empty($expiryDate)) {
        $expiryDateTime = new DateTime($expiryDate);
        $currentDate = new DateTime();
        if ($expiryDateTime < $currentDate) {
            $_err['expiryDate'] = 'Expiry date cannot be in the past.';
        }
    }

    // If no errors, insert into database
    if (empty($_err)) {
        $stmt = $_db->prepare("UPDATE voucher SET voucherCode = ?, discountAmount = ?, usageLimit = ?, expiryDate = ? WHERE voucherID = ?");
        $result = $stmt->execute([
            $voucherCode,
            $discountAmount,
            $usageLimit ?: 1, // Default to 1 if empty
            $expiryDate ?: null,
            $voucherID
        ]);
        
        if ($result) {
            temp('info', 'Voucher updated successfully!');
            redirect("/adminpage/Voucher/myVoucher.php");
        } else {
            $_err[] = 'Voucher update failed. Please try again.';
        }
        
    }
}

$_title = 'KAWAII.SellerCentre | Update Voucher';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Update Voucher</h1>
    <form action="" method="POST" novalidate>
        

        <!-- Voucher Code -->
        <div class="form-group">
            <label for="voucherCode">Voucher Code</label>
            <?= html_text('voucherCode', 'placeholder="e.g., DIS20" class="form-input" maxlength="5" pattern="[A-Z0-9]{5}" required data-upper') ?>
            <?= err('voucherCode') ?>
        </div>

        <!-- Discount Amount -->
        <div class="form-group">
            <label for="discountAmount">Discount Amount (RM)</label>
            <?= html_number('discountAmount', 0.01, 1000, 0.01, 'class="form-input" placeholder="e.g., 20.00" required') ?>
            <?= err('discountAmount') ?>
        </div>

        <!-- Usage Limit -->
        <div class="form-group">
            <label for="usageLimit">Usage Limit</label>
            <input type="number" name="usageLimit" id="usageLimit"
                   value="<?= !empty($_POST['usageLimit']) ? htmlspecialchars($_POST['usageLimit']) : '1' ?>"
                   min="1" class="form-input">
            <?= err('usageLimit') ?>
        </div>

        <!-- Expiry Date -->
        <div class="form-group">
            <label for="expiryDate">Expiry Date(Leave blank for no expiry)</label>
            <input type="datetime-local" name="expiryDate" id="expiryDate"
                   value="<?= !empty($_POST['expiryDate']) ? htmlspecialchars($_POST['expiryDate']) : '' ?>"
                   class="form-input">
            <?= err('expiryDate') ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="form-button primary">Update Voucher</button>
            <button type="reset" class="form-button">Reset</button>
            <a href="/adminpage/Voucher/myVoucher.php" data-confirm="Discard changes?" class="form-button">Cancel</a>
        </div>
    </form>
</div>

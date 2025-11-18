<?php
require '../../_base.php';

if (is_get()) {
    $productID = req('productID');

    $stm = $_db->prepare('SELECT * FROM product WHERE productID = ?');
    $stm->execute([$productID]);
    $product = $stm->fetch();

    if (!$product) {
        temp('error', 'Product not found');
        redirect('../../headadmin.php');
    }

    extract((array)$product);
}

// Handle deactivation request
if (is_post()) {
    $productID = req('productID');

    $stmt = $_db->prepare("UPDATE product SET status = 'Delisted' WHERE productID = ?");
    $stmt->execute([$productID]);

    temp('info', 'Product delisted successfully');
    redirect('/adminpage/Product/myProduct.php');
}

$_title = 'KAWAII.SellerCentre | Deactivate Product';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Deactivate Product</h1>

    <p>Are you sure you want to delist the product <strong><?= $productName ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="productID" value="<?= $productID ?>">
        
        <button type="submit" class="form-button" data-confirm="Are you sure you want to delist this product?">Delisted</button>
        <a href="/adminpage/Product/myProduct.php" data-confirm="Are you sure you want to discard changes?" class="form-button">Cancel</a>
    </form>
</div>
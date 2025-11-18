<?php
require '../../_base.php';

if (is_get()) {
    $categoryID = req('categoryID');

    $stm = $_db->prepare('SELECT * FROM category WHERE categoryID = ?');
    $stm->execute([$categoryID]);
    $category = $stm->fetch();

    if (!$category) {
        temp('error', 'Category not found');
        redirect('../../headadmin.php');
    }

    extract((array)$category);
}

// Handle deactivation request
if (is_post()) {
    $categoryID = req('categoryID');

    $stmt = $_db->prepare("UPDATE category SET status = 'inactive' WHERE categoryID = ?");
    $stmt->execute([$categoryID]);

    temp('info', 'Category deactivated successfully');
    redirect('/adminpage/Category/myCategory.php');
}

$_title = 'KAWAII.SellerCentre | Deactivate Category';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Deactivate Category</h1>

    <p>Are you sure you want to deactivate the category <strong><?= $categoryName ?></strong>?</p>

    <form action="" method="POST">
        <input type="hidden" name="categoryID" value="<?= $categoryID ?>">
        
        <button type="submit" class="form-button" data-confirm="Are you sure you want to deactivate this category?">Deactivate</button>
        <a href="/adminpage/Category/myCategory.php" data-confirm="Are you sure you want to discard changes?" class="form-button">Cancel</a>
    </form>
</div>

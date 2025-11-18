<?php
// Secure database connection
require '../../_base.php';

// Initialize error messages
$_err = [];

// Handle form submission
if (is_post()) {
    $categoryID   = req('categoryID');
    $categoryName = req('categoryName');
    $categoryType = req('categoryType');

    // Validation : Category ID
    if ($categoryID == '') {
        $_err['categoryID'] = 'Please enter a category ID.';
    } else if (!ctype_digit($categoryID) || strlen($categoryID) !== 5) {
        $_err['categoryID'] = 'The category ID must be exactly 5 digits. Please check your entry.';
    } else if (!is_unique($categoryID, 'category', 'categoryID')) {
        $_err['categoryID'] = 'This category ID is already in use. Please choose a unique ID.';
    }

    // Validation : Category Name
    if ($categoryName == '') {
        $_err['categoryName'] = 'Please enter a category name.';
    } else if (strlen($categoryName) > 50) {
        $_err['categoryName'] = 'The category name should not exceed 50 characters. Please shorten it.';
    } else if (is_exists($categoryName, 'category', 'categoryName')) {
        $_err['categoryName'] = 'This category name already exists. Please choose another name.';
    }

    // Validation : Category Type
    if ($categoryType == '') {
        $_err['categoryType'] = 'Please select a category type.';
    }

    // If no errors, insert into database
    if (empty($_err)) {
        try {

            $stmt = $_db->prepare("INSERT INTO category (categoryID, categoryName, categoryType) VALUES (?, ?, ?)");
            $stmt->execute([$categoryID, $categoryName, $categoryType]);

            temp('info', 'Category Added');

            redirect("/adminpage/Category/myCategory.php");
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$_title = 'KAWAII.SellerCentre | Add Category';
include '../../_headadmin.php';
?>
<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Add a New Category</h1>
    <form action="" method="POST">

        <div class="form-group">
            <label for="categoryID">Category ID</label>
            <?= html_text('categoryID', 'placeholder="Enter Category ID (Exp:10001)" class="form-input" required') ?>
            <?= err('categoryID') ?>
        </div>

        <div class="form-group">
            <label for="categoryName">Category Name</label>
            <?= html_text('categoryName', 'placeholder="Enter Category Name (Exp:Bunny)" class="form-input" maxlength = "50"  required data-upper') ?>
            <?= err('categoryName') ?>
        </div>

        <div class="form-group">
            <label for="categoryType">Category Type</label>
            <?= html_select('categoryType', $_categoryType, 'Select a category', 'class="form-select" required') ?>
            <?= err('categoryType') ?>
        </div>

        <button type="submit" class="form-button">Create Category</button>
        <button type="reset" class="form-button">Reset</button>
        <a href="/adminpage/Category/myCategory.php" data-confirm="Discard changes?" class="form-button">Cancel</a>
    </form>
</div>
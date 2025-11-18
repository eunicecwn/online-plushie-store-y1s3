<?php
require '../../_base.php';

$categories = [
    ["name" => "Animals", "image" => "/images/Animals.png", "type" => "Animals"],
    ["name" => "Best for Gift", "image" => "/images/Bestforgift.png", "type" => "Best for Gift"],
    ["name" => "Flowers", "image" => "/images/Flower.png", "type" => "Flowers"],
    ["name" => "Food & Drinks", "image" => "/images/foodDrink.png", "type" => "Food & Drinks"],
    ["name" => "Ocean & Sea Life", "image" => "/images/OceanSeaLife.png", "type" => "Ocean & Sea Life"],
    ["name" => "Personalised", "image" => "/images/Personalised.png", "type" => "Personalised"]
];

// Page title
$_title = 'KAWAII.SellerCentre | My Category';
include '../../_headadmin.php';
?>

<!-- Link External CSS -->
<link rel="stylesheet" href="/css/admin.css">

<h1 class="category-title">Browse Categories</h1>

<!-- Category Grid -->
<div class="category-container">
    <?php foreach ($categories as $category): ?>
        <a href="categoryTable.php?type=<?= urlencode($category['type']) ?>" class="category-box">
            <img src="<?= $category['image'] ?>" alt="<?= encode($category['name']) ?>">
            <div class="category-name"><?= encode($category['name']) ?></div>
        </a>
    <?php endforeach; ?>
</div>


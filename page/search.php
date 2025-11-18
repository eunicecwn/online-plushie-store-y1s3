<?php
require '../_base.php';

$searchTerm = trim($_GET['search'] ?? '');
$_title = 'Search Results';
include '../_head.php';
?>
<link rel="stylesheet" href="/css/product.css">

<div class="search-header">
    <h2>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h2>
</div>

<div class="product-grid">
<?php
if (!empty($searchTerm)) {
    $sql = "
        SELECT p.*
        FROM product p
        LEFT JOIN product_category pc ON p.productID = pc.productID
        LEFT JOIN category c ON pc.categoryID = c.categoryID
        WHERE (p.productName LIKE :search OR p.description LIKE :search OR p.tags LIKE :search OR c.categoryName LIKE :search) AND
        p.status = 'available'
        GROUP BY p.productID
    ";
    $stm = $_db->prepare($sql);
    $stm->execute([':search' => '%' . $searchTerm . '%']);

    if ($stm->rowCount() > 0) {
        while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
            $imagePath = "/images/placeholder.png";

            $sqlImage = "SELECT imageName FROM gallery WHERE productID = :productID AND is_cover = 1 ORDER BY imageID ASC LIMIT 1";
            $stmImage = $_db->prepare($sqlImage);
            $stmImage->execute([':productID' => $row['productID']]);

            if ($imageRow = $stmImage->fetch(PDO::FETCH_ASSOC)) {
                $images = explode(',', $imageRow['imageName']);
                $firstImage = trim($images[0]);
                $imagePath = "../productImage/" . $firstImage;
            }
?>
            <div class="product" id="product-<?= htmlspecialchars($row['productID']) ?>">
                <a href="productdetails.php?id=<?= htmlspecialchars($row['productID']) ?>">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($row['productName']) ?>" onerror="this.src='/images/placeholder.png'">
                    </div>
                    <div class="product-details">
                        <h3 class="product-name"><?= htmlspecialchars($row['productName']) ?></h3>
                        <p class="product-price">RM<?= htmlspecialchars($row['price']) ?></p>
                    </div>
                </a>
                <div class="product-actions">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['productID']) ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
<?php
        }
    } else {
        echo '<p class="no-products">Oops! No Products Found</p>';
    }
} else {
    echo '<p class="no-products">Please enter a search term.</p>';
}
?>
</div>

<?php include '../_foot.php'; ?>

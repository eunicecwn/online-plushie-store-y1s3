<?php
require '_base.php';

// Fetch top 10 best-selling products based on order data
$sql = "SELECT p.*, SUM(oi.orderItemQuantity) AS totalSold
        FROM ordereditem oi
        JOIN product p ON oi.productID = p.productID
        GROUP BY oi.productID
        ORDER BY totalSold DESC
        LIMIT 10";
$stm = $_db->prepare($sql);
$stm->execute();
$topProducts = $stm->fetchAll();

$_title = 'KAWAII.Home';
include '_head.php';
?>

<link rel="stylesheet" href="/css/product.css">

<div class="container">
<div class="main-photo">
    <div class="main-photo-content">
        <h1>Welcome to KAWAII Plushies</h1>
        <p>Discover the cutest plushies you'll ever meet!</p>
        <a href="/page/category.php" class="main-photo-btn">Shop Now</a>
    </div>
</div>

<section class="category-section">
    <div class="category-container">
        <a href="/page/category.php?categoryType=Animals" class="category-item">
            <img src="/productImage/animals.webp" alt="Animals">
            <span class="index-category">Animals</span>
        </a>
        <a href="/page/category.php?categoryType=Best for Gift" class="category-item">
            <img src="/productImage/bestforgifts.jpg" alt="Best for Gifts">
            <span class="index-category">Best for Gifts</span>
        </a>
        <a href="/page/category.php?categoryType=Food%20%26%20Drinks" class="category-item">
            <img src="/productImage/foodanddrinks.jpeg" alt="Food & Drinks">
            <span class="index-category">Food & Drinks</span>
        </a>
        <a href="/page/category.php?categoryType=Flowers" class="category-item">
            <img src="/productImage/flowers.jpeg" alt="Flowers">
            <span class="index-category">Flowers</span>
        </a>
        <a href="/page/category.php?categoryType=Ocean%20%26%20Sea%20Life" class="category-item">
            <img src="/productImage/oceanandsealife.jpg" alt="Ocean and Sea Life">
            <span class="index-category">Ocean & Sea Life</span>
        </a>
    </div>
</section>

<div class="voucher-banner">
    <div class="voucher-image"></div>
    <div class="voucher-text">
        <h2>Voucher</h2>
        <p>A sweet deal just for you!</p>
    </div>
    <div class="voucher-details">
        <h3>Enjoy RM10 off!</h3>
        <p>Use code <strong>KAWAI</strong> at checkout. Valid for all purchases above RM50.</p>
        <p>Happy Shopping!</p>
    </div>
</div>

<section class="highlighted-categories">
    <div class="highlighted-category-container">
        <a href="/page/category.php?categoryType=Animals" class="highlighted-category-item">
            <div class="image-wrapper">
                <img src="/productImage/animals2.webp" alt="Animals">
                <span>Animals</span>
            </div>
        </a>
        <a href="/page/category.php?categoryType=Best for Gift" class="highlighted-category-item">
            <div class="image-wrapper">
                <img src="/productImage/bestforgifts2.jpg" alt="Ocean & Sea Life">
                <span>Best for Gifts</span>
            </div>
        </a>
        <a href="/page/category.php?categoryType=Food & Drinks" class="highlighted-category-item">
            <div class="image-wrapper">
                <img src="/productImage/foodanddrinks2.jpg" alt="Food & Drinks">
                <span>Food & Drinks</span>
            </div>
        </a>

    </div>
</section>

<section class="top-sales">
    <h2>Trending</h2>
    <div class="product-grid">
        <?php
        // Fetch top best-selling products by total quantity sold
        $sql = "SELECT 
                    p.productID,
                    p.productName,
                    p.price,
                    SUM(oi.orderItemQuantity) AS total_sold
                FROM product p
                JOIN ordereditem oi ON p.productID = oi.productID
                WHERE p.status = 'available'
                GROUP BY p.productID
                ORDER BY total_sold DESC
                LIMIT 8";
        $stm = $_db->prepare($sql);
        $stm->execute();
        $topProducts = $stm->fetchAll(PDO::FETCH_OBJ);

        foreach ($topProducts as $row):
            // Get cover image
            $imagePath = "/productImage/placeholder.png";
            $sqlImage = "SELECT imageName FROM gallery WHERE productID = :productID AND is_cover = 1 LIMIT 1";
            $stmImage = $_db->prepare($sqlImage);
            $stmImage->execute([':productID' => $row->productID]);
            if ($imageRow = $stmImage->fetch(PDO::FETCH_ASSOC)) {
                $firstImage = trim($imageRow['imageName']);
                $imagePath = "/productImage/" . $firstImage;
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                    $imagePath = "/productImage/placeholder.png";
                }
            }
        ?>
            <div class="product" id="product-<?= htmlspecialchars($row->productID) ?>">
                <a href="productdetails.php?id=<?= htmlspecialchars($row->productID) ?>">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($imagePath) ?>"
                            alt="<?= htmlspecialchars($row->productName) ?>"
                            onerror="this.src='/productImage/placeholder.png'">
                    </div>
                    <div class="product-details">
                        <h3 class="product-name"><?= htmlspecialchars($row->productName) ?></h3>
                        <p class="product-price">RM<?= number_format($row->price, 2) ?></p>
                    </div>
                </a>
                <div class="product-actions">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($row->productID) ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
        </div>
</section>

<?php
include '_foot.php';

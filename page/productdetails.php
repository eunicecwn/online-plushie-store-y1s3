<?php
require '../_base.php';

$current_url = $_SERVER['REQUEST_URI']; // This includes categoryType parameter

// Initialize wishlist if not set
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Get product ID from URL
$productID = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($productID)) {
    echo "<p>Invalid Product!</p>";
    exit;
}

// Fetch product data from the database
$sql = "SELECT * FROM product WHERE productID = ?";
$stm = $_db->prepare($sql);
$stm->execute([$productID]);
$product = $stm->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Product not found!</p>";
    exit;
}

// Fetch images from gallery table (order by cover image first, then by imageID)
$imageStm = $_db->prepare("SELECT imageName FROM gallery WHERE productID = ? ORDER BY is_cover DESC, imageID ASC");
$imageStm->execute([$productID]);
$imageRows = $imageStm->fetchAll(PDO::FETCH_ASSOC);

// Process each image (only one image name per row)
$imageURLs = [];
foreach ($imageRows as $row) {
    $cleanName = trim($row['imageName']);
    if (!empty($cleanName)) {
        $imagePath = "../productImage/" . $cleanName;
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . str_replace("..", "", $imagePath);

        if (file_exists($absolutePath)) {
            $imageURLs[] = $imagePath;
        }
    }
}

// Use placeholder if no images exist
$firstImage = !empty($imageURLs) ? $imageURLs[0] : "/images/placeholder.png";

// Handle adding to wishlist
if (isset($_POST['add_to_wishlist'])) {
    $productID = $_POST['product_id'];

    // Only for logged in users
    if (isset($_SESSION['_user'])) {
        $memberID = $_SESSION['_user']->id;

        // Insert into database
        $stmt = $_db->prepare("INSERT IGNORE INTO member_wishlist (memberID, productID) VALUES (?, ?)");
        $stmt->execute([$memberID, $productID]);
        temp('info', 'Added to wishlist successfully!');
    }

    // Still add to session (optional, for faster frontend display)
    if (!in_array($productID, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $productID;
        temp('info', 'Added to wishlist successfully!');
    }

    redirect('wishlist.php');
}

$_title = 'KAWAII.Product Details';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/product.css">

<div class="product-details-page">
    <div class="product-image-container">
        <?php if (count($imageURLs) > 1): ?>
            <button id="prev-image"><i class="fa-solid fa-circle-chevron-left"></i></button>
        <?php endif; ?>

        <img id="product-image" src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($product['productName']) ?>">

        <?php if (count($imageURLs) > 1): ?>
            <button id="next-image"><i class="fa-solid fa-circle-chevron-right"></i></button>
        <?php endif; ?>
    </div>

    <div class="product-info">
        <h2 class="product-name"><?= htmlspecialchars($product['productName']) ?></h2>
        <p class="product-price">RM<?= htmlspecialchars($product['price']) ?></p>

        <div class="product-actions">
            <?php if (isset($_user) && ($_user->role === ROLE_ADMIN || $_user->role === ROLE_MEMBER || $_user->role === ROLE_STAFF)): ?>
                <form method='POST' action='productdetails.php?id=<?= $productID ?>'>
                    <input type='hidden' name='product_id' value='<?= $productID ?>'>
                    <button type='submit' name='add_to_wishlist' class='add-to-wishlist'>
                        <i class='fa-regular fa-heart'></i> Add To Wishlist
                    </button>
                </form>

                <form method='POST' action='shoppingcart.php'>
                    <input type='hidden' name='product_id' value='<?= $productID ?>'>
                    <input type='hidden' name='quantity' value='1'>
                    <button type='submit' name='add_to_cart' class='add-to-cart'>
                        <i class='fa-solid fa-cart-shopping'></i> Add To Cart
                    </button>
                </form>

            <?php else: ?>
                <a href="/page/login.php?return_url=<?= urlencode($current_url) ?>" class="add-to-wishlist">
                    <i class='fa-regular fa-heart'></i> Add To Wishlist
                </a>

                <a href="/page/login.php?return_url=<?= urlencode($current_url) ?>" class="add-to-cart">
                    <i class='fa-solid fa-cart-shopping'></i> Add To Cart
                </a>
            <?php endif; ?>
        </div>

        <h2 style="color: #e58f8f">Product Details </h2>

        <h3>Description</h3>
        <p class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <h3>Weight</h3>
        <p class="product-description"><?= nl2br(htmlspecialchars($product['weight'])) ?> kg</p>
        <h3>Dimension</h3>
        <p class="product-description"><?= nl2br(htmlspecialchars($product['length'])) ?>cm (Length) x <?= nl2br(htmlspecialchars($product['width'])) ?> cm (Width) x <?= nl2br(htmlspecialchars($product['height'])) ?>cm (Height)</p>

        <h2 style="color: #e58f8f">Safety and Care</h2>
        <p class="product-description"> Care Instructions: </p>
        <p class="product-description"> - 30 degree Celsius wash only
        <p class="product-description"> - Do not tumble dry, dry clean or iron. Check all labels upon arrival of purchase </p>
        <p class="product-description"> Safety Recommendations : Suitable from birth </p>
        <p class="product-description"> Tested to and complies with EN71, ASTM, and ISO 8124</p>

    </div>
</div>

<?php include '../_foot.php'; ?>

<script>
    let images = <?= json_encode($imageURLs) ?>;
    let currentIndex = 0;

    if (images.length > 1) {
        document.getElementById("product-image").src = images[currentIndex];

        document.getElementById("next-image").addEventListener("click", function() {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById("product-image").src = images[currentIndex];
        });

        document.getElementById("prev-image").addEventListener("click", function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById("product-image").src = images[currentIndex];
        });
    }
</script>
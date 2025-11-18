<?php
require '../_base.php';
// At the top of your file (before any output)
$current_url = $_SERVER['REQUEST_URI']; // This includes categoryType parameter

$page = req('page', 1);

require_once '../lib/SimplePager.php';

// Get the categoryType filter
$categoryType = isset($_GET['categoryType']) ? $_GET['categoryType'] : null;

// Initialize query conditions
$whereConditions = [];
$params = [];

// Get categoryType filter
if ($categoryType) {
    $whereConditions[] = "c.categoryType = ?";
    $params[] = $categoryType;
}

// Combine WHERE conditions
$whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Set up pager (pagination)
$p = new SimplePager(
    "SELECT DISTINCT p.* 
     FROM product p
     JOIN product_category pc ON p.productID = pc.productID
     JOIN category c ON pc.categoryID = c.categoryID 
     $whereClause 
     AND p.status = 'available'
     ORDER BY p.productName", 
    $params,
    12,  // Items per page
    $page
);

// Handle form submissions
if (is_post()) {

    // Handle adding to cart
    if (isset($_POST['add_to_cart'])) {
        $productID = $_POST['product_id'];
        $quantity = 1; // Default quantity

        add_to_cart($productID, $quantity);
        redirect();
    }

    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $productID = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity >= 1 && $quantity <= 10) {
            $_SESSION['cart'][$productID]['quantity'] = $quantity;
        }

        redirect();
    }
}

$_title = 'KAWAII.Category';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/product.css">

<div class="product-grid">
    <?php
    // Fetch products
    $products = $p->result;

    if ($products) {
        foreach ($products as $row) {
            // Get first image
            $imagePath = "/images/placeholder.png"; // Default image
            $sqlImage = "SELECT imageName FROM gallery WHERE productID = :productID AND is_cover = 1 LIMIT 1";
            $stmImage = $_db->prepare($sqlImage);
            $stmImage->execute([':productID' => $row->productID]);

            // Fetch the image data
            if ($imageRow = $stmImage->fetch(PDO::FETCH_ASSOC)) {
                $firstImage = trim($imageRow['imageName']);
                $imagePath = "../productImage/" . $firstImage; // Construct the image path
            }

            // Check if the image exists in the directory
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . str_replace("..", "", $imagePath))) {
                $imagePath = $imagePath; // Set the image path if file exists
            } else {
                $imagePath = "/images/placeholder.png"; // Fall back to placeholder if not found
            }
    ?>
            <div class="product" id="product-<?= htmlspecialchars($row->productID) ?>">
                <a href="productdetails.php?id=<?= htmlspecialchars($row->productID) ?>">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($imagePath) ?>"
                            alt="<?= htmlspecialchars($row->productName) ?>"
                            onerror="this.src='/images/placeholder.png'">
                    </div>
                    <div class="product-details">
                        <h3 class="product-name"><?= htmlspecialchars($row->productName) ?></h3>
                        <p class="product-price">RM<?= htmlspecialchars($row->price) ?></p>
                    </div>
                </a>
                <div class="product-actions">
                    <form method="POST">
                        <?php if (isset($_user) && ($_user->role === ROLE_ADMIN || $_user->role === ROLE_MEMBER || $_user->role === ROLE_STAFF)): ?>
                            <!-- Logged-in users: Normal Add to Cart -->
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($row->productID) ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <!-- Guests: Button redirects to login with return URL -->
                            <button
                                type="button"
                                class="add-to-cart"
                                onclick="window.location.href='/page/login.php?return_url=<?= urlencode($current_url) ?>'">
                                Add to Cart
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
    <?php
        }
    } else {
        echo '<p class="no-products">No products found.</p>';
    }
    ?>
</div>

<!-- Pagination Links (preserves categoryType filter) -->
<div class="pagination">
    <?= $p->html("&categoryType=$categoryType") ?>
</div>

<?php
include '../_foot.php';
?>

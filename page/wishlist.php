<?php
require '../_base.php';

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if (isset($_SESSION['_user']) && empty($_SESSION['wishlist'])) {
    load_wishlist_from_db($_SESSION['_user']->id);
}

// Handle Add to Wishlist (if you have this action)
if (is_post() && isset($_POST['add_to_wishlist'])) {
    $productID = trim($_POST['product_id'] ?? '');
    if (!empty($productID) && add_to_wishlist($productID)) {
        temp('info', 'Added to wishlist successfully!');
    }
    redirect();
}

// Handle Remove from Wishlist
if (is_post() && isset($_POST['remove_from_wishlist'])) {
    $productID = trim($_POST['product_id'] ?? '');
    if (!empty($productID) && remove_from_wishlist($productID)) {
        temp('info', 'Removed from wishlist successfully!');
    }
    redirect();
}

// Handle Add to Cart from Wishlist
if (is_post() && isset($_POST['add_to_cart'])) {
    $productID = trim($_POST['product_id'] ?? '');
    if (!empty($productID) && add_to_cart($productID, 1)) {
        temp('info', 'Added to cart successfully!');
    }
    redirect();
}

// Get the wishlist
$wishlistItems = get_wishlist();

$_title = 'KAWAII.Wishlist';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/wishlist.css">

<?php if ($message = temp('info')): ?>
    <div id="info" class="flash-message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (!empty($wishlistItems)): ?>
    <h2 class="wishlist-title">Your Wishlist</h2>
    <div class="wishlist-grid">
        <?php foreach ($wishlistItems as $item): ?>
            <div class="wishlist-item">
                <a href="productdetails.php?id=<?= $item['id'] ?>" class="wishlist-link">
                    <div class="wishlist-image">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </div>
                    <div class="wishlist-details">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="price">RM<?= number_format($item['price'], 2) ?></p>
                    </div>
                </a>

                <div class="button-group">
                    <form method="POST" action="wishlist.php">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="remove_from_wishlist" class="remove-from-wishlist">
                            <i class="fa-solid fa-xmark"></i> Remove
                        </button>
                    </form>

                    <form method="POST" action="wishlist.php">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                        </button>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-wishlist">
        <h2>Your Wishlist</h2>
        <p>Your Wishlist is Feeling Lonely..</p>
        <p>No plushie friends here yet!</p>
        <p>Start shopping and find your cuddly companions!</p>
        <a href="category.php" class="btn">BROWSE PLUSHIES NOW</a>
    </div>
<?php endif; ?>

<?php include '../_foot.php'; ?>
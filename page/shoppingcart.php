<?php
require '../_base.php';

auth(ROLE_MEMBER);

// Load cart from DB if member is logged in
if (isset($_SESSION['_user'])) {
    load_cart_from_db($_SESSION['_user']->id);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (is_post() && isset($_POST['add_to_cart'])) {
    $productID = $_POST['product_id'];
    if (add_to_cart($productID, 1)) {
        temp('success', 'Added to cart successfully!');
    } else {
        temp('error', 'Failed to add to cart.');
    }
    redirect();
}

// Handle Remove from Cart
if (is_post() && isset($_POST['remove_from_cart'])) {
    $productID = $_POST['product_id'];
    remove_from_cart($productID);
    temp('success', 'Removed successfully!');
    redirect();
}

// Handle Clear Cart
if (is_post() && isset($_POST['clear_cart'])) {
    clear_cart();
    unset($_SESSION['voucherID'], $_SESSION['voucherCode'], $_SESSION['discountPercentage']);
    temp('success', 'Cart cleared successfully!');
    redirect();
}

// Handle Update Quantity
if (is_post() && isset($_POST['update_cart'])) {
    $productID = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        update_cart($productID, $quantity);
        temp('success', 'Cart updated successfully!');
        // Explicitly reload the cart data
        $cart = get_cart_with_images();
    } else {
        temp('error', 'Invalid quantity.');
    }
    redirect();
}

// Get the cart
$cart = get_cart_with_images();

// Fetch active vouchers
$stm = $_db->prepare("SELECT voucherID, voucherCode, discountAmount, usageLimit, timesUsed, expiryDate FROM voucher WHERE status = 'active'");
$stm->execute();
$vouchers = $stm->fetchAll(PDO::FETCH_ASSOC);

// Handle voucher submission
if (is_post() && isset($_POST['apply_voucher'])) {
    $enteredCode = trim($_POST['voucher_code']);
    $found = false;

    foreach ($vouchers as $voucher) {
        if ($enteredCode === $voucher['voucherCode']) {
            // Check if the voucher is active and hasn't exceeded usage limit
            if ($voucher['timesUsed'] >= $voucher['usageLimit']) {
                temp('error', "This voucher has reached its usage limit.");
            } elseif ($voucher['expiryDate'] && strtotime($voucher['expiryDate']) < time()) {
                temp('error', "This voucher has expired.");
            } else {
                // Apply voucher and increment timesUsed
                $_SESSION['voucherID'] = $voucher['voucherID'];
                $_SESSION['voucherCode'] = $voucher['voucherCode'];
                $_SESSION['discountAmount'] = $voucher['discountAmount']; // Apply fixed discount amount
                $found = true;

                // Increment timesUsed for the voucher
                $updateStm = $_db->prepare("UPDATE voucher SET timesUsed = timesUsed + 1 WHERE voucherID = ?");
                $updateStm->execute([$voucher['voucherID']]);

                temp('success', "Voucher applied! RM{$voucher['discountAmount']} off.");
            }
            break;
        }
    }

    if (!$found) {
        unset($_SESSION['voucherID'], $_SESSION['voucherCode'], $_SESSION['discountAmount']);
        temp('error', "Invalid voucher code.");
    }

    redirect();
}

$discountAmount = $_SESSION['discountAmount'] ?? 0;  // Fixed discount amount
$subtotal = array_sum(array_map(fn($p) => (float)$p['price'] * (int)$p['quantity'], $cart));

// Ensure discount doesn't exceed subtotal
if ($discountAmount > $subtotal) {
    $discountAmount = $subtotal;
}

$totalAfterDiscount = $subtotal - $discountAmount;

// Calculate total quantity
$totalQuantity = array_sum(array_map(fn($p) => (int)$p['quantity'], $cart));

$_title = 'KAWAII.Shopping Cart';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/shoppingcart.css">

<?php if ($message = temp('success')): ?>
    <div id="success" class="flash-message"><?= htmlspecialchars($message) ?></div>
<?php elseif ($message = temp('error')): ?>
    <div id="error" class="flash-message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (!empty($cart)) : ?>
    <h2>Your Shopping Cart</h2>
    <table>
        <tr>
            <th>Item</th>
            <th></th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
        </tr>

        <?php
        $checkoutAllowed = true;
        foreach ($cart as $product) :
            $stm = $_db->prepare("SELECT stockQuantity FROM product WHERE productID = ?");
            $stm->execute([$product['id']]);
            $stock = $stm->fetchColumn();
            $exceedsStock = $product['quantity'] > $stock;

            if ($exceedsStock) {
                $checkoutAllowed = false;
            }
        ?>
            <tr <?= $exceedsStock ? 'style="background-color: #ffe6e6;"' : '' ?>>
                <td><img src="<?= htmlspecialchars($product['image']) ?>" width="50"></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>RM<?= number_format((float)$product['price'], 2) ?></td>
                <td>
                    <form method="POST" action="shoppingcart.php">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="number" name="quantity" value="<?= (int)$product['quantity'] ?>" min="1" max="100">
                        <button type="submit" name="update_cart">Update</button>
                    </form>
                    <?php if ($exceedsStock): ?>
                        <div style="color:rgb(240, 120, 120); font-size: 0.9em;">Only <?= $stock ?> in stock</div>
                    <?php endif; ?>
                </td>
                <td>RM<?= number_format((float)$product['price'] * (int)$product['quantity'], 2) ?></td>
                <td>
                    <form method="POST" action="shoppingcart.php">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <button type="submit" name="remove_from_cart">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td style="font-weight: bold;">Total:</td>
            <td></td>
            <td></td>
            <td style="font-weight: bold;"><?= $totalQuantity ?> item</td>
            <td style="font-weight: bold;">RM<?= number_format($subtotal, 2) ?></td>
            <td></td>
        </tr>
    </table>

    <div class="cart-footer">
        <form method="POST" action="shoppingcart.php">
            <button type="submit" name="clear_cart" class="clear-cart">Clear Cart</button>
        </form>

        <div class="cart-summary">
            <div class="summary-row">
                <span class="label">Subtotal&emsp;&emsp;&emsp;&emsp;&emsp;:</span>
                <span class="value">RM<?= number_format($subtotal, 2) ?></span>
            </div>

            <div class="voucher-section">
                <div class="summary-row">
                    <span class="label">Voucher Code&emsp;&emsp; :</span>
                    <form method="POST" class="voucher-input-container">
                        <input type="text" name="voucher_code" placeholder="Add Voucher Code" value="<?= htmlspecialchars($_SESSION['voucherCode'] ?? '') ?>">
                        <button type="submit" name="apply_voucher">Apply</button>
                    </form>
                </div>
            </div>

            <?php if ($discountAmount > 0): ?>
                <div class="summary-row discount">
                    <span class="label">Voucher Discount&emsp;:</span>
                    <span class="value">- RM&emsp;<?= number_format($discountAmount, 2) ?></span>
                </div>
            <?php endif; ?>

            <div class="summary-row total">
                <span class="label">Current Total&emsp;&emsp; :</span>
                <span class="value">RM<?= number_format($totalAfterDiscount, 2) ?></span>
            </div>

            <?php if (!$checkoutAllowed): ?>
                <button class="checkout-btn" style="background-color: #ccc; cursor: not-allowed;" disabled>
                    CHECK OUT
                </button>
            <?php else: ?>
                <a href="checkout.php" class="checkout-btn">CHECK OUT</a>
            <?php endif; ?>
        </div>
    </div>

<?php else : ?>
    <p class='empty-cart'>Your cart is empty.</p>
<?php endif; ?>

<?php include '../_foot.php'; ?>
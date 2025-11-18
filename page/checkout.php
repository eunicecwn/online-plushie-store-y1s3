<?php
require '../_base.php';

auth(ROLE_MEMBER);

// 1. Get member ID from authenticated user
$memberID = $_user->id;

// 2. Fetch current member details from database
$stm = $_db->prepare("
    SELECT memberName, memberEmail, phoneNumber, memberAddress 
    FROM member 
    WHERE memberID = ?
");

$stm->execute([$memberID]);
$member = $stm->fetch(PDO::FETCH_OBJ);

if (!$member) {
    temp('error', 'Member not found');
    redirect();
}
// Check for cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    temp('error', 'No items in cart.');
    redirect('shoppingcart.php');
}

$cart = get_cart_with_images();
if (!$cart) {
    temp('error', 'Cart could not be loaded.');
    redirect('shoppingcart.php');
}

$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$subtotal = calculate_subtotal($cart);
$discountAmount = $_SESSION['discountAmount'] ?? 0;
if ($discountAmount > $subtotal) {
    $discountAmount = $subtotal;
}
$totalAfterDiscount = $subtotal - $discountAmount;


// 3. Set default form values
$isPost = is_post();
$nameValue = $isPost ? req('name') : ($member->memberName ?? '');
$emailValue = $isPost ? req('email') : ($member->memberEmail ?? '');
$phoneValue = $isPost ? req('phone') : ($member->phoneNumber ?? '');
$addressValue = $isPost ? req('address') : ($member->memberAddress ?? '');

if (is_post() && isset($_POST['place_order'])) {
    $name = req('name');
    $email = req('email');
    $phone = req('phone');
    $shippingAddress = req('address');
    $paymentMethod = req('payment_method');

    if (!$name) $_err['name'] = 'Required';
    if (!$email) $_err['email'] = 'Required';
    elseif (!is_email($email)) $_err['email'] = 'Invalid email';
    if (!$phone) $_err['phone'] = 'Required';
    if (!$shippingAddress) $_err['address'] = 'Required';

    if (!$_err) {
        try {
            $_db->beginTransaction();

            $memberID = $_user->id; // Use authenticated member ID

            // Generate order ID
            $orderID = generate_order_id();

            // Insert order
            $stm = $_db->prepare("INSERT INTO orders (orderID, orderStatus, orderDate, totalAmount, memberID, discountAmount, voucherID) VALUES (?, 'Pending', NOW(), ?, ?, ?, ?)");
            $stm->execute([$orderID, $totalAfterDiscount, $memberID, $discountAmount, $_SESSION['voucherID'] ?? null]);

            // Insert ordered items
            $stmItem = $_db->prepare("INSERT INTO ordereditem (orderID, productID, orderItemQuantity, unitPrice, totalPrice, discountAmount, finalPrice) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmStock = $_db->prepare("SELECT stockQuantity FROM product WHERE productID = ?");
            $stmUpdateStock = $_db->prepare("UPDATE product SET stockQuantity = stockQuantity - ? WHERE productID = ?");

            foreach ($cart as $product) {
                $productID = $product['id'];
                $quantity = (int)$product['quantity'];
                $unitPrice = $product['price'];
                $totalPrice = $unitPrice * $quantity;
                $itemDiscount = $discountAmount / count($cart);
                $finalPrice = $totalPrice - $itemDiscount;

                $stmStock->execute([$productID]);
                $currentStock = (int)$stmStock->fetchColumn();
                if ($currentStock < $quantity) throw new Exception("Not enough stock for " . $product['name']);

                $stmItem->execute([$orderID, $productID, $quantity, $unitPrice, $totalPrice, $itemDiscount, $finalPrice]);
                $stmUpdateStock->execute([$quantity, $productID]);
            }

            // Insert payment
            $stm = $_db->prepare("
                INSERT INTO payment (
                    orderID, memberID, paymentDate, paymentMethod, amountPay
                ) VALUES (
                    ?, ?, NOW(), ?, ?
                )
            ");
            $stm->execute([$orderID, $memberID, $paymentMethod, $totalAfterDiscount]);

            $paymentID = $_db->lastInsertId();

            // Insert receipt
            $receiptNo = 'RCPT-' . strtoupper(uniqid());
            $stm = $_db->prepare("
                INSERT INTO receipt (receiptNo, paymentID, orderID, amountPay, receiptDate)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stm->execute([$receiptNo, $paymentID, $orderID, $totalAfterDiscount]);

            // Insert delivery
            // Insert delivery with ALL shipping details
            $trackingNumber = 'TRK-' . strtoupper(uniqid());
            $stm = $_db->prepare("INSERT INTO delivery (orderID, trackingNumber, recipient_name,recipient_phone,shippingAddress, estimatedDate, deliveryDate, courierService) 
            VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 DAY), NULL, NULL)
            ");
            $stm->execute([
                $orderID,
                $trackingNumber,
                $name,        // From checkout form (may differ from profile)
                $phone,       // From checkout form
                $shippingAddress,
            ]);

            // Clear the cart from the member_cart table
            $clearCartStmt = $_db->prepare("DELETE FROM member_carts WHERE memberID = ?");
            $clearCartStmt->execute([$memberID]);

            $_db->commit();
            unset($_SESSION['cart'], $_SESSION['discountAmount'], $_SESSION['voucherID']);

            // Send email confirmation
            $userEmail = isset($_SESSION['memberID']) ? $_SESSION['user']['email'] : $email;

            $mail = get_mail();
            $mail->addAddress($userEmail);
            $mail->Subject = "🧸 Order Confirmation - Order ID: $orderID";
            $mail->isHTML(true);
            $mail->Body = "
                <h2>Hi {$name},</h2>
                <p>Thank you for your order from <strong>KAWAII Plushies</strong>!</p>
                <p><strong>Order ID:</strong> {$orderID}</p>
                <p><strong>Total Paid:</strong> RM " . number_format($totalAfterDiscount, 2) . "</p>
                <p>We will ship your items to the following address:</p>
                <p style='background:#f9f9f9;padding:10px;border-radius:6px;'>{$shippingAddress}</p>
                <p>Our team is packing your plushies with love. They'll be on their way soon! 📦🐾</p>
                <br>
                <p>With love,</p>
                <p><strong>KAWAII Team 🩷</strong></p>
            ";

            try {
                $mail->send();
            } catch (Exception $e) {
                error_log("Mail error: " . $mail->ErrorInfo);
            }

            redirect("orderconfirm.php?id=$orderID");
        } catch (Exception $e) {
            if ($_db->inTransaction()) $_db->rollBack();
            temp('error', 'Order failed: ' . $e->getMessage());
            redirect();
        }
    }
}

$_title = 'KAWAII.Checkout';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/checkout.css">

<?php if ($message = temp('info')): ?>
    <div id="info" class="flash-message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h2>Checkout</h2>

<div class="checkout-wrapper">
    <form method="POST" class="checkout-form">
        <h3>Shipping Details</h3>

        <input type="text" name="name" placeholder="Full Name"
            value="<?= htmlspecialchars($nameValue) ?>">
        <?= err('name') ?>

        <input type="email" name="email" placeholder="Email"
            value="<?= htmlspecialchars($emailValue) ?>">
        <?= err('email') ?>

        <input type="tel" name="phone" placeholder="Phone Number"
            value="<?= htmlspecialchars($phoneValue) ?>">
        <?= err('phone') ?>

        <textarea name="address" placeholder="Shipping Address">
        <?= htmlspecialchars($addressValue) ?>

    </textarea>
        <?= err('address') ?>
        <h3>Payment Method</h3>
        <div class="payment-option">
            <label class="payment-label">
                <span>Credit Card</span>
                <input type="radio" name="payment_method" value="Credit Card" checked>
            </label>
        </div>
        <div class="payment-option">
            <label class="payment-label">
                <span>Debit Card</span>
                <input type="radio" name="payment_method" value="Debit Card">
            </label>
        </div>
        <div class="payment-option">
            <label class="payment-label">
                <span>PayPal</span>
                <input type="radio" name="payment_method" value="PayPal">
            </label>
        </div>
        <div class="payment-option">
            <label class="payment-label">
                <span>Bank Transfer</span>
                <input type="radio" name="payment_method" value="Bank Transfer">
            </label>
        </div>
        <div class="payment-option">
            <label class="payment-label">
                <span>Cash On Delivery</span>
                <input type="radio" name="payment_method" value="Cash On Delivery">
            </label>
        </div>

        <div id="card-details-section" class="card-details">
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" name="card_number" placeholder="0000 0000 0000 0000">
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Expiry (MM/YY)</label>
                    <input type="text" name="card_expiry" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" name="card_cvv" placeholder="123">
                </div>
            </div>
        </div>

        <button type="submit" name="place_order" class="place-order-btn">Place Order and Pay</button>
    </form>

    <div class="order-summary">
        <h3>Order Summary</h3>
        <ul class="order-items">
            <?php foreach ($cart as $product): ?>
                <li class="order-item">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="order-img">
                    <div class="order-details">
                        <span class="order-name"><?= htmlspecialchars($product['quantity']) ?> × <?= htmlspecialchars($product['name']) ?></span>
                        <span class="order-price">RM<?= number_format($product['price'] * $product['quantity'], 2) ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="summary-line">Subtotal &emsp; :&emsp;&emsp;RM<?= number_format($subtotal, 2) ?></p>
        <?php if ($discountAmount > 0): ?>
            <p class="summary-line">Discount&emsp; : <span class="discount">&emsp; -RM <?= number_format($discountAmount, 2) ?></span></p>
        <?php endif; ?>
        <p class="total-price" style="text-align: right;">Total&emsp;&emsp; :&emsp;RM<?= number_format($totalAfterDiscount, 2) ?></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const cardDetailsSection = document.getElementById('card-details-section');

        const cardNumber = document.querySelector('input[name="card_number"]');
        const cardExpiry = document.querySelector('input[name="card_expiry"]');
        const cardCVV = document.querySelector('input[name="card_cvv"]');

        function toggleCardDetails() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const isCard = selectedMethod === 'Credit Card' || selectedMethod === 'Debit Card';

            cardDetailsSection.style.display = isCard ? 'block' : 'none';

            // Conditionally set required attribute
            cardNumber.required = isCard;
            cardExpiry.required = isCard;
            cardCVV.required = isCard;
        }

        // Initial check
        toggleCardDetails();

        // Listen for changes
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', toggleCardDetails);
        });
    });
</script>

<?php include '../_foot.php'; ?>
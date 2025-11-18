<?php
require '../../_base.php';

$orderID = req('id');

// Fetch order and member details
$sql = "
    SELECT o.*, m.memberName, m.memberEmail, d.recipient_phone, d.shippingAddress,
           d.recipient_name, d.trackingNumber
    FROM orders o
    LEFT JOIN member m ON o.memberID = m.memberID
    LEFT JOIN delivery d ON o.orderID = d.orderID
    WHERE o.orderID = ?
    LIMIT 1
";

global $_db;
$stm = $_db->prepare($sql);
$stm->execute([$orderID]);
$order = $stm->fetch();

if (!$order) {
    redirect('orderTable.php');
}

// Fetch ordered items
$sql = "
    SELECT oi.*, p.productName, g.imageName
    FROM orderedItem oi
    LEFT JOIN product p ON oi.productID = p.productID
    LEFT JOIN gallery g ON p.productID = g.productID
    WHERE oi.orderID = ? AND is_cover = 1;
";
$stm = $_db->prepare($sql);
$stm->execute([$orderID]);
$orderedItems = $stm->fetchAll();

$_title = 'KAWAII.SellerCentre | Order Details';
include '../../_headadmin.php';
?>

<link rel="stylesheet" href="/css/member.css">

<div class="profile-container">

    <!-- Profile Header -->
    <div class="profile-header">
        <h2>Order Details</h2>
    </div>

    <!-- Member Info Section -->
    <div class="profile-info">
        <p><strong>Recipient Name</strong><span> <?= encode($order->recipient_name ?? 'Unknown') ?></span></p>
        <p><strong>Email</strong><span> <?= encode($order->memberEmail ?? 'Unknown') ?></span></p>
        <p><strong>Phone Number</strong><span> <?= encode($order->recipient_phone ?? 'Unknown') ?></span></p>
        <p><strong>Address</strong><span> <?= nl2br(encode($order->shippingAddress ?? 'Unknown')) ?></span></p>
        <p><strong>Tracking Number</strong><span> <?= encode($order->trackingNumber ?? 'Unknown') ?></span></p>
</div>



    <hr>

    <!-- Product List Table Section -->
    <h3>Product List</h3>

    <table class="order-details-table">
        <thead>
            <tr>
                <th>No.</th> 
                <th>Product</th> 
                <th>Quantity</th>
                <th>Unit Price (RM)</th>
                <th>Total Price (RM)</th>
                <th>Discount (RM)</th>
                <th>Final Price (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($orderedItems) > 0): ?>
                <?php $counter = 1; ?>
                <?php foreach ($orderedItems as $item): ?>
                    <tr>
                        <td><?= $counter++ ?></td> 

                        <td class="product-column">
                            <div class="product-image-details">
                                <?php if ($item->imageName): ?>
                                    <img src="/productImage/<?= encode($item->imageName) ?>" alt="<?= encode($item->productName) ?>" style="max-height: 80px;">
                                <?php else: ?>
                                    <div>No Image Available</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-name">
                                <?= encode($item->productName ?? 'Unknown Product') ?>
                            </div>
                        </td>

                        <td><?= encode($item->orderItemQuantity) ?></td>
                        <td><?= number_format($item->unitPrice, 2) ?></td>
                        <td><?= number_format($item->totalPrice, 2) ?></td>
                        <td><?= number_format($item->discountAmount, 2) ?></td>
                        <td><?= number_format($item->finalPrice, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-items">No items found for this order.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
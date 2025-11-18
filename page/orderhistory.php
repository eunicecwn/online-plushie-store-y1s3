<?php
require '../_base.php';

auth(ROLE_MEMBER);

// 1. Get member ID
$memberID = $_user->id;

// 2. Get status filter from URL
$statusFilter = $_GET['status'] ?? '';

// 3. If user clicks "Order Received" button
if (isset($_POST['received_order_id'])) {
    $receivedOrderID = $_POST['received_order_id'];

    // Change Delivered to Completed 
    $stmUpdate = $_db->prepare("UPDATE orders SET orderStatus = 'Completed' WHERE orderID = ? AND memberID = ?");
    $stmUpdate->execute([$receivedOrderID, $memberID]);

    // Refresh back to Completed tab
    redirect("orderhistory.php?status=Completed");
}

// 4. Fetch orders
$sql = "SELECT * FROM orders WHERE memberID = :memberID";
$params = ['memberID' => $memberID];

if (!empty($statusFilter)) {
    $sql .= " AND orderStatus = :status";
    $params['status'] = $statusFilter;
}

$sql .= " ORDER BY orderDate DESC";

$stm = $_db->prepare($sql);
$stm->execute($params);
$orders = $stm->fetchAll(PDO::FETCH_OBJ);

$_title = 'KAWAII.Order History';
include '../_head.php';
?>

<link rel="stylesheet" href="/css/orderhistory.css">
<div class="fixed-top-bar">
    <h2>Order History</h2>

    <div class="filter-buttons">
    <a href="orderhistory.php" class="filter-btn <?= $statusFilter == '' ? 'active' : '' ?>">All</a>
        <a href="orderhistory.php?status=Pending" class="filter-btn <?= $statusFilter == 'Pending' ? 'active' : '' ?>">Pending</a>
        <a href="orderhistory.php?status=Shipped" class="filter-btn <?= $statusFilter == 'Shipped' ? 'active' : '' ?>">Shipped</a>
        <a href="orderhistory.php?status=Delivered" class="filter-btn <?= $statusFilter == 'Delivered' ? 'active' : '' ?>">Delivered</a>
        <a href="orderhistory.php?status=Completed" class="filter-btn <?= $statusFilter == 'Completed' ? 'active' : '' ?>">Completed</a>
        <a href="orderhistory.php?status=Cancelled" class="filter-btn <?= $statusFilter == 'Cancelled' ? 'active' : '' ?>">Cancelled</a>
    </div>
</div>

    <div class="page-content">

    <?php if (empty($orders)): ?>
        <p style="font-size: 18px;">No orders found for this status.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-summary">
                <h3>Order #<?= htmlspecialchars($order->orderID) ?></h3>
                <table class="order-table">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars($order->orderDate) ?></td>
                        <td><?= htmlspecialchars($order->orderStatus) ?></td>
                        <td>RM<?= number_format($order->totalAmount, 2) ?></td>
                    </tr>
                </table>

                <?php
                // Fetch ordered items
                $stmItems = $_db->prepare("
                    SELECT oi.*, p.productName, 
                        (SELECT imageName FROM gallery 
                         WHERE gallery.productID = oi.productID AND is_cover = 1 
                         LIMIT 1) AS imageName
                    FROM ordereditem oi
                    JOIN product p ON p.productID = oi.productID
                    WHERE orderID = ?
                ");
                $stmItems->execute([$order->orderID]);
                $items = $stmItems->fetchAll(PDO::FETCH_OBJ);
                ?>

                <div class="order-items">
                    <?php foreach ($items as $item):
                        $imageURL = "/images/placeholder.png";

                        if (!empty($item->imageName)) {
                            $firstImage = trim($item->imageName);
                            $testPath = "../productImage/" . $firstImage;
                            $serverPath = $_SERVER['DOCUMENT_ROOT'] . str_replace("..", "", $testPath);

                            if (file_exists($serverPath)) {
                                $imageURL = $testPath;
                            }
                        }
                    ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($imageURL) ?>" alt="<?= htmlspecialchars($item->productName) ?>">
                            <div class="order-item-details">
                                <p><?= htmlspecialchars($item->orderItemQuantity) ?> × <?= htmlspecialchars($item->productName) ?></p>
                                <p>RM<?= number_format($item->finalPrice, 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($order->orderStatus == 'Delivered'): ?>
                    <!-- Show Order Received button for delivered -->
                    <form method="post" style="text-align: right; margin-top: 10px;">
                        <input type="hidden" name="received_order_id" value="<?= htmlspecialchars($order->orderID) ?>">
                        <button type="submit" class="order-received-btn">Order Received</button>
                    </form>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../_foot.php'; ?>
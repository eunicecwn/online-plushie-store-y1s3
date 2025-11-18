<?php
require '../../_base.php';
require_once '../../lib/SimplePager.php';

// Get request parameters
$searchTerm = req('search');
$statusFilter = req('status');
$GLOBALS['search'] = $searchTerm;

// Define sortable columns
$fields = [
    'orderID' => 'Order ID',
    'orderDate' => 'Order Date',
    'totalAmount' => 'Total Amount',
    'memberID' => 'Member ID',
];

// Validate sorting parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'orderDate';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc'; // Default to newest first

$page = max(1, (int)req('page', 1)); 

// Build WHERE conditions
$whereConditions = ["1=1"];
$params = [];

if ($searchTerm) {
    $whereConditions[] = "(o.orderID LIKE ? OR o.memberID LIKE ?)";
    array_push($params, "%$searchTerm%", "%$searchTerm%");
}

if ($statusFilter && in_array($statusFilter, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) {
    $whereConditions[] = "o.orderStatus = ?";
    $params[] = $statusFilter;
}

$whereClause = implode(' AND ', $whereConditions);

$sql = "
    SELECT * FROM (
        SELECT o.*, m.memberName 
        FROM `orders` o
        LEFT JOIN member m ON o.memberID = m.memberID
        WHERE $whereClause
    ) AS sub
    ORDER BY $sort $dir
";

$p = new SimplePager($sql, $params, 8, $page);


$orders = $p->result;

$_title = 'KAWAII.SellerCentre | View All Orders';
include '../../_headadmin.php';
?>
<link rel="stylesheet" href="/css/member.css">

<h1 class="title">View All Orders</h1>

<form class="search-form" method="GET">
    <input type="text" name="search" placeholder="Search by Order ID or Member ID" value="<?= encode($searchTerm) ?>">

    <select name="status">
        <option value="">All Status</option>
        <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Shipped" <?= $statusFilter === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
        <option value="Delivered" <?= $statusFilter === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
        <option value="Cancelled" <?= $statusFilter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>

    <button type="submit" class="search-button">Apply</button>

    <?php if ($searchTerm || $statusFilter): ?>
        <a href="orderTable.php" class="btn btn-reset">Reset</a>
    <?php endif; ?>
</form>

<p class="record-count">
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<div class="table-container" style="position:fixed">
    <table class="order-table">
        <thead>
            <tr>
                <th>No.</th>
                <?php foreach ($fields as $field => $label): ?>
                    <th>
                        <a href="?<?= http_build_query([
                            'sort' => $field,
                            'dir' => ($sort === $field && $dir === 'asc') ? 'desc' : 'asc',
                            'search' => $searchTerm,
                            'status' => $statusFilter,
                            'page' => $page
                        ]) ?>">
                            <?= $label ?>
                            <?php if ($sort === $field): ?>
                                <?= $dir === 'asc' ? '▴' : '▾' ?>
                            <?php endif; ?>
                        </a>
                    </th>
                <?php endforeach; ?>
                <th>Status</th>
                <th>Member Name</th>
                <th>Discount</th>
                <th>Final Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="10" class="no-results">
                        No orders found
                        <?php if ($searchTerm): ?>
                            matching: <strong><?= encode($searchTerm) ?></strong>
                        <?php endif; ?>
                        <?php if ($statusFilter): ?>
                            with status: <strong><?= encode($statusFilter) ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $index => $order): ?>
                    <?php
                    $statusClass = 'status-badge ';
                    switch ($order->orderStatus) {
                        case 'Pending':
                            $statusClass .= 'status-pending';
                            break;
                        case 'Shipped':
                            $statusClass .= 'status-active';
                            break;
                        case 'Delivered':
                            $statusClass .= 'status-delivered';
                            break;
                        case 'Cancelled':
                            $statusClass .= 'status-inactive';
                            break;
                    }
                    
                    $finalAmount = $order->totalAmount - $order->discountAmount;
                    ?>
                    <tr>
                        <td><?= $index + 1 + (($page - 1) * 8) ?></td>
                        <td><?= encode($order->orderID) ?></td>
                        <td><?= date('d M Y H:i', strtotime($order->orderDate)) ?></td>
                        <td>RM<?= number_format($order->totalAmount, 2) ?></td>
                        <td><?= encode($order->memberID) ?></td>
                        <td>
                            <?= $order->orderStatus ?>
                        </td>
                        <td><?= encode($order->memberName ?? 'N/A') ?></td>
                        <td>RM<?= number_format($order->discountAmount, 2) ?></td>
                        <td>RM<?= number_format($finalAmount, 2) ?></td>
                        <td>
                            <a href="orderDetails.php?id=<?= $order->orderID ?>" class="btn btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <?php if ($order->orderStatus === 'Pending'): ?>
                                <a href="updateOrderStatus.php?id=<?= $order->orderID ?>&status=Shipped" class="btn btn-ship">
                                    <i class="fas fa-truck"></i> Ship
                                </a>
                            <?php elseif ($order->orderStatus === 'Shipped'): ?>
                                <a href="updateOrderStatus.php?id=<?= $order->orderID ?>&status=Delivered" class="btn btn-deliver">
                                    <i class="fas fa-check-circle"></i> Deliver
                                </a>
                            <?php endif; ?>
                            <?php if ($order->orderStatus !== 'Cancelled' && $order->orderStatus !== 'Delivered'): ?>
                                <a href="cancelOrder.php?id=<?= $order->orderID ?>" class="btn btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <?= $p->html(http_build_query([
        'search' => $searchTerm,
        'status' => $statusFilter,
        'sort' => $sort,
        'dir' => $dir
    ])) ?>
</div>

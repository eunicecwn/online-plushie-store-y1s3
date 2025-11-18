<?php

require("../_base.php");

$_title = 'KAWAII.SellerCentre';
include "../_headadmin.php";

// Initialize variables with defaults
$stats = [
    'total_revenue' => 0,
    'total_orders' => 0,
    'avg_order_value' => 0,
    'total_customers' => 0
];

$order_status = [
    'pending_orders' => 0,
    'shipped_orders' => 0,
    'delivered_orders' => 0,
    'cancelled_orders' => 0
];

$top_products = [];
$recent_orders = [];

try {
    // Get stats with error handling
    $stats['total_revenue'] = fetch_single_value("SELECT COALESCE(SUM(totalAmount), 0) AS total_revenue FROM orders", 'total_revenue');
    $stats['total_orders'] = fetch_single_value("SELECT COALESCE(COUNT(*), 0) AS total_orders FROM orders", 'total_orders');
    $stats['avg_order_value'] = fetch_single_value("SELECT COALESCE(AVG(totalAmount), 0) AS avg_order_value FROM orders", 'avg_order_value');
    $stats['total_customers'] = fetch_single_value("SELECT COALESCE(COUNT(*), 0) AS total_customers FROM member", 'total_customers');

    // Get order status counts with COALESCE to handle NULL values
    $status_result = $_db->query("SELECT 
        COALESCE(COUNT(CASE WHEN orderStatus = 'Pending' THEN 1 END), 0) AS pending_orders,
        COALESCE(COUNT(CASE WHEN orderStatus = 'Shipped' THEN 1 END), 0) AS shipped_orders,
        COALESCE(COUNT(CASE WHEN orderStatus = 'Delivered' THEN 1 END), 0) AS delivered_orders,
        COALESCE(COUNT(CASE WHEN orderStatus = 'Cancelled' THEN 1 END), 0) AS cancelled_orders
    FROM orders");

    if ($status_result) {
        // Fetch the result as an associative array
        $order_status = $status_result->fetch(PDO::FETCH_ASSOC) ?? $order_status;
    }

    // Get top products with error handling
    $top_products = fetch_all_rows("SELECT p.productName, COALESCE(SUM(oi.orderItemQuantity), 0) AS total_sold
        FROM ordereditem oi
        JOIN product p ON oi.productID = p.productID
        GROUP BY p.productID
        ORDER BY total_sold DESC
        LIMIT 3");

    // Get recent orders with error handling
    $recent_orders = fetch_all_rows("SELECT o.orderID, o.orderDate, o.totalAmount, m.memberName 
        FROM orders o
        JOIN member m ON o.memberID = m.memberID
        ORDER BY o.orderDate DESC
        LIMIT 5");

    $sales_data = fetch_all_rows("SELECT DATE(orderDate) AS order_day, SUM(totalAmount) AS total_sales FROM orders GROUP BY order_day ORDER BY order_day ASC");
} catch (Exception $e) {
    handle_exception($e, 'Failed to load dashboard statistics');
}

function fetch_single_value($query, $key)
{
    global $_db; // Use PDO instead of MySQLi
    try {
        $stm = $_db->query($query);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        return $row[$key] ?? 0;
    } catch (Exception $e) {
        handle_db_error($e, "Failed to fetch single value");
        return 0;
    }
}

function fetch_all_rows($query)
{
    global $_db; // Use PDO instead of MySQLi
    try {
        $stm = $_db->query($query);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        handle_db_error($e, "Failed to fetch multiple rows");
        return [];
    }
}

?>

<link rel="stylesheet" href="/css/dashboard.css">
<main class="dashboard-container">
    <h1 class="dashboard-title">Dashboard</h1>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card revenue">
            <div class="stat-content">
                <i class="fas fa-dollar-sign"></i>
                <h3>Total Revenue</h3>
                <p>RM <?= number_format($stats['total_revenue'], 2) ?></p>
            </div>
        </div>

        <div class="stat-card orders">
            <div class="stat-content">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Orders</h3>
                <p><?= $stats['total_orders'] ?></p>
            </div>
        </div>

        <div class="stat-card avg-order">
            <div class="stat-content">
                <i class="fas fa-chart-line"></i>
                <h3>Avg. Order Value</h3>
                <p>RM <?= number_format($stats['avg_order_value'], 2) ?></p>
            </div>
        </div>

        <div class="stat-card customers">
            <div class="stat-content">
                <i class="fas fa-users"></i>
                <h3>Total Customers</h3>
                <p><?= $stats['total_customers'] ?></p>
            </div>
        </div>
    </div>

    <!-- 1. Row: Sales Over Time Chart (full width) -->
    <div class="charts-row">
        <div class="chart-container-sales">
            <div class="chart-card-sales">
                <h3>Sales Over Time</h3>
                <canvas id="salesOverTimeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. Row: Order Status + Top Selling Products side-by-side -->
    <div class="charts-row">
        <div class="chart-container">
            <div class="chart-card">
                <h3 class = "dashboard-section-title">Order Status</h3>
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-card">
                <h3 class="dashboard-section-title">Top Selling Products</h3>
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. Row: Recent Orders and Top Products Tables -->
    <div class="tables-row">
        <div class="table-container">
            <div class="table-card">
                <h3>Recent Orders</h3>
                <!-- table content here -->
            </div>
        </div>


        <!-- Recent Orders and Top Products -->
        <div class="tables-row">
            <div class="table-container">
                <div class="table-card">
                    <h3>Recent Orders</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><?= $order['orderID'] ?></td>
                                        <td><?= $order['memberName'] ?></td>
                                        <td><?= date('d M Y', strtotime($order['orderDate'])) ?></td>
                                        <td>RM <?= number_format($order['totalAmount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-card">
                    <h3>Top Selling Products</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_products as $product): ?>
                                    <tr>
                                        <td><?= $product['productName'] ?></td>
                                        <td><?= $product['total_sold'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


</main>

<script>
    // Order Status Chart
    document.addEventListener('DOMContentLoaded', function() {
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Shipped', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [
                        <?= $order_status['pending_orders'] ?>,
                        <?= $order_status['shipped_orders'] ?>,
                        <?= $order_status['delivered_orders'] ?>,
                        <?= $order_status['cancelled_orders'] ?>
                    ],
                    backgroundColor: [
                        '#FFB6C1', // LightPink for Pending
                        '#B0E0E6', // PowderBlue for Shipped
                        '#98FB98', // PaleGreen for Delivered
                        '#FFDAB9' // PeachPuff for Cancelled
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Top Products Chart
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        const truncatedProductNames = <?= json_encode(array_map(function($product) {
            return strlen($product['productName']) > 15 ? substr($product['productName'], 0, 15) . '...' : $product['productName'];
        }, $top_products)) ?>;

        const topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: truncatedProductNames, // Using truncated names here
                datasets: [{
                    label: 'Units Sold',
                    data: <?= json_encode(array_column($top_products, 'total_sold')) ?>,
                    backgroundColor: '#e58f8f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>


<script>
    // Sales Over Time Chart
    const salesDates = <?= json_encode(array_column($sales_data, 'order_day')) ?>;
    const salesTotals = <?= json_encode(array_column($sales_data, 'total_sales')) ?>;

    const salesOverTimeCtx = document.getElementById('salesOverTimeChart').getContext('2d');
    const salesOverTimeChart = new Chart(salesOverTimeCtx, {
        type: 'line',
        data: {
            labels: salesDates,
            datasets: [{
                label: 'Total Sales (RM)',
                data: salesTotals,
                fill: false,
                borderColor: '#e58f8f',
                backgroundColor: '#e58f8f',
                tension: 0.4, // smooth line
                pointBackgroundColor: '#fff',
                pointBorderColor: '#e58f8f',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                },
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
</script>
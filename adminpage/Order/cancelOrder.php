<?php
require '../../_base.php';

$orderID = req('id');

// Check if the order can be cancelled 
$sql = "SELECT orderStatus FROM orders WHERE orderID = ?";
global $_db;
$stm = $_db->prepare($sql);
$stm->execute([$orderID]);
$order = $stm->fetch();

if (!$order) {
    redirect('orderTable.php');
}

if (in_array($order->orderStatus, ['Delivered', 'Cancelled'])) {
    redirect('orderTable.php');
}

// Cancel the order (already using db_execute — or you can expand it manually if you want)
$sql = "UPDATE orders SET orderStatus = 'Cancelled' WHERE orderID = ?";
$stm = $_db->prepare($sql);
$stm->execute([$orderID]);

redirect('orderTable.php');

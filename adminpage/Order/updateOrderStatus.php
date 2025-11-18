<?php
require '../../_base.php';

$orderID = req('id');
$newStatus = req('status');

// Validate status
$validStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
if (!in_array($newStatus, $validStatuses)) {
    redirect('orderTable.php');
}

// Manual update (no db_execute)
$sql = "UPDATE orders SET orderStatus = ? WHERE orderID = ?";
global $_db;
$stm = $_db->prepare($sql);
$stm->execute([$newStatus, $orderID]);

redirect('orderTable.php');

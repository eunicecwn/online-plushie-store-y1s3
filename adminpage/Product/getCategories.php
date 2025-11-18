<?php
require '../../_base.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
if (empty($type)) {
    echo json_encode([]);
    exit;
}

$stmt = $GLOBALS['_db']->prepare('SELECT categoryID, categoryName FROM category WHERE categoryType = ? AND status = "active"');
$stmt->execute([$type]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);
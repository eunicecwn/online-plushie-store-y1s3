<?php
require '../../_base.php';

header('Content-Type: application/json');

$term = req('term');
$categoryID = req('categoryID');

$stmt = $_db->prepare("
    SELECT p.productID, p.productName 
    FROM product p
    WHERE p.productName LIKE :term
    AND p.productID NOT IN (
        SELECT productID FROM product_category WHERE categoryID = :categoryID
    )
    ORDER BY p.productName
    LIMIT 20
");

$stmt->execute([
    ':term' => "%$term%",
    ':categoryID' => $categoryID
]);

$products = $stmt->fetchAll();

echo json_encode($products);
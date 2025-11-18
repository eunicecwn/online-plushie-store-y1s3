<?php
require_once '../../_base.php';

header('Content-Type: application/json');

// Get parameters
$imageID = (int)$_GET['id'];
$productID = $_GET['productID']; // Keep as string

try {
    // Verify product exists (updated for string IDs)
    $productStmt = $_db->prepare("SELECT 1 FROM product WHERE productID = ?");
    $productStmt->execute([$productID]);
    
    if (!$productStmt->fetch()) {
        throw new Exception("Product not found (ID: $productID)");
    }

    // Verify image belongs to product
    $imageStmt = $_db->prepare("SELECT imageName FROM gallery WHERE imageID = ? AND productID = ?");
    $imageStmt->execute([$imageID, $productID]);
    $image = $imageStmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        throw new Exception("Image not found for this product");
    }

    // Delete file and record
    $filePath = '../../productImage/' . $image['imageName'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $delStmt = $_db->prepare("DELETE FROM gallery WHERE imageID = ? AND productID = ?");
    $delStmt->execute([$imageID, $productID]);

    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'imageID' => $imageID,
            'productID' => $productID,
            'productIDType' => gettype($productID)
        ]
    ]);
}
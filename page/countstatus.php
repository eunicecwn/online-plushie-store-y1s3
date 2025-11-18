<?php
require '../_base.php';

$response = [
    'cart_count' => get_cart_count(),
    'wishlist_count' => get_wishlist_count()
];

header('Content-Type: application/json');
echo json_encode($response);

<?php
require '../_base.php';

$_title = 'KAWAII.Order Complete';
include '../_head.php';
?>

<div class="order-complete-container" style="max-width: 800px; margin: 50px auto; margin-top: 70px; background-color: #fff0f5; border-radius: 20px; padding: 40px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); text-align: center; font-family: 'Playpen Sans', serif;">
    <img src="/images/logo.png" alt="Logo" style="width: 300px; margin-bottom: 2px;">

    <h1 style="color: #f59ca4; font-size: 40px; margin-bottom: 10px;">Thank you for your purchase! 🎉</h1>
    <p style="font-size: 20px; color: #555;">Your order has been placed successfully.</p>
    <p style="font-size: 18px; color: #666; margin-top: 10px;">We will send you a confirmation email shortly with all the details.</p>

    <div style="margin: 30px 0;">
        <a href="orderhistory.php" style="background-color: #ffb6c1; color: white; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: bold;">View Order History</a>
    </div>

    <div>
        <a href="/page/category.php" style="color: #f59ca4; text-decoration: underline; font-weight: 600;">Continue Shopping</a>
    </div>
</div>
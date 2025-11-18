<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/images/logo1.png">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff&family=Playpen+Sans:wght@100..800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>

<?php
ob_start(); // Start output buffering
require_once __DIR__ . '/_base.php';

?>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <header>
        <div class="header-top">
            <a href="/index.php"><img src="/images/logo.png" class="logo"></a>

            <div class="search-container">
                <form action="/page/search.php" method="GET">
                    <input type="text" name="search" placeholder="Search..." required>
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <script>
                $(document).ready(function() {
                    // Prevent autofocusing on the search input field
                    $('input[name="search"]').blur();
                });
            </script>


            <nav>
                <a href="/" class="dropbtn">Home</a>

                <div class="dropdown">
                    <a href="/page/category.php" class="dropbtn">Category</a>
                    <div class="dropdown-content">
                        <a href="/page/category.php?categoryType=Animals">Animals</a>
                        <a href="/page/category.php?categoryType=Best for Gift">Best for Gifts</a>
                        <a href="/page/category.php?categoryType=Flowers">Flowers</a>
                        <a href="/page/category.php?categoryType=Food%20%26%20Drinks">Food & Drinks</a>
                        <a href="/page/category.php?categoryType=Ocean%20%26%20Sea%20Life">Ocean & Sea Life</a>
                    </div>
                </div>

                <a href="/page/brandprofile.php" class="dropbtn">Brand Profile</a>
                <a href="/page/location.php" class="dropbtn">Location</a>
            </nav>

            <div class="header-nav-icons">
                <ul>
                    <?php if (isset($_user) && $_user->role === ROLE_MEMBER): ?>
                        <li>
                            <!-- Logged-in member view -->
                            <div class="dropdown">
                                <a href="#" class="dropbtn">
                                    <?php if (isset($_user->profilePhoto) && $_user->profilePhoto): ?>
                                        <img src="/profile/<?= encode($_user->profilePhoto) ?>" alt="Profile Photo" class="profile-photo-icon">
                                    <?php else: ?>
                                        <div class="default-profile-icon">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="user-name"><?= encode($_user->name) ?></span>
                                </a>
                                <div class="dropdown-content">
                                    <a href="/page/myprofile.php"><i class="fas fa-user"></i> My Profile</a>
                                    <a href="/page/orderhistory.php"><i class="fa-solid fa-clock-rotate-left"></i>Order History</a>
                                    <a href="/page/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                                </div>
                            </div>

                        <li>
                            <a href="/page/wishlist.php" class="header-wishlist-button"><i class="fa-regular fa-heart"></i><span class="wishlist-count"><?= get_wishlist_count() ?></span></a>
                        </li>
                        <li>
                            <a href="/page/shoppingcart.php"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count"><?= get_cart_count() ?></span></a>
                        </li>

                    <?php else: ?>
                        <!-- Guest view -->
                        <li>
                            <a href="/page/login.php" class="user-icon-link">
                                <i class="fa-regular fa-circle-user"></i>
                            </a>
                        </li>

                        <li>
                            <a href="/page/login.php" class="header-wishlist-button"><i class="fa-regular fa-heart"></i><span class="wishlist-count"><?= get_wishlist_count() ?></span></a>
                        </li>
                        <li>
                            <a href="/page/login.php"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count"><?= get_cart_count() ?></span></a>
                        </li>
                    <?php endif; ?>

                    </li>
                </ul>
            </div>
        </div>
    </header>
</body>
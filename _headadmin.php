<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/logo1.png">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff&family=Playpen+Sans:wght@100..800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
    <!-- Add Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php
ob_start();
require_once __DIR__ . '/_base.php';
auth(ROLE_ADMIN, ROLE_STAFF);

?>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <header>
        <div class="header-top">
            <a href="/adminpage/dashboard.php"><img src="/images/LogoSellerCentre.png" class="logo"></a>

            <nav>
                <div class="dropdown">
                    <a class="dropbtn">Product</a>
                    <div class="dropdown-content">
                        <a href="/adminpage/Product/addProduct.php">Add Product</a>
                        <a href="/adminpage/Product/myProduct.php">My Product</a>
                    </div>
                </div>

                <div class="dropdown">
                    <a href="/adminpage/Order/orderTable.php" class="dropbtn">Order</a>
                </div>

                <div class="dropdown">
                    <a href="/adminpage/Member/memberTable.php" class="dropbtn">Member</a>
                </div>

                <div class="dropdown">
                    <a class="dropbtn">Voucher</a>
                    <div class="dropdown-content">
                        <a href="/adminpage/Voucher/addVoucher.php">Add Voucher</a>
                        <a href="/adminpage/Voucher/myVoucher.php">My Voucher</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a class="dropbtn">Category</a>
                    <div class="dropdown-content">
                        <a href="/adminpage/Category/addCategory.php">Add Category</a>
                        <a href="/adminpage/Category/myCategory.php">My Category</a>
                    </div>
                </div>

                <!-- Staff Management - Admin only -->
                <?php if ($_user->role === ROLE_ADMIN): ?>
                    <div class="dropdown">
                        <a href="#" class="dropbtn">Staff</a>
                        <div class="dropdown-content">
                            <a href="/adminpage/Staff/addStaff.php">Add Staff</a>
                            <a href="/adminpage/Staff/staffTable.php">View All Staff</a>
                        </div>
                    </div>
                <?php endif; ?>

            </nav>

            <div class="header-nav-icons">
                <ul>
                    <li>
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
                                <a href="/page/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                            </div>
                        </div>
                    </li>

                    <li>
                        <a href="/index.php" class="marketplace-button">Marketplace</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
<?php
// Secure database connection
require '../../_base.php';

// Initialize error messages
$_err = [];

// Handle form submission
if (is_post()) {
    $staffName = req('staffName');
    $staffEmail = req('staffEmail');
    $staffPassword = req('staffPassword');
    $confirmPassword = req('confirmPassword');
    $phoneNumber = req('phoneNumber');

    // Validation
    if ($staffName == '') {
        $_err['staffName'] = 'Required';
    } else if (strlen($staffName) > 255) {
        $_err['staffName'] = 'Maximum length is 255 characters';
    }

    if ($staffEmail == '') {
        $_err['staffEmail'] = 'Required';
    } else if (!filter_var($staffEmail, FILTER_VALIDATE_EMAIL)) {
        $_err['staffEmail'] = 'Invalid email format';
    } else if (is_exists($staffEmail, 'staff', 'staffEmail')) {
        $_err['staffEmail'] = 'Email already exists';
    }

    if ($staffPassword == '') {
        $_err['staffPassword'] = 'Required';
    } else if (strlen($staffPassword) < 8) {
        $_err['staffPassword'] = 'Minimum 8 characters required';
    }

    if ($confirmPassword == '') {
        $_err['confirmPassword'] = 'Required';
    } else if ($staffPassword !== $confirmPassword) {
        $_err['confirmPassword'] = 'Passwords do not match';
    }

    if ($phoneNumber == '') {
        $_err['phoneNumber'] = 'Required';
    } else if (!preg_match('/^[0-9]{10,20}$/', $phoneNumber)) {
        $_err['phoneNumber'] = 'Invalid phone number format';
    }

    // If no errors, insert into database
    if (empty($_err)) {
        try {
            // Hash the password before storing
            $hashedPassword = password_hash($staffPassword, PASSWORD_DEFAULT);

            $stmt = $_db->prepare("INSERT INTO staff 
                (staffName, staffEmail, staffPassword, phoneNumber, status) 
                VALUES (?, ?, ?, ?, 'active')");
            $stmt->execute([$staffName, $staffEmail, $hashedPassword, $phoneNumber]);

            temp('info', 'Staff Added Successfully');
            redirect("/adminpage/Staff/staffTable.php");
    
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$_title = 'KAWAII.SellerCentre | Add Staff';
include '../../_headadmin.php';
?>
<link rel="stylesheet" href="/css/admin.css">

<div class="form-container">
    <h1>Add New Staff</h1>
    <form action="" method="POST">

        <div class="form-group">
            <label for="staffName">Staff Name</label>
            <?= html_text('staffName', 'placeholder="Enter Staff Name" class="form-input" required') ?>
            <?= err('staffName') ?>
        </div>

        <div class="form-group">
            <label for="staffEmail">Email</label>
            <?= html_text('staffEmail', 'placeholder="Enter Email" class="form-input" type="email" required') ?>
            <?= err('staffEmail') ?>
        </div>

        <div class="form-group">
            <label for="staffPassword">Password</label>
            <input type="password" name="staffPassword" id="staffPassword" placeholder="Enter your password (min 8 characters)" 
                   minlength="8"  class="form-input" required>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" 
                   minlength="8"  class="form-input" required>
        </div>

        <div class="form-group">
            <label for="phoneNumber">Phone Number</label>
            <?= html_text('phoneNumber', 'placeholder="Enter Phone Number" class="form-input" required') ?>
            <?= err('phoneNumber') ?>
        </div>

        <button type="submit" class="form-button">Submit</button>
        <button type="reset" class="form-button">Reset</button>
        <a href="/adminpage/Staff/staffTable.php" class="form-button">Cancel</a>
    </form>
</div>
<?php
require '../_base.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $stmt = $_db->prepare("UPDATE member SET status = 'active' WHERE memberEmail = ? AND status = 'pending'");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<script>
            alert('Account activated successfully! You can now log in.');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "<script>
            alert('Invalid or already activated account.');
            window.location.href = 'login.php';
        </script>";
    }
} else {
    header('Location: login.php');
    exit;
}
?>

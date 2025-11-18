<?php
require '../_base.php';

// Handle logout request
if (isset($_POST['confirm_logout'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = array();
    session_destroy();

    temp('info', 'You have been logged out successfully');
    redirect('../index.php');
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playpen+Sans&display=swap" rel="stylesheet"> <!-- Added font import -->
</head>

<body>
    <div class="popup-overlay">
        <div class="popup-content">
            <i class="fa-regular fa-circle-user user-icon popup-icon"></i>
            <h2>Logout Confirmation</h2>
            <p>Are you sure you want to logout from your account?</p>

            <div class="popup-buttons">
                <!-- Confirm Logout Button -->
                <form method="POST">
                    <button type="submit" name="confirm_logout" class="form-button">Yes, Logout</button>
                </form>
                    <!-- Cancel Button -->
                    <button type="button" onclick="history.back()" class="cancel-form-button">Cancel</button>
            </div>
        </div>

        <script>
            // Prevent closing the popup by clicking outside
            document.querySelector('.popup-overlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    history.back();
                }
            });

            // Escape key closes the popup
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    history.back();
                }
            });
        </script>

</body>
</html>
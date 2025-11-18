<?php
require '../_base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if ($email) {
        $stmt = $_db->prepare("SELECT * FROM member WHERE memberEmail = ?");
        $stmt->execute([$email]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            // Generate reset token
            $token = sha1(uniqid() . rand());
            $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing reset tokens for this member
            $stmt = $_db->prepare("DELETE FROM token WHERE memberID = ? AND type = 'reset_password'");
            $stmt->execute([$member['memberID']]);
            
            // Insert new token
            $stmt = $_db->prepare("INSERT INTO token (id, expire, memberID, type) VALUES (?, ?, ?, 'reset_password')");
            $stmt->execute([$token, $expire, $member['memberID']]);

            // Email exists, send reset email
            $m = get_mail();
            try {
                $resetLink = "http://localhost:8000/page/resetPassword.php?token=" . urlencode($token);

                $m->addAddress($email, $member['memberName'] ?? 'Member');
                $m->Subject = "Reset Your Password 🎀";
                $m->isHTML(true);
                $m->Body = "
                    <h2>Reset Your Password</h2>
                    <p>Hi <strong>" . encode($member['memberName']) . "</strong>,</p>
                    <p>We received a request to reset your password.</p>
                    <p>Click the button below to reset it (link expires in 1 hour):</p>
                    <a href='$resetLink' style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #e58f8f;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                    '>Reset Password</a>
                    <p>If you did not request this, please ignore this email.</p>
                ";
                $m->send();
                echo "email_sent"; 
            } catch (Exception $e) {
                echo "email_failed"; 
            }
        } else {
            // Email not found
            echo "email_not_found"; 
        }
    } else {
        echo "email_invalid";
    }
} else {
    echo "invalid_request";
}
?>
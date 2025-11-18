<?php
require '../_base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['member_id'] ?? '';
    
    if ($memberID) {
        // Get member details
        $stmt = $_db->prepare("SELECT memberName, memberEmail FROM member WHERE memberID = ?");
        $stmt->execute([$memberID]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($member) {
            // Generate new OTP
            $otp = rand(100000, 999999);
            $expire = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Delete old OTP
            $stmt = $_db->prepare("DELETE FROM token WHERE memberID = ? AND type = 'account_activation'");
            $stmt->execute([$memberID]);

            // Store new OTP
            $stmt = $_db->prepare("INSERT INTO token (id, expire, memberID, type) VALUES (?, ?, ?, 'account_activation')");
            $stmt->execute([$otp, $expire, $memberID]);

            // Send new OTP email
            $m = get_mail();
            try {
                $m->addAddress($member['memberEmail'], $member['memberName']);
                $m->Subject = "New KAWAII Verification Code 🎀";
                $m->Body = "
                    <h2>New Verification Code</h2>
                    <p>Here's your new verification code:</p>
                    <div style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #e58f8f;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        font-size: 24px;
                        letter-spacing: 3px;
                    '>$otp</div>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you didn't request this code, please ignore this email.</p>
                    <p>To verify your account, please <a href='http://localhost:8000/page/verify.php?member_id=$memberID'>click here</a> and enter your code.</p>
                ";
                $m->isHTML(true);
                $m->send();
                
                echo "success";
             } catch (Exception $e) {
                    echo "Failed to send email. Error: " . $e->getMessage();
                
            }
        } else {
            echo "invalid_member";
        }
    } else {
        echo "invalid_request";
    }
} else {
    echo "invalid_method";
}
?>  